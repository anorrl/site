<?php 

use anorrl\User;

header("Content-Type: text/plain"); 

// dont cache this shit!
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(isset($_GET['clothing'])): ?>
<roblox xmlns:xmime="http://www.w3.org/2005/05/xmlmime" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://arl.lambda.cam/roblox.xsd" version="4">
	<External>null</External>
	<External>nil</External>
	<Item class="BodyColors" referent="RBX96B37B6C58984541BA7545B230B6E10D">
		<Properties>
			<int name="HeadColor">194</int>
			<int name="LeftArmColor">194</int>
			<int name="LeftLegColor">194</int>
			<string name="Name">Body Colors</string>
			<int name="RightArmColor">194</int>
			<int name="RightLegColor">194</int>
			<int name="TorsoColor">194</int>
		</Properties>
	</Item>
</roblox>
<?php else:
	header("Content-Type: text/plain"); 
	// grab body colours of character
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";
	if(isset($_GET['userId'])) {
		$user = User::FromID(intval($_GET['userId']));
		if($user != null) {
			$colours = $user->GetBodyColours();
		} else {
			die();
		}
	} else {
		die();
	}
	
?>
<roblox xmlns:xmime="http://www.w3.org/2005/05/xmlmime" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://arl.lambda.cam/roblox.xsd" version="4">
	<External>null</External>
	<External>nil</External>
	<Item class="BodyColors" referent="RBXCCC36C132C584B37B29DB69EAE48292A">
		<Properties>
			<int name="HeadColor"><?= $colours['head'] ?></int>
			<int name="LeftArmColor"><?= $colours['rightarm'] ?></int>
			<int name="LeftLegColor"><?= $colours['leftleg'] ?></int>
			<string name="Name">Body Colors</string>
			<int name="RightArmColor"><?= $colours['leftarm'] ?></int>
			<int name="RightLegColor"><?= $colours['rightleg'] ?></int>
			<int name="TorsoColor"><?= $colours['torso'] ?></int>
		</Properties>
	</Item>
</roblox>
<?php endif ?>