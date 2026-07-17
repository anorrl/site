<?php
	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\Page;
	use anorrl\enums\AssetType;
	use anorrl\utilities\AssetUploader;

	if(isset($type)) {
		$type = trim(strtolower($type));
	}

	$user = SESSION->user;
	
	$validtypes = [
		"badge"
	];

	$types = [
		"badge" => AssetType::BADGE
	];

	if(!in_array($type, $validtypes))
		redirect("/my/stuff");

	if($type == "badge" &&
		isset($_POST['ANORRL$CreateAsset$Name']) &&
		isset($_POST['ANORRL$CreateAsset$Description']) &&
		isset($_FILES['ANORRL$CreateAsset$File'])
	) {
		$result = null;
		$name = trim($_POST['ANORRL$CreateAsset$Name']);

		$description = trim($_POST['ANORRL$CreateAsset$Description']);

		$result = AssetUploader::UploadAsset($_FILES['ANORRL$CreateAsset$File'], AssetType::BADGE, $name, $description, false, true, false);

		if(isset($result)) {
			if($result['error']) {
				$_SESSION['ANORRL$CreateAsset$Error'] = true;
				$_SESSION['ANORRL$CreateAsset$Result'] = $result['reason'];
			} else {
				$_SESSION['ANORRL$CreateAsset$Error'] = false;
				$_SESSION['ANORRL$CreateAsset$Result'] = $result['id'];

				Database::singleton()->run(
					"UPDATE `assets` SET `relatedid` = :raid WHERE `id` = :aid",
					[
						":raid" => $place->id,
						":aid" => $result['id']
					]
				);
			}
			redirect($_SERVER['REQUEST_URI']);
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Place Creation Panel - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/public/css/new/main.css?v=5">
		<link rel="stylesheet" href="/public/css/new/create.css?v=2">
		<link rel="stylesheet" href="/public/css/new/stuff.css?v=2">
		<link rel="stylesheet" href="/public/css/new/forms.css?v=1">
		<link rel="stylesheet" href="/public/css/new/window.css">
		<script src="/public/js/core/jquery.js"></script>
		<script src="/public/js/main.js?t=1776250887"></script>
		<script src="/public/js/specialcreate.js?t=1776537578"></script>
		<style>
			h2 {
				margin-top: 0px;
			}
			#BodyContainer {
				border-top: 4px solid black;
			}
		</style>
	</head>
	<body>
		<div id="Container">
			<div id="Body">
				<div id="BodyContainer">
					<div class="Asset" template>
						<a id="NameAndThumbs">
							<img src="">
							<span>AssetName</span>
						</a>
					</div>
					<style>
						#CreationPanel #UploadPanel {
							background: linear-gradient(#0a0a0a,#1a1a1a);
						}

						#StuffNavigation ul {
							background: linear-gradient(#222, #111);
						}

						#StuffNavigation li:hover a {
							text-decoration: underline;
							color: #ffc63f;
						}

						.RequiredThing {
							color: red;
							font-weight: bold;
							user-select: none;
						}
						
						#StuffContainer h4 {
							margin: 0px;
							width: 100%;
							padding: 5px 0px;
							margin-bottom: 10px;
							text-align: center;
						}
					</style>
					<script>
						$(function() {
							$(".RequiredThing").each(function() {
								$(this).attr("title", "This is required!");
							});
						})
					</script>
					<div id="StuffContainer" data-placeid="<?= $place->id ?>" data-studio="true">
						<h1 style="width: 834px;">
							<marquee scrollamount="20" direction="right" behavior="alternate" style="text-align: center">Place Creation Panel</marquee>
						</h1>
						<div id="StuffNavigation">	
							<ul>
								<img src="<?= $place->getThumbsUrl(154, 86); ?>">
								<h4>alias creation maybe</h4>
								<li data_category="21"><a>Badges</a></li>
								<h3><a onclick="window.close()" href="#">>> Go back <<</a></h3>
							</ul>
							
						</div><div id="CreationPanel">	
							<div id="UploadPanel">
								
								<form method="POST" enctype="multipart/form-data" style="">
									<div class="Window" style="width: 100%;">
										<div id="Name"><?= $place->name ?> </div>
										
										<div id="Contents">
											<?php if(isset($_SESSION['ANORRL$CreateAsset$Error']) && isset($_SESSION['ANORRL$CreateAsset$Result'])): ?>
												<?php if($_SESSION['ANORRL$CreateAsset$Error']): ?>
												<div id="ErrorTime" style="margin: -10px;margin-bottom: 10px;">Error: <span id="Message"><?= $_SESSION['ANORRL$CreateAsset$Result'] ?></span></div>
												<?php else: 
													$uploaded_asset = Asset::FromID($_SESSION['ANORRL$CreateAsset$Result']);
													?>
													<div id="SuccessTime" style="margin: -10px;margin-bottom: 10px;">You've successfully uploaded &quot;<?= $uploaded_asset->name ?>&quot;! <span id="Message">Check it out <a href="/"<?= $uploaded_asset->getURL() ?>">here</a>!  <a href="javascript:copyToClipboard(<?= $uploaded_asset->getAssetIDSafe() ?>)">(Copy Asset ID)</a></div>
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
														<label id="files_fakebutton" for="files" style="margin-top: 5px;display: inline-block;">Choose file</label>
														<input id="files" style="opacity: 0; position: absolute;" type="file"  name="ANORRL$CreateAsset$File" required>
														<label id="filename">No file chosen</label>
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
									<b id="NoAssets" style="display: none"><img src="/public/images/noassets.png" style="width: 110px;display: block;margin: 0 auto;margin-bottom: -92px;margin-top: 23px;">You have no <span id="AssetType"></span>!</b>
								</div>
							
								<table hidden></table>

								<div id="Paginator" style="display: none">
									<a href="javascript:ANORRL.Create.DeadvancePager()" id="PrevPager">&lt;&lt;Previous</a> Page <input maxlength="4"> of <span id="Pages">1</span> <a href="javascript:ANORRL.Create.AdvancePager()" id="NextPager">Next&gt;&gt;</a>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php
	unset($_SESSION['ANORRL$CreateAsset$Error']);
	unset($_SESSION['ANORRL$CreateAsset$Result']);
?>
