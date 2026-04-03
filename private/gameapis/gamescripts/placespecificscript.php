<?php ob_start(); ?>
game:GetService("Players"):SetSaveDataUrl("http://arl.lambda.cam/Persistence/SetBlob.ashx?placeid={id}&userid=%d&access={access}")
game:GetService("Players"):SetLoadDataUrl("http://arl.lambda.cam/Persistence/GetBlob.ashx?placeid={id}&userid=%d&access={access}")

game:GetService("Players").PlayerAdded:connectFirst(function(player)
	--player:LoadData()	
end)

game:GetService("Players").PlayerRemoving:connectLast(function(player)
	--player:SaveData()
end)
<?php
	use anorrl\Place;
	
	function get_signature($script) {
		$signature = "";
		openssl_sign($script, $signature, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/../PrivateKey.pem"), OPENSSL_ALGO_SHA1);
		return base64_encode($signature);
	}

	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/asset.php";

	if(isset($_GET['PlaceId']) && isset($_GET['access'])) {
		header("Content-Type: text/plain");

		$place = Place::FromID(intval($_GET['PlaceId']));

		if($place != null) {
			$script = "\r\n" . ob_get_clean();
			$script = str_replace("{id}", $_GET['PlaceId'], $script);
			$script = str_replace("{access}", $_GET['access'], $script);
			$signature = get_signature($script);

			echo "--rbxsig%". $signature . "%" . $script;
		}
	}
	
?>