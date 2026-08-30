<?php

	use anorrl\enums\AssetType;
	use anorrl\Asset;
	use anorrl\AssetVersion;
	use anorrl\utilities\ImageUtils;
	
	// this could be moved to some GetThumbs function or something...

	if(isset($_GET['id'])) {
		$id = intval($_GET['id']);
		
		$nocompress = isset($_GET['nocompress']);

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
			if(!$nocompress) {
				set_content_type(ARLTYPEWEBP);
				ob_start("ob_gzhandler");
				set_encoding("gzip");
				imagewebp($resizedimage, null, 50);
				ob_end_flush();
			} else {
				set_content_type(ARLTYPEPNG);
				imagepng($resizedimage, null, 9);
			}
			
			
		} else if(isset($_GET['sx']) && isset($_GET['sy'])) {
			$sizex = intval($_GET['sx']);
			if($sizex < 16 || $sizex > 1080) {
				$sizex = 420;
			}

			$sizey = intval($_GET['sy']);
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
			if(!$nocompress) {
				set_content_type(ARLTYPEWEBP);
				ob_start("ob_gzhandler");
				set_encoding("gzip");
				imagewebp($resizedimage, null, 50);
				ob_end_flush();
			} else {
				set_content_type(ARLTYPEPNG);
				imagepng($resizedimage, null, 9);
			}
		} else {
			$file_info = new finfo(FILEINFO_MIME_TYPE);
			$mime = $file_info->buffer($contents);

			set_content_type($mime);
			ob_clean();
			echo $contents;
		}
	}

?>
