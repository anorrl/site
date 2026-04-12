<?php
	use anorrl\Asset;
	use anorrl\Page;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetUploader;

	if(isset($type)) {
		$type = trim(strtolower($type));
	}

	$user = SESSION->user;

	$validtypes = [
		"faces",
		"shirts",
		"tshirts",
		"pants",

		"audio",
		"decals",
		"models",

		"gears",

		"meshes",
		"images",
		"lua",
		"hats",
		"animations",
	];

	$types = [
		"faces" => AssetType::FACE,
		"shirts" => AssetType::SHIRT,
		"tshirts" => AssetType::TSHIRT,
		"pants" => AssetType::PANTS,
		"audio" => AssetType::AUDIO,
		"decals" => AssetType::DECAL,
		"models" => AssetType::MODEL,
		"gears" => AssetType::GEAR,
		"meshes" => AssetType::MESH,
		"images" => AssetType::IMAGE,
		"lua" => AssetType::LUA,
		"hats" => AssetType::HAT,
		"animations" => AssetType::ANIMATION,
	];

	if(count($_POST) != 0) {
		if(in_array($type, $validtypes)) {
			if(isset($_POST['ANORRL$CreateAsset$Name']) &&
				isset($_POST ['ANORRL$CreateAsset$Description']) &&
				isset($_FILES['ANORRL$CreateAsset$File'])
			) {
				
				//if($user->isAdmin()) {
				$result = null;
				$name = trim($_POST['ANORRL$CreateAsset$Name']);

				$description = trim($_POST['ANORRL$CreateAsset$Description']);
				$public = isset($_POST['ANORRL$CreateAsset$Public']);
				$comments_enabled = isset($_POST['ANORRL$CreateAsset$CommentsEnabled']);
				$on_sale = isset($_POST['ANORRL$CreateAsset$OnSale']);

				$result = AssetUploader::UploadAsset($_FILES['ANORRL$CreateAsset$File'], $types[$type], $name, $description, $public, $on_sale, $comments_enabled);
				
				/*} else {
					$result = [
						"error" => true,
						"reason" => "Hey so this is temporarily disabled for non admins... Testing new uploader system :P"
					];
				}*/
				
				if(isset($result)) {
					if($result['error']) {
						$_SESSION['ANORRL$CreateAsset$Error'] = true;
						$_SESSION['ANORRL$CreateAsset$Result'] = $result['reason'];
					} else {
						$_SESSION['ANORRL$CreateAsset$Error'] = false;
						$_SESSION['ANORRL$CreateAsset$Result'] = $result['id'];
					}
					
					die(header("Location: /create/".$type));
				}
			}
		} else {
			die("Not valid type...");
		}
	}

	$page = new Page("Create", "my/create");

	$page->addStylesheet("/css/new/create.css");
	$page->addStylesheet("/css/new/stuff.css?v=1");

	$page->addScript("/js/create.js?t=1776011774");
	$page->loadHeader();
?>
<div class="Asset" template>
	<a id="NameAndThumbs">
		<img src="">
		<div id="Pricing">
			<span id="Cones" ><img src="/images/icons/traffic_cone.png" > <span id="Costing"></span></span>
			<span id="Lights"><img src="/images/icons/traffic_light.png"> <span id="Costing"></span></span>
		</div>
		<span>AssetName</span>
	</a>
