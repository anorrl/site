<?php

	use anorrl\User;
	use anorrl\utilities\ImageUtils;
	
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
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
			
			if(file_exists($_SERVER['DOCUMENT_ROOT']."/../users/profile_$id.png")) {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../users/profile_$id.png");
			} else {
				$pictures = array_diff(scandir($_SERVER['DOCUMENT_ROOT']."/images/profile_pictures/"), array("..", "."));
				 
				$rand_pic = 1+rand(0, count($pictures) - 1);
				
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/images/profile_pictures/pfp_$rand_pic.png");
			}

			ob_clean();
			
			if(!str_contains(ImageUtils::checkMimeType($contents), "image/gif") && (isset($_GET['sxy']) || (isset($_GET['sx']) && isset($_GET['sy'])))) {
				if(isset($_GET['sxy'])) {
					$size = intval($_GET['sxy']);
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
						header("Content-Type: image/webp");
						ob_start("ob_gzhandler");
						header("Content-Encoding: gzip");
						imagewebp($resizedimage, null, 50);
						ob_end_flush();
					} else {
						header("Content-Type: image/png");
						imagepng($resizedimage, null, 9);
					}
				}
			} else {
				$file_info = new finfo(FILEINFO_MIME_TYPE);
				$mime = $file_info->buffer($contents);

				header("Content-Type: $mime");
				ob_clean();
				echo $contents;
			}

			
		}
	}

?>