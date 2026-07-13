<?php

	namespace anorrl\utilities;

	class UtilUtils {
		public static function GetTimeAgo(\DateTime $time, bool $full = false) {
			$now = new \DateTime;
			$ago = $time;
			$diff = (array)$now->diff($ago);

			$diff['w'] = floor($diff['d'] / 7);
			$diff['d'] -= $diff['w'] * 7;

			$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
			);
			foreach ($string as $k => &$v) {
				if ($diff[$k]) {
					$v = $diff[$k] . ' ' . $v . ($diff[$k] > 1 ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}

			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}

		public static function RecurseRemove($input, $find, $replace) {
			
			$result = str_replace($find, $replace,$input);

			if(str_contains($result, $find)) {
				return self::RecurseRemove($result, $find, $replace);
			}

			return $result;
		}

		public static function StripUnicode(string $input) {
			$blockedchars = ['𒐫', '‮', '﷽', '𒈙', '⸻', '꧅'];
			return trim(str_replace($blockedchars, '', $input));
		}

		public static function HasBeenRewritten(): bool {
			if(!empty($_SERVER['IIS_WasUrlRewritten']))
				return true;
			else if(array_key_exists('HTTP_MOD_REWRITE',$_SERVER))
				return true;
			else if( array_key_exists('REDIRECT_URL', $_SERVER))
				return true;
			else
				return false;
		}

		public static function GetFilesArray(string $folder_location) {
			return array_diff(scandir($_SERVER['DOCUMENT_ROOT'].$folder_location, SCANDIR_SORT_NONE), ["..", "."]);
		}

		/**
		 * Summary of GetTimeDifference
		 * @param \DateTime $time
		 * @param string $format %a by default (days)
		 * @return int
		 */
		public static function GetTimeDifference(\DateTime $time, string $format = "%a"): int {
			return intval(new \DateTime()->diff($time)->format($format));
		}

		/**
		 * This is kind of ass.
		 * @param \DateTime $time
		 * @return void
		 */
		public static function GetSecondsElapsedFrom(\DateTime $time): int {
			$offset = -3600; //prod

			return (time()-($time->getTimestamp()+$time->getOffset()+$offset));
		}

		public static function IsValidCSS(string $data) {
			$blockedcssids = [
				/*"@font",
				"ProfileSign",
				"#background",
				"UsernameRow",
				"CreditsRow",
				"LogoutSign",
				"Logo",
				"Links",
				"UserLinks",
				"DisplayMobileWarning",
				"MobileWarningText",
				"Footer",
				"FooterContainer",
				"Legalese",
				/*"line-height",
				"display:",
				"opacity",
				"url(",
				"base64",
				"BodyContainer",
				"#Container",
				"WrapperBody",*/
				"\\",
				"::",
				/*"filter",
				"@keyframes",
				"transform",
				"deg",
				"\"",
				"'",
				"none",
				"hidden"*/
				/*"filter",
				"em",
				"\\",
				"transform",
				"border",
				"@keyframes",
				"width",
				"height",
				"margin",
				"%",
				"padding",
				"spacing",
				"top",
				"left",
				"right",
				"bottom",
				"position",
				"break",
				"!important",
				"direction",
				"writing-mode",
				"circle(",
				"clip",
				"shape",
				"columns",
				"clear",
				"vertical",
				"blend",
				"space",
				"white-space",
				"mode",
				"unicode",
				"indent",
				"transparent",
				"::",
				"visibility",
				"hidden",
				"none",
				"shadow",
				"*",
				"quotes",
				"\"",
				"align",
				"deg;",
				"deg",
				"img",
				"00;",
				"div",
				*/
			];

			foreach($blockedcssids as $blockedterm) {
				if(str_contains($data, $blockedterm)) {
					return false;
				}

				if(str_contains($data, "\t$blockedterm")) {
					return false;
				}

				if(str_contains($data, " $blockedterm")) {
					return false;
				}

				if(str_contains($data, "\r$blockedterm")) {
					return false;
				}

				if(str_contains($data, "\n$blockedterm")) {
					return false;
				}

				if(str_contains($data, "\r\n$blockedterm")) {
					return false;
				}
			}

			return true;
		}

	}
?>