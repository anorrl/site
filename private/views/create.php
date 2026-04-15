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
		"hats",
		"faces",
		"shirts",
		"tshirts",
		"pants",
		"gears",

		"audio",
		"decals",
		"models",
		"meshes",
		"animations",

		"images",
		"lua",
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

	if(!in_array($type, $validtypes))
		die(header("Location: /create/hats"));

	if(count($_POST) != 0) {
		if(in_array($type, $validtypes)) {
			if(isset($_POST['ANORRL$CreateAsset$Name']) &&
				isset($_POST ['ANORRL$CreateAsset$Description']) &&
				isset($_FILES['ANORRL$CreateAsset$File'])
			) {
				
				$result = null;
				$name = trim($_POST['ANORRL$CreateAsset$Name']);

				$description = trim($_POST['ANORRL$CreateAsset$Description']);
				$public = isset($_POST['ANORRL$CreateAsset$Public']);
				$comments_enabled = isset($_POST['ANORRL$CreateAsset$CommentsEnabled']);
				$on_sale = isset($_POST['ANORRL$CreateAsset$OnSale']);

				$result = AssetUploader::UploadAsset($_FILES['ANORRL$CreateAsset$File'], $types[$type], $name, $description, $public, $on_sale, $comments_enabled);
				
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

	$_SESSION['ANORRL$CreateAsset$Error'] = false;
	$_SESSION['ANORRL$CreateAsset$Result'] = 2;

	$page = new Page("Create", "my/create");

	$page->addStylesheet("/css/new/create.css?v=2");
	$page->addStylesheet("/css/new/stuff.css?v=2");
	$page->addStylesheet("/css/new/forms.css?v=1");

	$page->addScript("/js/create.js?t=1776187959");
	$page->loadHeader();
?>
<div class="Asset" template>
	<a id="NameAndThumbs">
		<img src="">
		<div id="Pricing"></div>
		<span>AssetName</span>
	</a>
</div>
<style>
	.Window {
		width: fit-content;
		background: #222;
	}

	.Window #Name {
		background: black;
		padding: 5px;
		text-align: center;
		letter-spacing: 2px;
		font-weight: bold;
		user-select: none;
	}

	.Window #Contents {
		padding: 10px;
		border: 2px solid black;
	}

	.Window input[type="number"] {
		border: 2px solid black;
		background: #444;
		padding: 2px 4px;
		color: white;
		width: 125px;
	}

	.Window table td {
		vertical-align: middle !important;
	}

	.Window ul {
		margin-bottom: 0px;
	}

	.Window li {
		padding: 5px;
		border-bottom: 2px solid black;
	}

	.Window li:nth-child(even) {
		background: #0a0a0a;
	}

	.Window li:last-child {
		border: none;
	}

	.Window[id] {
		margin-bottom: 10px;
	}

	.Window ul {
		list-style: none;
		padding-left: 0px;
		background: #1a1a1a;
		border: 2px solid black;
		margin-top: 0px;
	}

	.Window .Number {
		margin: 0px 5px;
		margin-right: 10px;
	}
</style>
<style>
	#CreationPanel #UploadPanel {
		background: linear-gradient(#0a0a0a,#1a1a1a);
	}

	#StuffNavigation ul {
		background: linear-gradient(#222, #111);
	}

	
	/*#StuffNavigation li {
		border-bottom: 2px solid black;
		padding: 3px 15px;
	}

	#StuffNavigation li:nth-child(even) {
		background: #111;
	}

	#StuffNavigation li:last-child {
		border: none;
	}

	#StuffNavigation ul {
		padding: 0px;
		*margin: 15px;
	}

	#StuffNavigation li hr {
		height: 0px;
		margin: 0px;
	}*/

	#StuffNavigation li:hover a {
		text-decoration: underline;
		color: #ffc63f;
	}

	.RequiredThing {
		color: red;
		font-weight: bold;
		user-select: none;
	}
	
</style>
<script>
	$(function() {
		$(".RequiredThing").each(function() {
			$(this).attr("title", "This is required!");
		});
	})
