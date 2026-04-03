<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/renderer.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/asset.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/user.php";

	$user = UserUtils::RetrieveUser();

	if($user == null || ($user != null && !$user->IsAdmin())) {
		die("Nice try but this is no longer needed...");
	}

	$mediadir = $_SERVER['DOCUMENT_ROOT']."/../renders/";

	include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

	$stmt = $con->prepare("SELECT `user_id` FROM `users` WHERE 1;");
	$stmt->execute();

	$result_stmt = $stmt->get_result();

	while($row = $result_stmt->fetch_assoc()) {
		$user = User::FromID(intval($row['user_id']));

		$characterinfo = $user->GetCharacterAppearanceVerbose();
		$charactermd5 = md5($characterinfo);

		if(!file_exists("$mediadir/$charactermd5.png")) {
			$render = Renderer::RenderUser($user->id);
			if($render != null) {
				$data = "data:image/png;base64,$render";
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);

				$render_image = imagecreatefromstring($data);
				imagesavealpha($render_image, true);
				imagepng($render_image, "$mediadir/$charactermd5.png");

				$user->UpdateOutfitHash();

				
			}
			
		}

		if(!file_exists("$mediadir/headshot_$charactermd5.png")) {
			$render = Renderer::RenderUser($user->id, true);
			if($render != null) {
				$data = "data:image/png;base64,$render";
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);

				$render_image = imagecreatefromstring($data);
				imagesavealpha($render_image, true);
				imagepng($render_image, "$mediadir/headshot_$charactermd5.png");

				$user->UpdateOutfitHash();

				
			}
			
		}
	}

	
?>
