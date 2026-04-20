<?php

	use anorrl\User;

	if(isset($_GET['headshot'])) {
		die(include $_SERVER['DOCUMENT_ROOT']."/core/thumbs/headshot.php");
	}
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
			include $_SERVER['DOCUMENT_ROOT']."/private/connection.php";
			
			//base64_encode(

			$md5hash = $user->currentoutfitmd5;

			$path = $_SERVER['DOCUMENT_ROOT']."/../renders/$md5hash.png";

			if(!file_exists($path)) {
				$user->render();

				if(!file_exists($path))
					$path = $_SERVER['DOCUMENT_ROOT']."/public/images/unavailable.jpg";
			}

			$contents = file_get_contents($path);

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