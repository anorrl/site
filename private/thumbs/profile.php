<?php

	use anorrl\User;
	use anorrl\utilities\ImageUtils;
	use anorrl\utilities\UtilUtils;
	
	

	if(isset($_GET['id']) || isset($_GET['userId'])) {
		if(isset($_GET['id'])) {
			$id = intval($_GET['id']);
		} else {
			$id = intval($_GET['userId']);
		}

		
		
		$nocompress = isset($_GET['nocompress']);

		$specialcase = false;

		$asset = User::FromID($id);
		
		if($asset != null) {
			
			if(file_exists(get_user_profile_path($id))) {
				$contents = file_get_contents(get_user_profile_path($id));
			} else {
				$pictures = UtilUtils::GetFilesArray("/public/images/profile_pictures/");
				 
				$rand_pic = 1+rand(0, count($pictures) - 1);
				
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/public/images/profile_pictures/pfp_$rand_pic.png");
				
			}

			ob_clean();
			if(!str_contains(ImageUtils::checkMimeType($contents), "image/gif") && (isset($_GET['sxy']) || (isset($_GET['sx']) && isset($_GET['sy'])))) {
				$size = intval($_GET['sxy'] ?? $_GET['sx']);
				if($size < 16 || $size > 420) {
					$size = 420;
				}

				$image = imagecreatefromstring($contents);
				$width = imagesx($image);
				$height = imagesy($image);
				$resizedimage = imagecreatetruecolor($size, $size);
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
			} else {
				$file_info = new finfo(FILEINFO_MIME_TYPE);
				$mime = $file_info->buffer($contents);

				set_content_type($mime);
				ob_clean();
				echo $contents;
			}

			
		}
	}

?>