<?php
	use anorrl\Place;
	use anorrl\User;

	header("Content-Type: application/xml");
	function IsValidXML(string $xml): bool {
		libxml_use_internal_errors(true);
		$sxe = simplexml_load_string($xml);
		if (!$sxe) {
			return false;
		}

		return true;
	}

	function IsTooManyBlobbers(Place $place, User $user) {
		if(!$user->IsBanned()) {
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
			$stmt = $con->prepare("SELECT * FROM `persistenceblobs` WHERE `blob_placeid` = ? AND `blob_playerid` = ?");
			$stmt->bind_param("ii", $place->id, $user->id);
			$stmt->execute();

			return $stmt->get_result()->num_rows > 1;
		}

		return false;
	}

	function BlobberExists(Place $place, User $user) {
		if(!$user->IsBanned()) {
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
			$stmt = $con->prepare("SELECT * FROM `persistenceblobs` WHERE `blob_placeid` = ? AND `blob_playerid` = ?");
			$stmt->bind_param("ii", $place->id, $user->id);
			$stmt->execute();

			return $stmt->get_result()->num_rows > 0;
		}

		return false;
	}

	function CreateBlobber(Place $place, User $user) {
		if(!$user->IsBanned() && !BlobberExists($place, $user)) {
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

			$stmt = $con->prepare("INSERT INTO `persistenceblobs`(`blob_placeid`, `blob_playerid`) VALUES (?, ?)");
			$stmt->bind_param("ii", $place->id, $user->id);
			$stmt->execute();
		}
		
	}

	function GetDataBlob(Place $place, User $user) {
		if(!$user->IsBanned() && BlobberExists($place, $user)) {
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

			$stmt = $con->prepare("SELECT * FROM `persistenceblobs` WHERE `blob_placeid` = ? AND `blob_playerid` = ?");
			$stmt->bind_param("ii", $place->id, $user->id);
			$stmt->execute();

			return $stmt->get_result()->fetch_assoc()['blob_data'];
		}

		return null;
	}
	
	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);
	$access = $settings['asset']['ACCESSKEY'];
	//placeid={id}&userid=%d&access={access}
	if(isset($_GET['placeid']) && isset($_GET['userid']) && isset($_GET['access'])) {
		$place = Place::FromID(intval($_GET['placeid']));
		$user = User::FromID(intval($_GET['userid']));

		if($place != null && $user != null && !$user->IsBanned() && $access == $_GET['access']) {

			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

			if(BlobberExists($place, $user)) {
				if(IsTooManyBlobbers($place, $user)) {
					$stmt = $con->prepare("DELETE FROM `persistenceblobs` WHERE `blob_placeid` = ? AND `blob_playerid` = ?");
					$stmt->bind_param("ii", $place->id, $user->id);
					$stmt->execute();

					CreateBlobber($place, $user);
				}
			} else {
				CreateBlobber($place, $user);
			}

			ob_clean();

			echo GetDataBlob($place, $user);

		}
	}
?>