</script>
<div id="StuffContainer">
	<h1 style="width: 834px;">
		<marquee scrollamount="20" direction="right" behavior="alternate">Creation Panel</marquee>
		<marquee scrollamount="20" behavior="alternate" style="display: block;margin-top: -33px;z-index: 9;" direction="left">Creation Panel</marquee>
	</h1>
	<div id="StuffNavigation">	
		<h4>Categories</h4>						
		<ul>
			<li data_category="8" ><a>Hats</a></li>
			<li data_category="18"><a>Faces</a></li>
			<li data_category="11"><a>Shirts</a></li>
			<li data_category="2" ><a>T-Shirts</a></li>
			<li data_category="12"><a>Pants</a></li>
			<li data_category="19"><a>Gears</a></li>
			<hr>
			<li data_category="13"><a>Decals</a></li>
			<li data_category="3"><a>Audio</a></li>
			<li data_category="4"><a>Meshes</a></li>
			<li data_category="10"><a>Models</a></li>
			<li data_category="24"><a>Animations</a></li>
			
			<?php if($user->isAdmin()): ?>
			<hr>
			
			<li data_category="1"><a>Images</a></li>
			<li data_category="5"><a>Lua</a></li>
			<?php endif ?>
		</ul>
	</div><div id="CreationPanel">	
		<div id="UploadPanel">
			<div class="Window" id="HatUploadRules" style="width: 100%;">
				<div id="Name">Hat Uploading Rules</div>
				<div id="Contents">
					<ul>
						<li><span class="Number">1.</span>do not use this to upload gears</li>
						<li><span class="Number">2.</span>do not make a hat that alters the gameplay that can give you an advantage</li>
						<li><span class="Number">3.</span>if you are adding particle effects, DO NOT HAVE IT BE SUPER OBSTRUCTIVE</li>
						<li><span class="Number">4.</span>don't use the uploader to upload character meshes</li>
						<li><span class="Number">5.</span>don't reupload other people's hats. that wouldn't be nice!</li>
					</ul>
					
					<div style="margin: 15px;">
						<p><b>clarification on the 3rd rule:</b></p>	
						<p>stuff like <a href="/images/hatuploaderexample.png" target="_blank">this</a> is fine, what i meant was if the sparkles were like super massive and blocked the view of everything and everyone </p>
					</div>
				</div>
			</div>

			<div class="Window" id="GearUploadRules" style="width: 100%;">
				<div id="Name">Gear Uploading Rules</div>
				<div id="Contents" style="padding: 0px">
					<ul style="border: none">
						<li><span class="Number">1.</span>do not upload gears that actively damage a game e.g build tools</li>
						<li><span class="Number">2.</span>do not upload gears that actively harm players e.g swords or guns</li>
					</ul>
				</div>
			</div>
			
			<form method="POST" enctype="multipart/form-data" style="">
				<div class="Window" style="width: 100%;">
					<div id="Name">Upload <span id="TypaLabel"></span></div>
					
					<div id="Contents">
						<?php if(isset($_SESSION['ANORRL$CreateAsset$Error']) && isset($_SESSION['ANORRL$CreateAsset$Result'])): ?>
							<?php if($_SESSION['ANORRL$CreateAsset$Error']): ?>
							<div id="ErrorTime" style="margin: -10px;margin-bottom: 10px;">Error: <span id="Message"><?= $_SESSION['ANORRL$CreateAsset$Result'] ?></span></div>
							<?php else: 
								$uploaded_asset = Asset::FromID($_SESSION['ANORRL$CreateAsset$Result']);
								?>
								<?php if(true): ?>
									<div id="SuccessTime" style="margin: -10px;margin-bottom: 10px;">You've successfully uploaded &quot;<?= $uploaded_asset->name ?>&quot;! <span id="Message">Check it out <a href="/"<?= $uploaded_asset->getUrl() ?>">here</a>!  <a href="javascript:copyToClipboard(<?= $uploaded_asset->getAssetIDSafe() ?>)">(Copy Asset ID)</a></div>
								<?php else: ?>
									<!-- Other iterations. -->
									<div id="SuccessTime" style="margin: -10px;margin-bottom: 10px;">You've successfully uploaded &quot;<a href="/"<?= $uploaded_asset->getUrl() ?>"><?= $uploaded_asset->name ?></a>&quot;! <span id="Message">(<a href="javascript:copyToClipboard(<?= $uploaded_asset->getAssetIDSafe() ?>)">Copy Asset ID</a>)</div>
									<div id="SuccessTime" style="margin: -10px;margin-bottom: 10px;">You've successfully uploaded a <?= $uploaded_asset->type->label() ?>! <span id="Message">Check it out <a href="/"<?= $uploaded_asset->getUrl() ?>">here!</a></span> (<a href="javascript:copyToClipboard(<?= $uploaded_asset->getAssetIDSafe() ?>)">Copy Asset ID</a>)</div>
								<?php endif ?>
							<?php endif ?>
						<?php endif ?>
						<table style="width: 100%">
							<tr>
								<td style="width: 70px;">Name <span class="RequiredThing">*</span></td>
								<td><input type="text" name="ANORRL$CreateAsset$Name" minlength="3" maxlength="100" required placeholder></td>
							</tr>
							<tr>
								<td>Description</td>
								<td><textarea name="ANORRL$CreateAsset$Description" maxlength="1000"></textarea></td>
							</tr>
							<tr>
								<td>File <span class="RequiredThing">*</span></td>
								<td>
									<label for="files" style="margin-top: 5px;display: inline-block;">Choose file</label>
									<input id="files" style="display:none;" type="file"  name="ANORRL$CreateAsset$File" required>
									<label id="filename">No file chosen</label>
								</td>
							</tr>
							<tr>
								<td><span style="margin-top: 10px;display: block;">Extras</span></td>
								<td>
									<div class="Window" style="margin-top: 5px; ">
										<div id="Name">Toggles</div>
										<div id="Contents">
											<table style="float: left;">
												<tr>
													<td><label for="ANORRL_CreateAsset_Public">Public</label></td>
													<td><input id="ANORRL_CreateAsset_Public" name="ANORRL$CreateAsset$Public" type="checkbox" checked=""></td>
												</tr>
												<tr>
													<td><label for="ANORRL_CreateAsset_CommentsEnabled">Comments</label></td>
													<td><input id="ANORRL_CreateAsset_CommentsEnabled" name="ANORRL$CreateAsset$CommentsEnabled" type="checkbox" checked=""></td>
												</tr>
												<tr>
													<td><label for="ANORRL_CreateAsset_Purchasable">Purchasable</label></td>
													<td><input type="checkbox" checked="" id="ANORRL_CreateAsset_Purchasable" name="ANORRL$CreateAsset$Purchasable"></td>
												</tr>
											</table>
											
											<div style="display: inline;">
												<img src="/images/jane.png" style="width: 70px;margin-left: 10px;border: 2px solid black;">
											</div>
											
											<div style="clear: both;"></div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" value="Upload" style="margin-top:5px" name="ANORRL$CreateAsset$Submit" onclick="$(this).attr('disabled', 'true'); document.forms[0].submit()"></td>
							</tr>
						</table>
						<div style="font-size: 10px; color: #ccc; font-style: italic; margin-top: 5px;" title="You need to fill those out!"><span class="RequiredThing">*</span> means required fields!</div>
					</div>
				</div>

				
			</form>
			<script>
				function toggleTemplate() {
					if($("#ShowHideTemplate").parent().parent().find("#Contents").is(":visible")) {
						$("#ShowHideTemplate").parent().parent().find("#Contents").css("display", "none");
						$("#ShowHideTemplate").html("(Show)");
					} else {
						$("#ShowHideTemplate").parent().parent().find("#Contents").css("display", "block");
						$("#ShowHideTemplate").html("(Hide)");
					}
				}
			</script>
			<div class="Window" style="display: none; margin: 0 auto; margin-top: 10px; margin-bottom: 0px;" id="ShirtPantsTemplate">
				<div id="Name" style="min-width: 328px;"><span id="Title"></span><a id="ShowHideTemplate" href="javascript:toggleTemplate()">(Show)</a></div>
				<div id="Contents" style="display: none;">
					<a download="" href="" title="Click to download!">
						<img alt="Click to download!" src="" height="300">
					</a>
				</div>
			</div>
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