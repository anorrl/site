<?php

	namespace anorrl\utilities;

	class FileSplasher extends Splasher {
		
		function __construct(string $filename, bool $true_random = true, string $name = "") {
			parent::__construct(file(get_path_sitefile("private/splashes/$filename.txt")), $true_random, $name);
		}
	}

?>