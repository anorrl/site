<?php
	include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
	require_once $_SERVER["DOCUMENT_ROOT"]."/core/utilities/assetutils.php";
	require_once $_SERVER["DOCUMENT_ROOT"]."/core/classes/renderer.php";
	require_once $_SERVER["DOCUMENT_ROOT"]."/core/utilities/userutils.php";
	
	$user = UserUtils::RetrieveUser();

	$directory = $_SERVER['DOCUMENT_ROOT'];
	$assetsdir = "$directory/../assets/";

	function CheckAndDeleteAsset(int $aid) {
		include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
		$asset = Asset::FromID($aid);
		if($asset != null) {
			$stmt = $con->prepare("SELECT * FROM `assets` WHERE `asset_id` = ? OR `asset_relatedid` = ?;");
			$stmt->bind_param("ii", $aid, $aid);
			$stmt->execute();

			$result = $stmt->get_result();

			$ids = [];
			while($row = $result->fetch_assoc()) {
				array_push($ids, $row['asset_id']);
			}

			$md5s = [];

			foreach($ids as $key => $value) {
				$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_assetid` = ? ORDER BY `version_id` DESC;");
				$stmt->bind_param("i", $value);
				$stmt->execute();

				$result = $stmt->get_result();
				if($result->num_rows != 0) {
					$row = $result->fetch_assoc();

					$md5s["$value"] = $row['version_md5sig'];
				}
			}

			foreach($md5s as $key => $value) {
				$stmt = $con->prepare("SELECT * FROM `asset_versions` WHERE `version_md5sig` = ? AND `version_assetid` != ? ORDER BY `version_id` DESC;");
				$stmt->bind_param("si", $value, $key);
				$stmt->execute();

				$result = $stmt->get_result();
				if($result->num_rows == 0) {
					$row = $result->fetch_assoc();

					if(file_exists("$assetsdir/$value")){
						unlink("$assetsdir/$value");
					}

					if(file_exists("$assetsdir/thumbs/$value")){
						unlink("$assetsdir/thumbs/$value");
					}
				}
			}
		}
	}

	if(isset($_POST['type'])) {
		if(isset($_POST['id'])) {
			$asset = Asset::FromID(intval($_POST['id']));

			if($asset != null && ($asset->creator->id == $user->id || $user->IsAdmin())) {
				if($_POST['type'] == "delete") {
					echo "deleting";
					$id = $asset->id;

					// move all of this shit to a delete function

					$stmt = $con->prepare('DELETE FROM `inventory` WHERE `inv_assetid` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `transactions` WHERE `ta_asset` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `visit` WHERE `visit_place` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					$stmt = $con->prepare('DELETE FROM `favourites` WHERE `fav_assetid` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();
					
					CheckAndDeleteAsset($asset->id);

					$stmt = $con->prepare('DELETE FROM `assets` WHERE `asset_id` = ?');
					$stmt -> bind_param("i", $id);
					$stmt->execute();

					if($asset->type == AssetType::PLACE) {
						$stmt = $con->prepare('DELETE FROM `asset_places` WHERE `place_id` = ?');
						$stmt -> bind_param("i", $id);
						$stmt->execute();
					}


					die("Success!");
				} else if($_POST['type'] == "render") {
					$id = $asset->id;
					$type = $asset->type;

					if($type == AssetType::SHIRT || $type == AssetType::PANTS) {
						$render = Renderer::RenderPlayer($id);	
					} else if($type == AssetType::PLACE) {
						$render = Renderer::RenderPlace($id);
					} else if($type == AssetType::MESH) {
						$render = Renderer::RenderMesh($id);
					} else if($type == AssetType::MODEL || $type == AssetType::HAT || $type == AssetType::GEAR) {
						$render = Renderer::RenderModel($id);
					} else if($type == AssetType::TORSO) {
						$render = Renderer::RenderPlayer($id);
					}

					if($render != null) {
						$data = base64_decode($render);
						
						AssetVersion::GetLatestVersionOf($asset)->SetThumbnail($asset);

						file_put_contents($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/".AssetVersion::GetLatestVersionOf($asset)->md5sig, $data);
					} else {
						if(file_exists($_SERVER['DOCUMENT_ROOT']."/../assets/thumbs/".AssetVersion::GetLatestVersionOf($asset)->md5thumb)) {

						} else {
							$stmt = $con->prepare("UPDATE `asset_versions` SET `version_md5thumb` = 'placeholder' WHERE `version_id` = ?");
							$stmt->bind_param('i', AssetVersion::GetLatestVersionOf($asset)->id);
							$stmt->execute();
						}
					}
					
					$message = "Success!";
				}

			}
		}
	}

	$message = "You are not authorised to use this.";
		
	

	die($message);
?>
