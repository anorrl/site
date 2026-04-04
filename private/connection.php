<?php
	
	$CONFIG = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/../settings.json"));
	$con = mysqli_connect(
		$CONFIG->database->hostname,
		$CONFIG->database->username,
		$CONFIG->database->password,
		$CONFIG->database->name
	);
?>
