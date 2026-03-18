<?php 
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die("Hey have you tried logging in before doing this? <br><a href='javascript:window.close()'>No...</a>");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Publish to a Place - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/publish.css">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/publish.js"></script>
	</head>
	<body>
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
										<img src="/images/ide/createnewplace.png">
										<span>Create a New Place</span>
									</div>
									<?php 
										$places = $user->GetOwnedAssets(AssetType::PLACE);
										
										if(count($places) != 0) {
											foreach($places as $place) {
												$place_id = $place->id;
												$place_name = $place->name;
												echo <<<EOT
												<div class="Place" data-placeid="$place_id">
													<img src="/thumbs/?id=$place_id&sx=261&sy=149">
													<span>$place_name</span>
												</div>
												EOT;
											}
										}
									?>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<link href="/css/RobloxOld.css" rel="stylesheet" type="text/css" />
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