<?php

	// Width=60&Height=62&ImageFormat=png&AssetID=8
	
	use anorrl\Asset;
	use anorrl\enums\AssetType;
	use anorrl\utilities\ImageUtils;

	if(isset($_GET['format']) && $_GET['format'] == "png") {
	
		if(isset($_GET['assetId'])) {
			$id = intval($_GET['assetId']);
			
			$asset = Asset::FromID($id);

			$contents = $asset ? $asset->getThumbnail() : file_get_contents(get_path_sitefile("public/images/unavailable.jpg"));

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
				set_content_type(ARLTYPEPNG);
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
				set_content_type(ARLTYPEPNG);
				imagepng($resizedimage, null, 9);
			} else {
				$file_info = new finfo(FILEINFO_MIME_TYPE);
				$mime = $file_info->buffer($contents);

				set_content_type($mime);
				ob_clean();
				echo $contents;
			}
		}
	} else {
		$width = $_GET['Width'];
		$height = $_GET['Height'];
		$assetid = $_GET['AssetID'];
		echo "/thumbs/?id=$assetid&sx=$width&sy=$height";
	}

	
?>