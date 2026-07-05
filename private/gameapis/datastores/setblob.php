<?php
	use anorrl\Place;
	use anorrl\User;
	use anorrl\Database;

	set_content_type(ARLTYPEXML);
	function IsValidXML(string $xml): bool {
		libxml_use_internal_errors(true);
		$sxe = simplexml_load_string($xml);
		if (!$sxe) {
			return false;
		}

		return true;
	}

	function IsTooManyBlobbers(Place $place, User $user) {
		if(!$user->isBanned()) {
			return Database::singleton()->run(
				"SELECT * FROM `persistenceblobs` WHERE `blob_placeid` = :placeid AND `blob_playerid` = :userid",
				[
					":placeid" => $place->id,
					":userid" => $user->id
				]
			)->rowCount() > 1;
		}

		return false;
	}

	function BlobberExists(Place $place, User $user) {
		if(!$user->isBanned()) {
			return Database::singleton()->run(
				"SELECT * FROM `persistenceblobs` WHERE `blob_placeid` = :placeid AND `blob_playerid` = :userid",
				[
					":placeid" => $place->id,
					":userid" => $user->id
				]
			)->rowCount() > 0;
		}

		return false;
	}

	function CreateBlobber(Place $place, User $user) {
		if(!$user->isBanned() && !BlobberExists($place, $user)) {
			Database::singleton()->run(
				"INSERT INTO `persistenceblobs`(`blob_placeid`, `blob_playerid`) VALUES (:placeid, :userid)",
				[
					":placeid" => $place->id,
					":userid" => $user->id
				]
			);
		}
		
	}

	function GetDataBlob(Place $place, User $user) {
		if(!$user->isBanned() && BlobberExists($place, $user)) {
			return Database::singleton()->run(
				"SELECT * FROM `persistenceblobs` WHERE `blob_placeid` = :placeid AND `blob_playerid` = :userid",
				[
					":placeid" => $place->id,
					":userid" => $user->id
				]
			)->fetchObject()->blob_data;
		}

		return null;
	}

	function SetDataBlob(Place $place, User $user, string $data) {
		if(IsValidXML($data) && !$user->isBanned() && BlobberExists($place, $user)) {
			Database::singleton()->run(
				"UPDATE `persistenceblobs` SET `blob_data` = :data WHERE `blob_placeid` = :placeid AND `blob_playerid` = :userid",
				[
					":data" => $data,
					":placeid" => $place->id,
					":userid" => $user->id
				]
			);
		} else {
			exit_http(500);
		}
	}
	
	if(isset($_GET['placeid']) && isset($_GET['userid']) && isset($_GET['access'])) {
		$place = Place::FromID(intval($_GET['placeid']));
		$user = User::FromID(intval($_GET['userid']));

		if($place != null && $user != null && !$user->isBanned() && CONFIG->asset->key == $_GET['access']) {
			if(BlobberExists($place, $user)) {
				if(IsTooManyBlobbers($place, $user)) {
					Database::singleton()->run(
						"DELETE FROM `persistenceblobs` WHERE `blob_placeid` = :placeid AND `blob_playerid` = :userid",
						[
							":placeid" => $place->id,
							":userid" => $user->id
						]
					);
					CreateBlobber($place, $user);
				}
			} else {
				CreateBlobber($place, $user);
			}

			$recieveddata = file_get_contents("php://input");
			if(strlen(trim($recieveddata)) != 0) {
				if(strlen(gzdecode($recieveddata)) != 0) {
					$recieveddata = gzdecode($recieveddata);
				}
			} else {
				exit_http(500);
			}

			SetDataBlob($place, $user, $recieveddata);
		}
	}
?>