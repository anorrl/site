<?php

	namespace anorrl\utilities;

	use anorrl\utilities\ByteReader;

	class MeshConverter {

		/**
		 * Converts a newer roblox mesh to version 1.00
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
			// todo: rewrite this if possible.

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
					if ($newline == 0x0A | ($newline == 0x0D && $reader->Byte() == 0x0A)) { // "Bad newline"
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
								if ($headerSize >= 16) { // "Invalid header size"
									$vertexSize = $reader->Byte();
									$faceSize = $reader->Byte();
									$lodSize = $reader->UInt16LE();
									$lodCount = $reader->UInt16LE();
									$vertexCount = $reader->UInt32LE();
									$faceCount = $reader->UInt32LE();
								}
								break;
							case "4.":
								$headerSize = $reader->UInt16LE();
								if ($headerSize >= 24) { // "Invalid header size"
									$reader->Jump(2); // uint16 lodType;
									$vertexCount = $reader->UInt32LE();
									$faceCount = $reader->UInt32LE();
									$lodCount = $reader->UInt16LE();
									$boneCount = $reader->UInt16LE();
									$nameTableSize = $reader->UInt32LE();
									$subsetCount = $reader->UInt16LE();
									$reader->Jump(2); // byte numHighQualityLODs, unused;
									$vertexSize = 40;
								}
								break;
							case "5.":
								$headerSize = $reader->UInt16LE();
								if ($headerSize >= 32) { // "Invalid header size"
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
								}
								break;
						}
						$reader->SetIndex($begin + $headerSize);
						if ($vertexSize >= 36 && $faceSize >= 12 & $lodSize >= 4) { // "Invalid vertex size", "Invalid face size", "Invalid lod size"
							$fileEnd = $reader->GetIndex()
								+ ($vertexCount * $vertexSize)
								+ ($boneCount > 0 ? $vertexCount * 8 : 0)
								+ ($faceCount * $faceSize)
								+ ($lodCount * $lodSize)
								+ ($boneCount * 60)
								+ ($nameTableSize)
								+ ($subsetCount * 72)
								+ ($facsDataSize);
							if ($fileEnd == $reader->GetLength()) { // "Invalid file size"
								$faces = [];
								$vertices = [];
								$normals = [];
								$uvs = [];
								$tangents = [];
								$enableVertexColors = $vertexSize >= 40;
								$vertexColors = [];
								$lods = array(0, $faceCount);
								for($i = 0; $i < $vertexCount; $i++) { // Vertex[vertexCount]
									$vertices[$i * 3] = $reader->FloatLE();
									$vertices[$i * 3 + 1] = $reader->FloatLE();
									$vertices[$i * 3 + 2] = $reader->FloatLE();
									$normals[$i * 3] = $reader->FloatLE();
									$normals[$i * 3 + 1] = $reader->FloatLE();
									$normals[$i * 3 + 2] = $reader->FloatLE();
									$uvs[$i * 2] = $reader->FloatLE();
									$uvs[$i * 2 + 1] = 1 - $reader->FloatLE();
									$tangents[$i * 4] = $reader->Byte() / 127 - 1; // tangents are mapped from [0, 254] to [-1, 1]; byte tx, ty, tz, ts;
									$tangents[$i * 4 + 1] = $reader->Byte() / 127 - 1;
									$tangents[$i * 4 + 2] = $reader->Byte() / 127 - 1;
									$tangents[$i * 4 + 3] = $reader->Byte() / 127 - 1;
									if($enableVertexColors) {
										// byte r, g, b, a
										$vertexColors[$i * 4] = $reader->Byte();
										$vertexColors[$i * 4 + 1] = $reader->Byte();
										$vertexColors[$i * 4 + 2] = $reader->Byte();
										$vertexColors[$i * 4 + 3] = $reader->Byte();
										$reader->Jump($vertexSize - 40);
									} else {
										$reader->Jump($vertexSize - 36);
									}
								}
								if($boneCount > 0) { // Envelope[vertexCount]
									$reader->Jump($vertexCount*8);
								}
								for($i = 0; $i < $faceCount; $i++) { // Face[faceCount]
									$faces[$i * 3] = $reader->UInt32LE();
									$faces[$i * 3 + 1] = $reader->UInt32LE();
									$faces[$i * 3 + 2] = $reader->UInt32LE();

									$reader->Jump($faceSize - 12);
								}
								if($lodCount <= 2) { // LodLevel[lodCount]; Lod levels are pretty much ignored if lodCount is not
									$reader->Jump($lodCount * $lodSize); // at least 3, so we can just skip reading them completely.
								} else {
									$lods = [];
									for($i = 0; $i < $lodCount; $i++) {
										$lods[$i] = $reader->UInt32LE();
										$reader->Jump($lodSize - 4);
									}
								}
								if($boneCount > 0) { // Bone[boneCount]
									$reader->Jump($boneCount * 60);
								}
								if($nameTableSize > 0) { // byte[nameTableSize]
									$reader->Jump($nameTableSize);
								}
								if($subsetCount > 0) { // MeshSubset[subsetCount]
									$reader->Jump($subsetCount*72); // subsetCount * (UInt32 * 5 + UInt16 * 26)
								}
								if($facsDataSize > 0) {
									$reader->Jump($facsDataSize);
								}
								// Convertion to mesh v1.00 (this code is old and nasty)
								$facesLength = ($lods[1] * 3) - ($lods[0] * 3);
								$actualfaces = array_slice($faces, $lods[0] * 3, $lods[1] * 3);
								$data = "version 1.00\n" . ($facesLength / 3) . "\n";
								function s($Float) { // Convert float to string
									if ($Float==null) { $Float=0; }
									$FloatScientificNotation = sprintf("%.5e", $Float);
									$FloatCleaned = str_replace("e+0", "", str_replace("e-0", "", $FloatScientificNotation));
									$sub1 = substr($FloatScientificNotation, 7, 3);
									$sub2 = substr($FloatScientificNotation, 8, 3);
									if ($sub1=="e+0" | $sub1=="e-0" | $sub1=="e-1" | $sub1=="e-2" | $sub2=="e+0" | $sub2=="e-0" | $sub2=="e-1" | $sub2=="e-2") {
										$FloatCleaned = sprintf("%g", $FloatCleaned);
									}
									return $FloatCleaned;
								}
								function addFaceToData($index, $vertices, $normals, $uvs) {
									$indexVertex = $index*3;
									$indexUV = $index*2;
											
									$data = "[" . s((float)($vertices[$indexVertex]/0.5)) . "," . s((float)($vertices[$indexVertex+1]/0.5)) . "," . s((float)($vertices[$indexVertex+2]/0.5)) . "]"; // vertex
									$data = $data . "[" . s($normals[$indexVertex]) . "," . s($normals[$indexVertex+1]) . "," . s($normals[$indexVertex+2]) . "]"; // normals
									$data = $data . "[" . s($uvs[$indexUV]) . "," . s($uvs[$indexUV+1]) . ",0]"; // uvs
									return $data;
								}
								for($i = 0; $i < $facesLength; $i += 3) {
									$data = $data . addFaceToData($actualfaces[$i], $vertices, $normals, $uvs);
									$data = $data . addFaceToData($actualfaces[$i + 1], $vertices, $normals, $uvs);
									$data = $data . addFaceToData($actualfaces[$i + 2], $vertices, $normals, $uvs);
								}

								return ["error" => false, "mesh" => $data];
							}
						}
					}
					break;
				default:
					return ["error" => true, "reason" => "Invalid mesh version found. [ $version ]"];
			}

			return ["error" => true, "reason" => "Mesh failed to convert I guess."];
			
		}
	}
?>