<?php

	namespace anorrl\utilities;

	class Splasher {

		public array $splashes;

		function __construct(string $filename) {
			$this->splashes = file($_SERVER["DOCUMENT_ROOT"]."/private/splashes/$filename.txt");
			shuffle($this->splashes);
		}

		function getRandomSplash() {
			return $this->splashes[array_rand($this->splashes)];
		}
	}
?>