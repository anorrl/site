<?php

	namespace anorrl\utilities;

	use anorrl\utilities\ByteReader;
	use anorrl\utilities\ByteWriter;

	class MeshConverter {


		public static function ConvertFromOBJ(string $contents): array {

			if(
				str_contains("v ", $contents) && 
				str_contains("vn ", $contents) && 
				str_contains("vt ", $contents)
			) {
				// could be a valid obj
			}

			return ["error" => true, "reason" => "Unimplemented"];
		}

		/**
		 * Converts a newer roblox mesh to version 2.00
		 * 
		 * Supports:
		 * - v3.00
		 * - v3.01
		 * - v4.00
		 * - v4.01
		 * - v5.00
		 * 
		 * @param string $contents
		 * @return array{error: bool, mesh: string|array{error: bool, reason: string}}
		 */
		public static function Convert(string $contents): array {
			$reader = new ByteReader();
			$reader->buffer = $contents;

			$is_a_mesh = ($reader->String(8)) == "version ";

			if(!$is_a_mesh)
				return ["error" => true, "reason" => "Invalid mesh file!"];

			$version = ($reader->String(4));
			switch($version) {
				case "3.00":
				case "3.01":
				case "4.00":
				case "4.01":
				case "5.00":
					$newline = $reader->Byte();
					if (!($newline == 0x0A | ($newline == 0x0D && $reader->Byte() == 0x0A))) return ["error" => true, "reason" => "Bad newline"];

					$begin = $reader->GetIndex();
					$headerSize = 0;
					$vertexSize = 0;
					$faceSize = 12;
					$lodSize = 4;
					$nameTableSize = 0;
					$facsDataSize = 0;
					$lodCount = 0;
					$vertexCount = 0;
					$faceCount = 0;
					$boneCount = 0;
					$subsetCount = 0;

					switch (substr($version, 0, 2)) {
						case "3.":
							$headerSize = $reader->UInt16LE();
							if ($headerSize < 16) return ["error" => true, "reason" => "Invalid header size"];
							$vertexSize = $reader->Byte();
							$faceSize = $reader->Byte();
							$lodSize = $reader->UInt16LE();
							$lodCount = $reader->UInt16LE();
							$vertexCount = $reader->UInt32LE();
							$faceCount = $reader->UInt32LE();
							break;
						case "4.":
							$headerSize = $reader->UInt16LE();
							if ($headerSize < 24) return ["error" => true, "reason" => "Invalid header size"];
							$reader->Jump(2); // uint16 lodType;
							$vertexCount = $reader->UInt32LE();
							$faceCount = $reader->UInt32LE();
							$lodCount = $reader->UInt16LE();
							$boneCount = $reader->UInt16LE();
							$nameTableSize = $reader->UInt32LE();
							$subsetCount = $reader->UInt16LE();
							$reader->Jump(2); // byte numHighQualityLODs, unused;
							$vertexSize = 40;
							break;
						case "5.":
							$headerSize = $reader->UInt16LE();
							if ($headerSize < 32) return ["error" => true, "reason" => "Invalid header size"];
							$reader->Jump(2); // uint16 meshCount;
							$vertexCount = $reader->UInt32LE();
							$faceCount = $reader->UInt32LE();
							$lodCount = $reader->UInt16LE();
							$boneCount = $reader->UInt16LE();
							$nameTableSize = $reader->UInt32LE();
							$subsetCount = $reader->UInt16LE();
							$reader->Jump(2); // byte numHighQualityLODs, unused;
							$reader->Jump(4); // uint32 facsDataFormat;
							$facsDataSize = $reader->UInt32LE();
							$vertexSize = 40;
							break;
					}
					$reader->SetIndex($begin + $headerSize);

					if ($vertexSize < 36) return ["error" => true, "reason" => "Invalid vertex size"]; // This triggers for version 2.00 pre-PBR (2016), so BTRoblox's implementation is wrong.
					if ($faceSize < 12) return ["error" => true, "reason" => "Invalid face size"];
					if ($lodSize < 4) return ["error" => true, "reason" => "Invalid lod size"];

					$fileEnd = $reader->GetIndex()
						+ ($vertexCount * $vertexSize)
						+ ($boneCount > 0 ? $vertexCount * 8 : 0)
						+ ($faceCount * $faceSize)
						+ ($lodCount * $lodSize)
						+ ($boneCount * 60)
						+ ($nameTableSize)
						+ ($subsetCount * 72)
						+ ($facsDataSize);

					if ($fileEnd != $reader->GetLength()) return ["error" => true, "reason" => "Invalid file size"];

					$faces = [];
					$vertices = [];
					$normals = [];
					$uvs = [];
					$lods = array(0, $faceCount);

					for($i = 0; $i < $vertexCount; $i++) { // Verts[vertexCount]
						$vertices[$i * 3] = $reader->FloatLE();
						$vertices[$i * 3 + 1] = $reader->FloatLE();
						$vertices[$i * 3 + 2] = $reader->FloatLE();
						$normals[$i * 3] = $reader->FloatLE();
						$normals[$i * 3 + 1] = $reader->FloatLE();
						$normals[$i * 3 + 2] = $reader->FloatLE();
						$uvs[$i * 2] = $reader->FloatLE();
						$uvs[$i * 2 + 1] = 1 - $reader->FloatLE();
						$reader->Jump($vertexSize - 4*8);
					}

					if($boneCount > 0) { // Envelope[vertexCount]
						$reader->Jump($vertexCount*8);
					}

					for($i = 0; $i < $faceCount; $i++) { // Faces[faceCount]
						$faces[$i * 3] = $reader->UInt32LE();
						$faces[$i * 3 + 1] = $reader->UInt32LE();
						$faces[$i * 3 + 2] = $reader->UInt32LE();

						$reader->Jump($faceSize - 12);
					}

					if($lodCount > 2) { // LodLevel[lodCount]; Lod levels are pretty much ignored if lodCount is not
						$lods = []; // at least 3, so we can just skip reading them completely.
						for($i = 0; $i < $lodCount; $i++) {
							$lods[$i] = $reader->UInt32LE();
							$reader->Jump($lodSize - 4);
						}
					}

					$facesLength = ($lods[1] * 3) - ($lods[0] * 3);
					$actualFaces = array_slice($faces, $lods[0] * 3, $lods[1] * 3);

					// Encode + optimize vertex and face data (trust me, this DOES make a difference)
					$vertexArray = [];
					$faceArray = [];

					for($faceIndex = 0; $faceIndex < $facesLength; $faceIndex) {
						$oldVertexIndex = $actualFaces[$faceIndex];

						$writer = new ByteWriter();
						$writer->FloatLE($vertices[$oldVertexIndex * 3]);
						$writer->FloatLE($vertices[$oldVertexIndex * 3 + 1]);
						$writer->FloatLE($vertices[$oldVertexIndex * 3 + 2]);
						$writer->FloatLE($normals[$oldVertexIndex * 3]);
						$writer->FloatLE($normals[$oldVertexIndex * 3 + 1]);
						$writer->FloatLE($normals[$oldVertexIndex * 3 + 2]);
						$writer->FloatLE($uvs[$oldVertexIndex * 2]);
						$writer->FloatLE(1 - $uvs[$oldVertexIndex * 2 + 1]);
						$writer->FloatLE(0);

						$newVertexIndex = array_search($writer->buffer, $vertexArray);

						if ($newVertexIndex == false) {
							$newVertexIndex = array_push($vertexArray, $writer->buffer) - 1;
						}

						array_push($faceArray, pack("V", $newVertexIndex));

						$faceIndex++;
					}

					$writer = new ByteWriter();
					$writer->String("version 2.00\n");
					// FileMeshHeaderV2
					$writer->UInt16LE(2+1*2+4*2);             // ushort sizeof_FileMeshHeaderV2
					$writer->Byte(4*9);                       // byte sizeof_FileMeshVertex
					$writer->Byte(4*3);                       // byte sizeof_FileMeshFace
					$writer->UInt32LE(count($vertexArray));   // uint numVerts
					$writer->UInt32LE(count($faceArray) / 3); // uint numFaces
					// Verts
					$writer->String(implode($vertexArray));
					// Faces
					$writer->String(implode($faceArray));

					return ["error" => false, "mesh" => $writer->buffer];
				default:
					return ["error" => true, "reason" => "Invalid mesh version found. [ $version ]"];
			}

			return ["error" => true, "reason" => "Mesh failed to convert I guess."];
			
		}
	}
?>