</div>
<div id="StuffContainer">
	<h1>Creation Panel</h1>
	<div id="StuffNavigation">							
		<ul>
			<li data_category="8" ><a>Hats</a></li>
			<li data_category="18"><a>Faces</a></li>
			<li data_category="11"><a>Shirts</a></li>
			<li data_category="2" ><a>T-Shirts</a></li>
			<li data_category="12"><a>Pants</a></li>
			<hr>
			<li data_category="3" ><a>Audio</a></li>
			<li data_category="13"><a>Decals</a></li>
			<li data_category="10"><a>Models</a></li>
			<li data_category="4"><a>Meshes</a></li>
			
			<hr>
			<li data_category="19"><a>Gears</a></li>
			<?php if($user->isAdmin()): ?>
			<li data_category="32"><a>Packages</a></li>
			<hr>
			<li data_category="1"><a>Images</a></li>
			<li data_category="5"><a>Lua</a></li>
			<?php endif ?>
			<hr>
			<li data_category="24"><a>Animations</a></li>
			
		</ul>
	</div><div id="CreationPanel">	
		<div id="UploadPanel">
			<h3>Upload <span id="TypaLabel"></span></h3>
			<?php if(isset($_SESSION['ANORRL$CreateAsset$Error']) && isset($_SESSION['ANORRL$CreateAsset$Result'])): ?>
				<?php if($_SESSION['ANORRL$CreateAsset$Error']): ?>
				<div id="ErrorTime">Error: <span id="Message"><?= $_SESSION['ANORRL$CreateAsset$Result'] ?></span></div>
				<?php else: ?>
				<div id="SuccessTime">Success! <span id="Message"><?= "Check it out <a href=\"/".Asset::FromID($_SESSION['ANORRL$CreateAsset$Result'])->getUrl()."\">here!</a>"?></span></div>
				<?php endif ?>
			<?php endif ?>
			<style>
				.Rules {
					background: #1a1a1a;
					padding-bottom: 5px;
					border-bottom: 2px solid black;
				}

				.Rules h4 {
					margin: 0px;
					width: 636px;
					background: #111;
					border-bottom: 2px solid black;
				}

				.Rules ul {
					margin-bottom: 0px;
				}
			</style>
			<div id="HatUploadRules" class="Rules">
				<h4>Hat Uploading Rules:</h4>
				<ol>
					<li>do not use this to upload gears</li>
					<li>do not make a hat that alters the gameplay that can give you an advantage</li>
					<li>if you are adding particle effects, DO NOT HAVE IT BE SUPER OBSTRUCTIVE</li>
					<li>don't use the uploader to upload character meshes</li>
				</ol>
				<div style="margin: 15px;white-space: normal;">
					<p><b>clarification on the 3rd rule:</b></p>	
					<p>stuff like <a href="/images/hatuploaderexample.png" target="_blank">this</a> is fine, what i meant was if the sparkles were like super massive and blocked the view of everything and everyone </p>
				</div>
			</div>
			<div id="GearUploadRules" class="Rules">
				<h4>Gear Uploading Rules:</h4>
				<ol>
					<li>do not upload gears that actively damage a game e.g build tools</li>
					<li>do not upload gears that actively harm players e.g swords or guns</li>
				</ol>
			</div>
			<form method="POST" enctype="multipart/form-data">
				<table>
					<tr>
						<td>Name</td>
						<td><input type="text" name="ANORRL$CreateAsset$Name" minlength="3" maxlength="100" required></td>
					</tr>
					<tr>
						<td>Description</td>
						<td><textarea name="ANORRL$CreateAsset$Description" maxlength="1000"></textarea></td>
					</tr>
					<tr>
						<td>File</td>
						<td><label for="files">Choose file</label><input id="files" style="display:none;" type="file"  name="ANORRL$CreateAsset$File" required><label id="filename">No file chosen</label></td>
					</tr>
					<tr>
						<td><span style="margin-top: 8px;display: block;">Public</span></td>
						<td><input name="ANORRL$CreateAsset$Public" type="checkbox" style="margin-top: 8px;" checked></td>
					</tr>
					<tr>
						<td>Comments</td>
						<td><input name="ANORRL$CreateAsset$CommentsEnabled" type="checkbox" checked></td>
					</tr>
					<tr>
						<td>On Sale</td>
						<td><input name="ANORRL$CreateAsset$OnSale" type="checkbox"></td>
					</tr>
					<tr>
						<td><input type="submit" value="Upload" style="margin-top:10px" name="ANORRL$CreateAsset$Submit" onclick="$(this).attr('disabled', 'true'); document.forms[0].submit()"></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="AssetsContainer" style="border-top: 2px solid black">
			<div id="StatusText">
				<b id="Loading" style="display: none">Loading assets...</b>
				<b id="NoAssets" style="display: none"><img src="/images/noassets.png" style="width: 110px;display: block;margin: 0 auto;margin-bottom: -92px;margin-top: 23px;">You have no <span id="AssetType"></span>!</b>
			</div>
		
			<table hidden></table>

			<div id="Paginator" style="display: none">
				<a href="javascript:ANORRL.Create.DeadvancePager()" id="PrevPager">&lt;&lt;Previous</a> Page <input maxlength="4"> of <span id="Pages">1</span> <a href="javascript:ANORRL.Create.AdvancePager()" id="NextPager">Next&gt;&gt;</a>
			</div>
		</div>
	</div>
	
</div>

<?php
	$page->loadFooter();
	unset($_SESSION['ANORRL$CreateAsset$Error']);
	unset($_SESSION['ANORRL$CreateAsset$Result']);
?>