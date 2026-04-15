<?php

	// Width=60&Height=62&ImageFormat=png&AssetID=8
	
	use anorrl\Asset;
	use anorrl\AssetVersion;
	use anorrl\enums\AssetType;
	use anorrl\utilities\ImageUtils;

	if(isset($_GET['format']) && $_GET['format'] == "png") {
	
		if(isset($_GET['assetId'])) {
			$id = intval($_GET['assetId']);

			$specialcase = false;

			$asset = Asset::FromID($id);
			if($asset != null) {
				
				$version = AssetVersion::GetLatestVersionOf($asset);

				if($version == null && $asset->type == AssetType::PLACE) {
					$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/noassets.png");
				} else {
					$md5hash = $version->md5sig;
					$thumbsmd5hash = $version->md5thumb;

					if($asset->type == AssetType::AUDIO && ($thumbsmd5hash == "sound" || $md5hash == $thumbsmd5hash)) {
						$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/audio.png");
					} else if($asset->type == AssetType::LUA) {
						$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/script.png");
					} else if($asset->type == AssetType::ANIMATION) {
						$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/animation.png");
					} else if($thumbsmd5hash == "placeholder" || !$asset->isUsable()) {
						$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
					} else {
						// TODO: rewrite this abomination.
						if($asset->type == AssetType::AUDIO && $md5hash != $thumbsmd5hash) {
							if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash")) {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash");
								$specialcase = true;
							} else {
								$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
							}
						} else {
							if(count($asset->getRelatedAssets()) != 0 && ($asset->type == AssetType::DECAL || $asset->type == AssetType::FACE) || $asset->type == AssetType::IMAGE) {
								if(count($asset->getRelatedAssets()) == 1 && $asset->getRelatedAssets()[0]->type == AssetType::IMAGE && ($asset->type == AssetType::DECAL || $asset->type == AssetType::FACE)) {
									$thumbsmd5hash = $asset->getRelatedAssets()[0]->getLatestVersionDetails()->md5sig;
								}
								
								if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash")) {
									$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/$thumbsmd5hash");
									$specialcase = true;
								} else {
									$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
								}
							} else {
								if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$id")) {
									$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$id");
								}
								else if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$thumbsmd5hash")) {
									$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/$thumbsmd5hash");
								}
								else {
									$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.png");
								}
							}
						}
						
					}
				}

				ob_clean();

				if(isset($_GET['sxy'])) {
					$size = intval($_GET['sxy']);
					if($size < 16 || $size > 420) {
						$size = 420;
					}

					$image = imagecreatefromstring($contents);
					$width = imagesx($image);
					$height = imagesy($image);
					
					// Mostly just used for places in stuff/create pages
					if($width != $height) {
						if($width > $height) {
							$cropSize = $height;
						}

						if($width < $height) {
							$cropSize = $width;
						}

						$image = ImageUtils::cropAlign($image,$cropSize, $cropSize);
					}

					$width = imagesx($image);
					$height = imagesy($image);

					$resizedimage = imagecreatetruecolor($size, $size);
					imagesavealpha($resizedimage, true);
					$trans_colour = imagecolorallocatealpha($resizedimage, 0, 0, 0, 127);
					imagefill($resizedimage, 0, 0, $trans_colour);
					
					if($asset->type == AssetType::FACE) {
						// whatever lmfao
						$sizeoffsetfactor = 15 * ((420-($size == 420 ? 0 : $size))/420);
						imagefilledrectangle($resizedimage, $sizeoffsetfactor, $sizeoffsetfactor, $size-$sizeoffsetfactor, $size-$sizeoffsetfactor, 0xafafaf);
					}

					imagecopyresampled($resizedimage, $image, 0, 0, 0, 0, $size, $size, $width, $height);
					imagesavealpha($resizedimage, true);
					
					ob_clean();
					header("Content-Type: image/png");
					imagepng($resizedimage, null, 9);
				} else if(isset($_GET['width']) && isset($_GET['height'])) {
					$sizex = intval($_GET['width']);
					if($sizex < 16 || $sizex > 1080) {
						$sizex = 420;
					}

					$sizey = intval($_GET['height']);
					if($sizey < 16 || $sizey > 1080) {
						$sizey = 420;
					}

					$image = imagecreatefromstring($contents);
					$width = imagesx($image);
					$height = imagesy($image);

					if($width != $height && $asset->type != AssetType::PLACE) {
						if($width > $height) {
							$cropSize = $height;
						}

						if($width < $height) {
							$cropSize = $width;
						}

						$image = ImageUtils::cropAlign($image,$cropSize, $cropSize);
						$width = $cropSize;
						$height = $cropSize;
					}

					imagesavealpha($image, true);

					$resizedimage = imagecreatetruecolor($sizex, $sizey);
					imagesavealpha($resizedimage, true);
					$trans_colour = imagecolorallocatealpha($resizedimage, 0, 0, 0, 127);
					imagefill($resizedimage, 0, 0, $trans_colour);
					imagecopyresampled($resizedimage, $image, 0, 0, 0, 0, $sizex, $sizey, $width, $height);

					ob_clean();
					header("Content-Type: image/png");
					imagepng($resizedimage, null, 9);
				} else {
					$file_info = new finfo(FILEINFO_MIME_TYPE);
					$mime = $file_info->buffer($contents);

					header("Content-Type: $mime");
					ob_clean();
					echo $contents;
				}
			} else {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.jpg");

				if(isset($_GET['width']) && isset($_GET['height'])) {
					$sizex = intval($_GET['width']);
					if($sizex < 16 || $sizex > 1080) {
						$sizex = 420;
					}

					$sizey = intval($_GET['height']);
					if($sizey < 16 || $sizey > 1080) {
						$sizey = 420;
					}

					$image = imagecreatefromstring($contents);
					$width = imagesx($image);
					$height = imagesy($image);

					imagesavealpha($image, true);

					$resizedimage = imagecreatetruecolor($sizex, $sizey);
					imagesavealpha($resizedimage, true);
					$trans_colour = imagecolorallocatealpha($resizedimage, 0, 0, 0, 127);
					imagefill($resizedimage, 0, 0, $trans_colour);
					imagecopyresampled($resizedimage, $image, 0, 0, 0, 0, $sizex, $sizey, $width, $height);

					ob_clean();
					header("Content-Type: image/png");
					imagepng($resizedimage, null, 9);
				} else {
					$file_info = new finfo(FILEINFO_MIME_TYPE);
					$mime = $file_info->buffer($contents);

					header("Content-Type: $mime");
					ob_clean();
					echo $contents;
				}
			}
		}
	} else {
		$width = $_GET['Width'];
		$height = $_GET['Height'];
		$assetid = $_GET['AssetID'];
		echo "/thumbs/?id=$assetid&sx=$width&sy=$height";
	}

	
?>