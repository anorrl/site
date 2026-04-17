<?php
	namespace anorrl\utilities;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\User;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\utilities\ImageUtils;

	define("INVALIDFILE", ["error" => true, "reason" => "File format not valid!"]);
	define("INTERNALERROR", ["error" => true, "reason" => "Something went wrong idfk bitch about it to grace... (FILE UPLOAD ERROR)"]);
	define("INTERNALSQLERROR", ["error" => true, "reason" => "Something went wrong idfk bitch about it to grace... (SQL ERROR!)"]);

	class AssetUploader {
		
		/**
		 * This calculates if the user is able to upload an asset at this current time.
		 * This is to prevent spam.
		 * @param User $user User to check availability on.
		 * @return string|null null if nothing is wrong, string for a generated "Please wait [X] seconds" message
		 */
		private static function CanUpload(User $user): string|null {
			$timer = 61;
			if($user->getLatestAssetUploaded() != null) {
				$difference = (time()-($user->getLatestAssetUploaded()->created_at->getTimestamp()-3600));

				$timer = $difference;
			}

			if($timer > 60) {
				return null;
			}
			
			$timercalc = 60 - $timer;
			return "You're uploading way too fast! Wait $timercalc more seconds before uploading!";
		}

		/**
		 * This checks if the RBX data given is a valid loadable roblox file. (Not entirely accurate)
		 * @param string $data Data to parse through
		 * @param bool $legacy If true it will try to load as an XML.
		 * @return bool returns true if data is valid.
		 */
		private static function IsValidXML(string $data, bool $legacy = false): bool {
			$trimmed_data = trim($data);

			if(!str_starts_with($trimmed_data, "<roblox") && !str_ends_with($trimmed_data, "</roblox>")) {
				return false;
			}

			if($legacy) {
				libxml_use_internal_errors(true);
				$sxe = simplexml_load_string($trimmed_data);
				if (!$sxe) {
					return false;
				}
			}
			
			return true;
		}

		private static function IsValidMesh(string $data): bool {
			return 
				str_starts_with(trim($data), "version 1.0") || 
				str_starts_with(trim($data), "version 2.0") ||
				str_starts_with(trim($data), "version 3.0") ||
				str_starts_with(trim($data), "version 4.0") ||
				str_starts_with(trim($data), "version 5.0");
		}

		private static function IsSupportedMesh(string $data): bool {
			return 
				str_starts_with(trim($data), "version 1.0") || 
				str_starts_with(trim($data), "version 2.0");
		}

		private static function GetMD5OfData(mixed $data) {
			return md5($data);
		}

		private static function GetRender(int $id, AssetType $type): string|null {
			return match($type) {
				AssetType::SHIRT => Renderer::RenderClothing($id),
				AssetType::PANTS => Renderer::RenderClothing($id),

				AssetType::HEAD => Renderer::RenderClothing($id),
				AssetType::TORSO => Renderer::RenderClothing($id),
				AssetType::LEFTARM => Renderer::RenderClothing($id),
				AssetType::RIGHTARM => Renderer::RenderClothing($id),
				AssetType::LEFTLEG => Renderer::RenderClothing($id),
				AssetType::RIGHTLEG => Renderer::RenderClothing($id),

				AssetType::PLACE => Renderer::RenderPlace($id),
				AssetType::MESH => Renderer::RenderMesh($id),
				AssetType::MODEL => Renderer::RenderModel($id),
				default => null
			};
			 
		}

		private static function ExecuteRender(int $id, AssetType $type, string $data) {
			$directory = $_SERVER['DOCUMENT_ROOT'];
			$md5hashfile = self::GetMD5OfData($data);
			$assetsdir = "$directory/../assets/thumbs/$md5hashfile";
			if(!file_exists($assetsdir)) {
				$render = self::GetRender($id, $type);
				if($render != null) {
					$data = "data:image/png;base64,$render";
					list($type, $data) = explode(';', $data);
					list(, $data)      = explode(',', $data);
					$data = base64_decode($data);

					$render_image = imagecreatefromstring($data);
					imagesavealpha($render_image, true);
					imagepng($render_image, $assetsdir);
				}
			}
		}

		private static function PushWebhook(Asset $asset) {
			if(strlen(trim(\CONFIG->asset->webhook)) == 0 || !str_starts_with(\CONFIG->asset->webhook, "https://discord.com/api/webhooks/")) {
				return;
			}
			$webhook_url = trim(\CONFIG->asset->webhook);
			$domain = \CONFIG->domain;
			
			$msg = [
				"username" => "Catalog Hotline",
				"content" => "New Catalog Item Dropped!",
				"embeds" => [
					[
						"title" => $asset->name,
						"description" => "Uploaded by: ".$asset->creator->name,
						"url" => "https://$domain".$asset->getUrl(),
						"author" => [
							"name" => "ANORRL",
							"url" => "https://$domain/",
							"icon_url" => "https://$domain/images/download/2016client.png"
						],
						"thumbnail" => [
							"url" => "https://$domain{$asset->getThumbsUrl()}"
						],
					]
				]
			];

			$headers = ['Content-Type: application/json'];

			$ch = curl_init($webhook_url);
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $msg ) );
			curl_exec($ch);
			curl_close($ch);
		}

		private static function CommitAsset(
			string|null $data,
			AssetType $type,
			string $name,
			string $description = "",
			bool $public = true,
			bool $on_sale = true,
			bool $comments_enabled = true,
			User $user
		): array {
			$db = Database::singleton();

			$hidden = AssetTypeUtils::IsHidden($type);

			$db->run(
				"INSERT INTO `assets`
					(`name`, `description`, `creator`, `type`, `public`, `onsale`, `comments_enabled`, `nevershow`) 
					VALUES (:name, :desc, :uid, :type, :public, :onsale, :commentsenabled, :hidden);",
				[
					":name" => $name,
					":desc" => $description,
					":uid" => $user->id,
					":type" => $type->ordinal(),
					":public" => intval($public),
					":onsale" => intval($on_sale),
					":commentsenabled" => intval($comments_enabled),
					":hidden" => intval($hidden)
				]
			);

			$id = (int)$db->lastInsertId();

			if($data != null) {
				$md5 = self::GetMD5OfData($data);

				$directory = $_SERVER['DOCUMENT_ROOT'];
				$assetsdir = "$directory/../assets/";
				$filepath = $assetsdir.$md5;
				if(!file_exists($filepath) || (file_exists($filepath) && filesize($filepath) != strlen($data))) {
					file_put_contents($filepath, $data);
				}

				$exec = $db->run(
					'INSERT INTO `asset_versions`(`assetid`, `md5sig`, `md5thumb`) VALUES (:aid, :md5, :md5)',
					[
						":aid" => $id,
						":md5" => $md5
					]
				);
				$error = $exec->errorCode();
				if($error && $error != "00000") {
					if(filesize($filepath) == 0)
						unlink($filepath);

					$db->run(
						'DELETE FROM `assets` WHERE `id` = :aid',
						[":aid" => $id]
					);

					return ["error" => true, "reason" => "Something went wrong idfk bitch about it to grace... (SQLERROR$error)"];
				}
			}

			$asset = Asset::FromID($id);

			TransactionUtils::CommitTransaction($user, $asset);

			if($public && $on_sale && !$hidden) {
				
				self::PushWebhook($asset);
			}
			
			return ["error" => false, "id" => $id];
		}

		public static function EditAsset(
			Asset $asset,
			string $name,
			string $description,
			bool $public = true,
			bool $on_sale = true,
			bool $comments_enabled = true,
			User|null $user = null
		): array {

			if($user == null) {
				$user = UserUtils::RetrieveUser();
			}

			if($user != null && !$user->isBanned()) {
				return self::CommitUpdateAsset($asset, null, $name, $description, $public, $on_sale, $comments_enabled, $user);
			}

			return ["error" => true, "reason" => "User is not authorised to perform this action!"];
		}

		private static function CommitUpdateAsset(
			Asset $asset,
			string|null $data,
			string $name,
			string $description = "",
			bool $public = true,
			bool $on_sale = true,
			bool $comments_enabled = true,
			User $user
		): array {
			if($user->id != $asset->creator->id && !$user->isAdmin()) {
				return ["error" => true, "reason" => "User is not authorised to perform this action!"];
			}
			$db = Database::singleton();

			$id = $asset->id;
			$parsed_public          = intval($public);
			$parsed_onsale          = intval($on_sale);
			$parsed_commentsenabled = intval($comments_enabled);

			$new_versionid = $asset->current_version;

			if($data != null) {
				$md5 = self::GetMD5OfData($data);

				if($md5 == $asset->getLatestVersionDetails()->md5sig) { 
					return ["error" => true, "reason" => "I'm pretty sure you've already uploaded this?"];
				}

				$new_versionid = count($asset->getAllVersions())+1;

				$db->run(
					'INSERT INTO `asset_versions`(`assetid`, `md5sig`, `md5thumb`, `subid`) VALUES (:assetid, :md5, :md5, :subid)',
					[
						":assetid" => $id,
						":md5" => $md5,
						":subid" => $new_versionid
					]
				);

				$directory = $_SERVER['DOCUMENT_ROOT'];
				$assetsdir = "$directory/../assets/";
				$filepath = $assetsdir.$md5;
				if(!file_exists($filepath) || (file_exists($filepath) && filesize($filepath) != strlen($data))) {
					file_put_contents($filepath, $data);
				}
			}
			
			$versionid = $db->lastInsertId();
			
			$db->run(
				"UPDATE `assets` SET `currentversion` = :curver, `lastedited` = now(), `name` = :name, `description` = :desc, `public` = :public, `onsale` = :onsale, `comments_enabled` = :commentsenabled WHERE `id` = :assetid",
				[
					":curver" => $new_versionid,
					":name" => $name,
					":desc" => $description,
					":public" => $parsed_public,
					":onsale" => $parsed_onsale,
					":commentsenabled" => $parsed_commentsenabled,
					":assetid" => $id,
				]
			);
		
			return ["error" => false, "versionid" => $versionid];
		}

		public static function UpdateAsset(
			Asset $asset,
			array|string $file,
			User|null $user = null
		): array {
			if($user == null) {
				$user = UserUtils::RetrieveUser();
			}

			if($user != null && !$user->isBanned() && ($asset->creator->id == $user->id || $user->isAdmin())) {
				$name = $asset->name;
				$description = $asset->description;
				$public = $asset->public;
				$on_sale = $asset->onsale;
				$comments_enabled = $asset->comments_enabled;
				
				if($asset->type == AssetType::IMAGE && $asset->type == AssetType::LUA) {
					if(!$user->isAdmin()) {
						return ['error' => true, 'reason' => "You are not authorised to perform this action!"];
					}
				}

				if(is_array($file)) {
					if($file['error'] != 0) {
						return INTERNALERROR;
					}

					if($file['size'] > 26214400) {
						return ["error" => true, "reason" => "File was too large! Only 25MB maximum is allowed!"];
					}

					if($file['size'] <= 0) {
						return ["error" => true, "reason" => "File was empty! Hello???"];
					}

					$data = file_get_contents($file['tmp_name']);
				} else {
					$filesize = strlen($file);
					$data = $file;

					if($filesize > 26214400) {
						return ["error" => true, "reason" => "File was too large! Only 25MB maximum is allowed!"];
					}

					if($filesize <= 0) {
						return ["error" => true, "reason" => "File was empty! Hello???"];
					}
				}

				$name = trim($name);

				$type = $asset->type;

				if(strlen($name) <= 0) {
					return ["error" => true, "reason" => "You need to enter a name doofus!"];
				}

				if(strlen($name) > 128) {
					$name = substr($name, 0, 128);
				}

				if(AssetTypeUtils::IsRBX($type)) {
					if(!self::IsValidXML($data)) {
						return INVALIDFILE;
					}

					$result = self::CommitUpdateAsset($asset, $data, $name, $description, $public, $on_sale, $comments_enabled, $user);
					
					if(!$result['error']) {
						self::ExecuteRender($asset->id, $type, $data);
					}

					return $result;
				} else if($asset->type == AssetType::MESH) {
					if(!self::IsValidMesh($data)) {
						return INVALIDFILE;
					}

					if(!self::IsSupportedMesh($data)) {
						$mesh_result = MeshConverter::Convert($data);

						if(!$mesh_result['error'])
							$data = $mesh_result['mesh'];
						else
							return $mesh_result;
					}
					

					$result = self::CommitUpdateAsset($asset, $data, $name, $description, $public, $on_sale, $comments_enabled, $user);

					if(!$result['error']) {
						if(AssetTypeUtils::IsRenderable($type)) {
							self::ExecuteRender($asset->id, $type, $data);
						}
					}

					return $result;

				} else if($asset->type == AssetType::LUA) {
					return self::CommitUpdateAsset($asset, $data, $name, $description, $public, $on_sale, $comments_enabled, $user);

				} else {
					return ["error" => true, "reason" => "Invalid asset type found!"];
				}
			}

			return ["error" => true, "reason" => "User is not authorised to perform this action!"];
		}

		public static function CreatePlace(
			string $name,
			string $description,
			bool $public,
			bool $comments_enabled,
			int $server_size = 12,
			bool $copylocked = true,
			bool $gears_enabled = false,
			bool $original = false,
			User|null $user = null
		): array {
			$result = self::UploadAsset(null, AssetType::PLACE, $name, $description, $public, false, $comments_enabled, $user);

			if(!$result['error']) {
				include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
				$stmt_addplace = $con->prepare("INSERT INTO `places`(`id`, `copylocked`, `serversize`, `gears_enabled`, `original`) VALUES (?, ?, ?, ?, ?)");
				
				$place_copylocked = $copylocked ? 1 : 0;
				$place_gears = $gears_enabled ? 1 : 0;
				$place_original = $original ? 1 : 0;
				$stmt_addplace->bind_param('iiiii', $result['id'], $place_copylocked, $server_size, $place_gears, $place_original);
				if(!$stmt_addplace->execute()) {
					$stmt = $con->prepare('DELETE FROM `assets` WHERE `id` = ?;');
					$stmt->bind_param('i', $result['id']);
					$stmt->execute();

					return INTERNALSQLERROR;
				}
			}

			return $result;
		}

		public static function UploadAsset(
			array|null $file,
			AssetType $type,
			string $name,
			string $description = "",
			bool $public = true,
			bool $on_sale = true,
			bool $comments_enabled = true,
			User|null $user = null
		): array {
			if($user == null) {
				$user = UserUtils::RetrieveUser();
			}

			if($file == null && $type != AssetType::PLACE && $type != AssetType::PACKAGE) {
				return ["error" => true, "reason" => "Invalid action!"];
			}

			if($user != null && !$user->isBanned()) {

				if($type == AssetType::IMAGE && $type == AssetType::LUA) {
					if(!$user->isAdmin()) {
						return ['error' => true, 'reason' => "You are not authorised to perform this action!"];
					}
				}

				$data = null;

				if(is_array($file)) {
					if($file['error'] != 0) {
						return INTERNALERROR;
					}

					if($file['size'] > 26214400) {
						return ["error" => true, "reason" => "File was too large! Only 25MB maximum is allowed!"];
					}

					if($file['size'] <= 0) {
						return ["error" => true, "reason" => "File was empty! Hello???"];
					}
					
					$data = file_get_contents($file['tmp_name']);
				}
				
				
				$canupload = self::CanUpload($user);

				if(is_null($canupload)) {

					$name = trim($name);

					if(strlen($name) <= 0) {
						return ["error" => true, "reason" => "You need to enter a name doofus!"];
					}

					if(strlen($name) > 128) {
						$name = substr($name, 0, 128);
					}

					if($data != null) {
						if(AssetTypeUtils::IsRBX($type)) {
							if(!self::IsValidXML($data)) {
								return INVALIDFILE;
							}

							$result = self::CommitAsset($data, $type, $name, $description, $public, $on_sale, $comments_enabled, $user);
							
							if(!$result['error']) {
								self::ExecuteRender($result['id'], $type, $data);
							}

							return $result;
						} else {
							if(AssetTypeUtils::IsImage($type)) {
								if(!str_starts_with(ImageUtils::checkMimeType($data),"image/")) {
									return INVALIDFILE; 
								}
								if($type == AssetType::DECAL || $type == AssetType::FACE) {
									$original_image = imagecreatefromstring($data);
									if(is_bool($original_image) && !$original_image) {
										return INVALIDFILE; 
									}
									$width = imagesx($original_image);
									$height = imagesy($original_image);

									imagesavealpha($original_image, true);

									$image = imagecreatetruecolor($width, $height);
									$bga = imagecolorallocatealpha($image, 0, 0, 0, 127);
									imagefill($image, 0, 0, $bga);
									imagecopy($image, $original_image, 0, 0, 0, 0, $width, $height);
									imagesavealpha($image, true);

									if($width > $height) {
										$new_width = 420;
										$new_height = -1;
									} else if($width < $height) {
										$new_width = -1;
										$new_height = 420;
									} else {
										$new_width = 420;
										$new_height = 420;
									}

									$resultimage = imagescale($image, $new_width, $new_height);
									imagesavealpha($resultimage, true);

									ob_start();
									imagepng($resultimage);
									$data = ob_get_contents();
									ob_end_clean();
								} else if($type == AssetType::SHIRT || $type == AssetType::PANTS) {
									$original_image = imagecreatefromstring($data);
									if(is_bool($original_image) && !$original_image) {
										return INVALIDFILE; 
									}
									imagesavealpha($original_image, true);
									$width = imagesx($original_image);
									$height = imagesy($original_image);

									if($width != 585 || $height != 559) {
										return ["error" => true, "reason" => "Image size was not correct! Did you mean to upload a t-shirt or decal? Expected: 585 x 559."];
									}

									ob_start();
									imagepng($original_image);
									$data = ob_get_contents();
									ob_end_clean();
								} else if($type == AssetType::TSHIRT) {
									$original_image = imagecreatefromstring($data);
									if(is_bool($original_image) && !$original_image) {
										return INVALIDFILE; 
									}
									imagesavealpha($original_image, true);
									
									$width = imagesx($original_image);
									$height = imagesy($original_image);

									$image = imagecreatetruecolor($width, $height);
									$bga = imagecolorallocatealpha($image, 0, 0, 0, 127);
									imagefill($image, 0, 0, $bga);
									imagecopy($image, $original_image, 0, 0, 0, 0, $width, $height);
									imagesavealpha($image, true);

									if($width > $height) {
										$new_width = 420;
										$new_height = -1;
									} else if($width < $height) {
										$new_width = -1;
										$new_height = 420;
									} else {
										$new_width = 420;
										$new_height = 420;
									}
									
									// calculate resized image
									$r_image = imagescale($image, $new_width, $new_height);
									// get size parameters of scaled image as for easier copying
									$r_width  = imagesx($r_image);
									$r_height = imagesy($r_image);
									
									// if the height is taller than the width then attempt to center it
									if($r_width < $r_height) {
										$dst_x = (420 - $r_width)/2;
									} else {
										$dst_x = 0;
									}
									
									$resizedimage = imagecreatetruecolor(420, 420);
									$trans_colour = imagecolorallocatealpha($resizedimage, 0, 0, 0, 127);
									imagefill($resizedimage, 0, 0, $trans_colour);
									imagecopyresampled($resizedimage, $image, $dst_x, 0, 0, 0, $r_width, $r_height, $width, $height);
									
									
									// create tshirt THUMBNAIL image
									// create base image of size 420x420 with transparent background
									$tshirt = imagecreatetruecolor(420, 420);
									$trans_colour = imagecolorallocatealpha($tshirt, 0, 0, 0, 127);
									imagefill($tshirt, 0, 0, $trans_colour);
									
									// paste tshirt (the icon thing) into image
									$bg_tshirt = imagecreatefrompng($_SERVER['DOCUMENT_ROOT']."/public/images/tshirt.png");
									imagecopy($tshirt, $bg_tshirt, 0, 0, 0, 0, 420, 420);
									// and paste the processed resizedimage on top of it
									imagecopyresampled($tshirt, $resizedimage, 84, 84, 0, 0, 252, 252, 420, 420);
									
									imagesavealpha($resizedimage, true);

									ob_start();
									imagepng($resizedimage);
									$data = ob_get_contents();
									ob_end_clean();
								}



								$result = self::CommitAsset($data, AssetType::IMAGE, $name, "", false, false, $comments_enabled, $user);
								if($result["error"]) {
									return $result;
								}
								
								if($type != AssetType::IMAGE) {
									$image_id = $result['id'];

									$data = match($type) {
										AssetType::DECAL => AssetTypeUtils::GenerateDecalRBXM($image_id),
										AssetType::FACE => AssetTypeUtils::GenerateFaceRBXM($image_id),
										AssetType::SHIRT => AssetTypeUtils::GenerateShirtRBXM($image_id),
										AssetType::PANTS => AssetTypeUtils::GeneratePantsRBXM($image_id),
										AssetType::TSHIRT => AssetTypeUtils::GenerateTShirtRBXM($image_id),
									};

									$data = AssetTypeUtils::Replace("name", str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("\"", "&quot;", $name))), $data);

									$result = self::CommitAsset($data, $type, $name, $description, $public, $on_sale, $comments_enabled, $user);

									if(!$result['error']) {
										include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";

										$stmt = $con->prepare("UPDATE `assets` SET `relatedid` = ? WHERE `id` = ?;");
										$stmt->bind_param('ii', $result['id'], $image_id);
										$stmt->execute();

										if($type == AssetType::DECAL || $type == AssetType::FACE) {
											/*$stmt = $con->prepare("UPDATE `asset_versions` SET `md5thumb` = ? WHERE `assetid` = ?");
											$stmt->bind_param('si', $md5hashfile, $result['id']);
											$stmt->execute();*/
										}

										if($type == AssetType::TSHIRT) {
											$directory = $_SERVER['DOCUMENT_ROOT'];
											$md5hashfile = md5($data);
											$assetsdir = "$directory/../assets/thumbs/$md5hashfile";
											imagesavealpha($tshirt, true);
											imagepng($tshirt, $assetsdir);
										}

										if(AssetTypeUtils::IsRenderable($type)) {
											self::ExecuteRender($result['id'], $type, $data);
										}
									}
								}

								return $result;
							} else if($type == AssetType::MESH) {
								if(!self::IsValidMesh($data)) {
									return INVALIDFILE;
								}

								if(!self::IsSupportedMesh($data)) {
									$mesh_result = MeshConverter::Convert($data);

									if(!$mesh_result['error'])
										$data = $mesh_result['mesh'];
									else
										return $mesh_result;
								}

								$result = self::CommitAsset($data, $type, $name, $description, $public, $on_sale, $comments_enabled, $user);

								if(!$result['error']) {
									if(AssetTypeUtils::IsRenderable($type)) {
										self::ExecuteRender($result['id'], $type, $data);
									}
								}

								return $result;

							} else if($type == AssetType::AUDIO) {

								$audio_mime_type = ImageUtils::checkMimeType($data);

								if(
									$audio_mime_type != "audio/mpeg" &&
									$audio_mime_type != "audio/ogg" &&
									$audio_mime_type != "audio/vorbis" &&
									$audio_mime_type != "audio/x-wav"
								) {
									return ["error" => true, "reason" => "Audio file was not a valid format! (found $audio_mime_type instead)"];
								}

								return self::CommitAsset($data, $type, $name, $description, $public, $on_sale, $comments_enabled, $user);

							} else if(
								$type == AssetType::HEAD	 ||
								$type == AssetType::TORSO	 ||
								$type == AssetType::LEFTARM	 ||
								$type == AssetType::RIGHTARM ||
								$type == AssetType::LEFTLEG	 ||
								$type == AssetType::RIGHTLEG
							) {
								if(!self::IsValidMesh($data)) {
									return INVALIDFILE;
								}

								if(!self::IsSupportedMesh($data)) {
									$mesh_result = MeshConverter::Convert($data);

									if(!$mesh_result['error'])
										$data = $mesh_result['mesh'];
									else
										return $mesh_result;
								}

								$mesh_asset_result = self::CommitAsset($data, AssetType::MESH, $name, $description, false, false, false, $user);

								if(!$mesh_asset_result['error']) {
									self::ExecuteRender($mesh_asset_result['id'], AssetType::MESH, $data);

									$result = self::CommitAsset(AssetTypeUtils::GenerateCharacterMeshRBXM($mesh_asset_result['id'], $type), $type, $name, $description, $public, $on_sale, $comments_enabled, $user);

									if(!$result['error']) {
										self::ExecuteRender($result['id'], $type, $data);
									}

									return $result;
								} else {
									return $mesh_asset_result;
								}
							} else {
								return ["error" => true, "reason" => "Invalid asset type found!"];
							}
						}
					} else {
						if($type == AssetType::PLACE) {
							return self::CommitAsset(null, $type, $name, $description, $public, $on_sale, $comments_enabled, $user);
						}
					}

				}
				else {
					return ["error" => true, "reason" => $canupload];
				}
			}

			return ["error" => true, "reason" => "User is not authorised to perform this action!"];
		}
	}
?>
