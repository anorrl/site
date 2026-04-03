<?php
	namespace anorrl\utilities;

	class ImageUtils {

		public static function checkMimeType($contents) {
			$file_info = new \finfo(FILEINFO_MIME_TYPE);
			return $file_info->buffer($contents);
		}

		public static function cropAlign($image, $cropWidth, $cropHeight, $horizontalAlign = 'center', $verticalAlign = 'middle') {
			$width = imagesx($image);
			$height = imagesy($image);
			$horizontalAlignPixels = self::calculatePixelsForAlign($width, $cropWidth, $horizontalAlign);
			$verticalAlignPixels = self::calculatePixelsForAlign($height, $cropHeight, $verticalAlign);
			return imageCrop($image, [
				'x' => $horizontalAlignPixels[0],
				'y' => $verticalAlignPixels[0],
				'width' => $horizontalAlignPixels[1],
				'height' => $verticalAlignPixels[1]
			]);
		}

		//https://stackoverflow.com/questions/6891352/crop-image-from-center-php
		public static function calculatePixelsForAlign($imageSize, $cropSize, $align) {
			switch ($align) {
				case 'left':
				case 'top':
					return [0, min($cropSize, $imageSize)];
				case 'right':
				case 'bottom':
					return [max(0, $imageSize - $cropSize), min($cropSize, $imageSize)];
				case 'center':
				case 'middle':
					return [
						max(0, floor(($imageSize / 2) - ($cropSize / 2))),
						min($cropSize, $imageSize),
					];
				default: return [0, $imageSize];
			}
		}
	}
?>