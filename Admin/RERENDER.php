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

    if(isset($_GET['userId'])) {
        $userid = intval($_GET['userId']);
        $user = User::FromID($userid);
        if($user == null) {
            return;
        }
        $characterinfo = $user->GetCharacterAppearanceVerbose();
		$charactermd5 = md5($characterinfo);
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
                header("Content-Type: image/png");
                imagepng($render_image);
                
            }
            
        }
    }

	

	
?>
