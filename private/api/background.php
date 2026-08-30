<?php
	// this is just used for default profiles :P

	set_content_type(ARLTYPEPNG);

	function moving(GDImage $image, int $size, int $x, int $y, string $text, string $font, int $iteration = 10, int $steps = 20, int $alpha_steps = 30, bool $inverted = false) {
		for($i = 0; $i < $iteration; $i++) {
			$offset_x = $i * $steps;
			if($inverted)
				$offset_x = -$offset_x;

			imagettftext($image, $size, 0, $x+$offset_x, $y, imagecolorallocatealpha($image, 255, 255, 255, min(127, 20 + $i * $alpha_steps)), get_path_sitefile("public/css/fonts/{$font}.ttf"), $text);
		}
		
	}



	$image = imagecreatetruecolor(970, 220);
	imagefill($image, 0, 0, rand(0x000000, 0xffffff));
	moving($image, 15, 10, 22, "its a anorrl background", "Punktype", 10, 30, 40);
	moving($image, 20, 970-150, 220-20, "ANORRL", "SplendidB", 10, 10, 10);
	imagettftext($image, 24, 0, 970-150, 220-20, imagecolorallocatealpha($image, 60, 60, 60, 0), get_path_sitefile("public/css/fonts/SplendidB.ttf"), "ANORRL");

	die(imagepng($image));
?>
