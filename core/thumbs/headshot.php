<?php

	use anorrl\User;

	if(isset($_GET['id']) || isset($_GET['userId'])) {
		if(isset($_GET['id'])) {
			$id = intval($_GET['id']);
		} else {
			$id = intval($_GET['userId']);
		}
		
		$nocompress = isset($_GET['nocompress']);

		$specialcase = false;

		$user = User::FromID($id);
		if($user != null) {
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
			
			//base64_encode(

			$md5hash = $user->currentoutfitmd5;

			if(file_exists($_SERVER['DOCUMENT_ROOT']."/../renders/headshot_$md5hash.png")) {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/../renders/headshot_$md5hash.png");
			} else {
				$contents = file_get_contents($_SERVER['DOCUMENT_ROOT']."/images/unavailable.jpg");
			}

			ob_clean();

			if(isset($_GET['sxy'])) {
				$size = intval($_GET['sxy']);
				if($size < 16 || $size > 420) {
					$size = 420;
				}

				$image = imagecreatefromstring($contents);
				imagesavealpha($image, true);
				$width = imagesx($image);
				$height = imagesy($image);

				$resizedimage = imagecreatetruecolor($size, $size);
				imagesavealpha($resizedimage, true);
				$trans_colour = imagecolorallocatealpha($resizedimage, 0, 0, 0, 127);
				imagefill($resizedimage, 0, 0, $trans_colour);
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