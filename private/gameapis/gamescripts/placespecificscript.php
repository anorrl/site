<?php ob_start(); ?>
game:GetService("Players"):SetSaveDataUrl("http://{domain}/Persistence/SetBlob.ashx?placeid={id}&userid=%d&access={access}")
game:GetService("Players"):SetLoadDataUrl("http://{domain}/Persistence/GetBlob.ashx?placeid={id}&userid=%d&access={access}")

game:GetService("Players").PlayerAdded:connectFirst(function(player)
	player:LoadData()
end)

game:GetService("Players").PlayerRemoving:connectLast(function(player)
	player:SaveData()
end)
<?php
	use anorrl\Place;

	$domain = CONFIG->domain;
	
	function get_signature($script) {
		$signature = "";
		openssl_sign($script, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
		return base64_encode($signature);
	}

	if(isset($_GET['PlaceId']) && isset($_GET['access'])) {
		header("Content-Type: text/plain");

		$place = Place::FromID(intval($_GET['PlaceId']));

		if($place != null) {
			$script = "\r\n" . ob_get_clean();
			$script = str_replace("{domain}", $domain, $script);
			$script = str_replace("{id}", $_GET['PlaceId'], $script);
			$script = str_replace("{access}", $_GET['access'], $script);
			$signature = get_signature($script);

			echo "--arlsig%". $signature . "%" . $script;
		}
	}
	
?>
