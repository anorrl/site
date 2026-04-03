<?php
	/*
		REWRITE TO USE PDO AND ALSO LIKE DATABASE CLASS MAYBEEEE
	*/

	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);

	$database_settings = $settings['mysql'];

	$con = mysqli_connect(
		$database_settings['HOSTNAME'],
		$database_settings['USERNAME'],
		$database_settings['PASSWORD'],
		$database_settings['DATABASE']
	);
?>
