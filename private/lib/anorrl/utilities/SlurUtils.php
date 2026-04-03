<?php
	namespace anorrl\utilities;

	/**
	 * This is for detecting and censoring any horrendous shit that could be posted...
	 */
	class SlurUtils {

		// TODO: just return [Content Deleted]
		public static function ProcessText(string $input) {
			$profanity = file($_SERVER['DOCUMENT_ROOT']."/core/badwords.txt", FILE_IGNORE_NEW_LINES);

			$processed = $input;

			foreach($profanity as $slur) {
				if(str_starts_with($input, "$slur ")) {
					$pretext = substr($input, strlen("$slur "), strlen($input));

					$processed = str_repeat("#", strlen($slur))." ".$pretext;
				}
			}

			$original_words = explode(" ", $input);
			$words = explode(" ", $processed);
			
			$processed = "";
			
			
			for($i = 0; $i < count($words); $i++) {
				//$word = $words[$i];
				foreach($profanity as $slur) {
					if(strlen($slur) >= 4 && str_contains($words[$i], $slur)) {
						$words[$i] = str_replace($slur,str_repeat("#", strlen($slur)),$words[$i]);
					} else {
						//echo $slur;
						if(str_starts_with(strtolower($words[$i]), $slur)) {
							//echo $slur;
							$pretext = substr($words[$i], strlen($slur), strlen($words[$i]));

							$words[$i] = str_repeat("#", strlen($slur)).$pretext;
						}
					}
				}
				
				if(strlen($original_words[$i]) > 2) {
					if(str_contains($words[$i], "#") && count(count_chars($words[$i], 1)) <= 2) {
						$words[$i] = str_repeat("#", strlen($words[$i]));
					}
				}
				
			}

			foreach($words as $word) {
				$processed .= $word." ";
			}

			return trim($processed);
		}

	}
?>
