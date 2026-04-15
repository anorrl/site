<?php 
	use anorrl\enums\AssetType;

	if(!SESSION)
		die("Hey have you tried logging in before doing this? <br><a href='javascript:window.close()'>No...</a>");
	else 
		$user = SESSION->user;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Publish to a Place - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/public/css/new/main.css?v=1">
		<link rel="stylesheet" href="/public/css/new/publish.css">
		<script src="/public/js/core/jquery.js"></script>
		<script src="/public/js/main.js?t=1776250887"></script>
		<script src="/public/js/publish.js"></script>
	</head>
	<body domain="<?= CONFIG->domain ?>">
		<div id="Container">
			<div id="Body">
				<div id="BodyContainer">
					<div id="PublishContainer">
						<h2>Publish your lovely little place...</h2>
						<div id="ItemDetails">
							<form method="POST">
								<input name="ANORRL$IDE$Publish$Place$Action" hidden>
								<div id="PublishPlaces">
									<div class="Place" data-placeid="createnew">
										<img src="/public/images/ide/createnewplace.png">
										<span>Create a New Place</span>
									</div>
									<?php $places = $user->getOwnedAssets(AssetType::PLACE); foreach($places as $place): ?>
										<div class="Place" data-placeid="<?= $place->id ?>">
											<img src="<?= $place->getThumbsUrl(261, 149) ?>">
											<span><?= $place->name ?></span>
										</div>
									<?php endforeach ?>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<link href="/public/css/RobloxOld.css" rel="stylesheet" type="text/css" />
		<form style="display:none;padding:15px;" scroll="no" name="PublishContent" id="PublishContent">
			<input id="DialogResult" type="hidden" />
			<div id="Uploading" style="DISPLAY: block; FONT-WEIGHT: bold; COLOR: royalblue">Uploading. Please wait...</div>
			<div id="Confirmation" style="display: none;">
				<table height="100%" width="100%">
					<tr valign="top" height="100%">
						<td>The upload has completed!</td>
					</tr>
					<tr>
						<td align="right">
							<table cellspacing="5" cellpadding="0" border="0">
								<tr>
									<td><input class="OKCancelButton" onclick="window.close(); return false" type="button" value="Close" /></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div id="Failure" style="display: none;">
				<p>The upload has failed.</p>
			</div>
		</form>
	</body>
</html>