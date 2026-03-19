<?php
    class UtilUtils {
        public static function GetTimeAgo(DateTime $time) {
			$time_difference = time() - $time->getTimestamp();

			if( $time_difference < 1 ) { return 'less than 1 second ago'; }
			$condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
						30 * 24 * 60 * 60       =>  'month',
						24 * 60 * 60            =>  'day',
						60 * 60                 =>  'hour',
						60                      =>  'minute',
						1                       =>  'second'
			);

			foreach($condition as $secs => $str) {
				$d = $time_difference / $secs;

				if($d >= 1) {
					$t = round( $d );
					return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
				}
			}
		}

        public static function RecurseRemove($input, $find, $replace) {
			
			$result = str_replace($find, $replace,$input);

			if(str_contains($result, $find)) {
				return self::RecurseRemove($input, $find, $replace);
			}

			return $result;
		}

		public static function IsValidCSS(string $data) {
			if(true) {
				return true;
			}

			$blockedcssids = [
				//"@font",
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
				"url(",*/
				"base64",
				"BodyContainer",
				"#Container",
				"WrapperBody",
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
