<?php 
	use anorrl\utilities\AssetUploader;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\UserUtils;
	use anorrl\utilities\UtilUtils;

	if(!SESSION) {
		die("Hey have you tried logging in before doing this? <br><a href='javascript:window.close()'>No...</a>");
	}

	$user = SESSION->user;

	function FunnyBoolToStr(bool $value) {
		return $value ? "True" : "False";
	}

	$verifiedcrap = false;

	if(
		isset($_POST['ANORRL$IDE$Publish$Place$Name']) && 
		isset($_POST['ANORRL$IDE$Publish$Place$Description']) && 
		isset($_POST['ANORRL$IDE$Publish$Place$ServerSize']) && 
		isset($_POST['ANORRL$IDE$Publish$Place$Submit'])
	) {

		$name = UtilUtils::StripUnicode($_POST['ANORRL$IDE$Publish$Place$Name']);
		$description = UtilUtils::StripUnicode($_POST['ANORRL$IDE$Publish$Place$Description']);
		
		$isclient = ClientDetector::IsAClient();

		if(!$isclient)
			die("Hey something isn't right here... You sure you're using the right studio?");

		$server_size = intval($_POST['ANORRL$IDE$Publish$Place$ServerSize']) <= 0 ? 12 : intval($_POST['ANORRL$IDE$Publish$Place$ServerSize']);

		$allUsersCount = count(UserUtils::GetAllUsers());

		if($server_size > $allUsersCount) {
			$server_size = $allUsersCount;
		}

		$isPublic =        isset($_POST['ANORRL$IDE$Publish$Place$ServerSize']);
		$commentsEnabled = isset($_POST['ANORRL$IDE$Publish$Place$ServerSize']);
		$isCopylocked =    isset($_POST['ANORRL$IDE$Publish$Place$Copylocked']);
		$gears =           isset($_POST['ANORRL$IDE$Publish$Place$GearsEnabled']);
		$original =        isset($_POST['ANORRL$IDE$Publish$Place$IsOriginal']);
		

		if(strlen($name) < 4) {
			die("Name must not be less than 4 characters!");
		}
	
		$result = AssetUploader::CreatePlace($name, $description, $isPublic, $commentsEnabled, $server_size, $isCopylocked, $gears, $original, $user);
		
		if(!$result['error']) {
			$place_verified_id = $result['id'];
			$verifiedcrap = true;
		} else {

			$errorReason = $result['reason'];
			$verifiedcrap = false;
			die("<script>window.alert(\"$errorReason\");</script>");
		}
		
	}

	
?>
<?php if(!$verifiedcrap): ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Publish Place - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/public/css/new/main.css">
		<link rel="stylesheet" href="/public/css/new/publish.css">
		<script src="/public/js/core/jquery.js"></script>
		<script src="/public/js/main.js?t=1776011774"></script>
	</head>
	<body>
		<div id="Container">
			<div id="Body">
				<div id="BodyContainer">
					<div id="PublishContainer">
						<h2>Publish your lovely little place...</h2>
						<div id="ItemDetails" style="background: #222">
							<form method="POST">
								<div id="DetailStack">
									<h4>Information</h4>
									<table>
										<tr>
											<td>Name</td>
											<td><input type="text" name="ANORRL$IDE$Publish$Place$Name" value="My Place" minlength="3" maxlength="128"></td>
										</tr>
										<tr>
											<td>Description</td>
											<td><textarea style="height: 50px;" name="ANORRL$IDE$Publish$Place$Description"></textarea></td>
										</tr>
										<tr>
											<td>Public</td>
											<td><input type="checkbox" name="ANORRL$IDE$Publish$Place$PublicBox" checked></td>
										</tr>
										<tr>
											<td>Enable Comments</td>
											<td><input type="checkbox" name="ANORRL$IDE$Publish$Place$CommentsBox" checked></td>
										</tr>
									</table>
								</div>
								<div id="DetailStack">
									<h4 style="margin-top: 10px">Place Settings</h4>
									<table>
										<tr>
											<td>Server Size</td>
											<td><input type="number" name="ANORRL$IDE$Publish$Place$ServerSize" value="12"></td>
										</tr>
										<tr>
											<td>Copylocked</td>
											<td><input type="checkbox" name="ANORRL$IDE$Publish$Place$Copylocked" checked></td>
										</tr>
										<tr>
											<td>Gears Enabled</td>
											<td><input type="checkbox" name="ANORRL$IDE$Publish$Place$GearsEnabled"></td>
										</tr>
										<tr>
											<td>Original</td>
											<td><input type="checkbox" name="ANORRL$IDE$Publish$Place$IsOriginal"></td>
										</tr>
									</table>
									<input type="submit" value="Publish" name="ANORRL$IDE$Publish$Place$Submit" style="text-align: center">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php else: ?>
<?php
	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}

	// Prevent user from uploading the same place again by refreshing.
	if(isset($_SESSION['HasUploaded']) && $_SESSION['HasUploaded']) {
		$_SESSION['HasUploaded'] = false;
		die("<script>window.close()</script>");
	}


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Upload</title>
		<link href="/public/css/RobloxOld.css" rel="stylesheet" type="text/css" />
	</head>
	<body bgcolor="buttonface" scroll="no">
		<form name="PublishContent" method="post" action="Upload.aspx" id="PublishContent">
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
			<script type="text/javascript">
				function uploadData()
				{
					try
					{
						window.external.SaveUrl('http://<?= $_SERVER['SERVER_NAME'] ?>/Data/Upload.ashx?assetid=<?= $place_verified_id ?>&type=Place&name=<?= urlencode($name) ?>&description=<?= urlencode($description) ?>&ispublic=<?= FunnyBoolToStr($isPublic) ?>&commentsenabled=<?= FunnyBoolToStr($commentsEnabled) ?>&serversize=<?= $server_size ?>&iscopylocked=<?= FunnyBoolToStr($isCopylocked) ?>');
						document.getElementById("Uploading").style.display='none';
						document.getElementById("Confirmation").style.display='block';
					}
					catch (ex)
					{
						try
						{
							window.external.SaveUrl('http://<?= $_SERVER['SERVER_NAME'] ?>/Data/Upload.ashx?assetid=<?= $place_verified_id ?>&type=Place&name=<?= urlencode($name) ?>&description=<?= urlencode($description) ?>&ispublic=<?= FunnyBoolToStr($isPublic) ?>&commentsenabled=<?= FunnyBoolToStr($commentsEnabled) ?>&serversize=<?= $server_size ?>&iscopylocked=<?= FunnyBoolToStr($isCopylocked) ?>');
							document.getElementById("Uploading").style.display='none';
							document.getElementById("Confirmation").style.display='block';
						}
						catch (ex2)
						{
							document.getElementById("Uploading").style.display='none';
							document.getElementById("Failure").style.display='block';
						}
					}
				}
				window.setTimeout("uploadData()", 1000);
			</script>
		</form>
	</body>
</html>
<?php
	$_SESSION['HasUploaded'] = true;
?>
<?php 
die();
endif ?>