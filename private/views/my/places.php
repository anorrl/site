<?php
	use anorrl\utilities\ClientDetector;

	if(!SESSION) {
		die(header("Location: /login"));
	}

	$user = SESSION->user;

	$isclient = ClientDetector::IsAClient();
	if(!$isclient)
		die("Hey something isn't right here... You sure you're using the right studio?");
	
	$places = $user->GetPlaces(false);
	$teamplaces = $user->GetPlaces(true);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Welcome</title>
		<link rel="stylesheet" href="/js/ide/welcome/FetchCSS.css">
		<script type="text/javascript" src="/js/core/jquery.js"></script>
		<script type="text/javascript" src="/js/core/json2.min.js"></script>
		<script type="text/javascript" src="/js/ide/welcome/MicrosoftAjax.js"></script>
		<script type="text/javascript" src="/js/ide/welcome/240ed1621e8429eeafce9b709e38ab5b.js"></script>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		
		
		<script type="text/javascript">
			function editTemplateInStudio(play_placeId) {
				window.external.StartGame("http://arl.lambda.cam/","http://arl.lambda.cam/","http://arl.lambda.cam/game/edit.ashx?placeId=" + play_placeId);
			}
			function editrecentfile(recentpath) {
				window.external.StartGame("","", recentpath);	
			}
		</script>
	</head>
	<body id="StudioWelcomeBody">
		<div class="header">
			<div id="header-login-wrapper" class="iframe-login-signup" data-display-opened="">
				<p>So... you're <b><?= $user->name ?></b>?</p>
			</div>
			<!-- This is only after the login stuff because IE7 demands floated elements be before non-floated -->
			<img src="/images/ide/studio_title.png" alt="ANORRL Studio Title">
			<!-- <p id="HomeLink">
				<a class="text-link" href="https://web.archive.org/web/20130715023235/http://roblox.com/Build/Default.aspx">Switch to Classic View</a>
			</p> -->
		</div>
		<div class="container">
			<div class="navbar" style="height: 581px;">
				<ul class="navlist" style="border-bottom: medium;">
					<li id="NewProject" class="navselected">
						<p>New Project</p>
					</li>
					<li id="MyProjects" class="lastnav">
						<p>My Projects</p>
					</li>
					<!--li class="lastnav"><p>Recent News</p></li-->
				</ul>
			</div>
			<div class="main">
				<div id="TemplatesView" class="welcome-content-area" style="display: block; height: 535px;">
					<h2 id="StudioGameTemplates">GAME TEMPLATES</h2>
					<div class="templatetypes">
						<ul class="templatetypes">
							<li js-data-templatetype="Basic" class="selectedType">
								<a href="#Basic">Basic</a>
							</li>
							<li js-data-templatetype="Strategy">
								<a href="#Strategy">Strategy</a>
							</li>
							<li js-data-templatetype="Action">
								<a href="#Action">Action</a>
							</li>
						</ul>
						<!--div class="tool-tip"><img alt="Recommended for users new to ROBLOX studio" src="/images/IDE/img-tail-top.png" class="top" /><p>Recommended for users new to ROBLOX studio</p><a class="closeButton"></a></div -->
					</div>
					<div class="templates" js-data-templatetype="Basic" style="display: block;">
						<div class="template" placeid="123">
							<a class="game-image">
								<img src="/images/ide/welcome/baseplate.png">
							</a>
							<p>Baseplate</p>
						</div>
						<div class="template" placeid="124">
							<a class="game-image">
								<img src="/images/ide/welcome/flatterrain.png">
							</a>
							<p>Flat Terrain</p>
						</div>
					</div>
					<div class="templates" js-data-templatetype="Strategy">
						<div class="template" placeid="92721754">
							<a class="game-image">
								<img src="/images/ide/welcome/capturetheflag.jpg">
							</a>
							<p>Capture The Flag</p>
						</div>
						<div class="template" placeid="95269276">
							<a class="game-image">
								<img src="/images/ide/welcome/controlpoints.jpg">
							</a>
							<p>Control Points</p>
						</div>
					</div>
					<div class="templates" js-data-templatetype="Action">
						<div class="template" placeid="92721884">
							<a class="game-image">
								<img src="/images/ide/welcome/freeforall.jpg">
							</a>
							<p>Free For All</p>
						</div>
						<div class="template" placeid="95205458">
							<a class="game-image">
								<img src="/images/ide/welcome/teamdeathmatch.jpg">
							</a>
							<p>Team Deathmatch</p>
						</div>
					</div>
				</div>
				<div id="MyProjectsView" class="welcome-content-area" style="display: none;">
					<h2>My Published Projects</h2>
					<div class="templates" style="display: block;">
						<?php if(count($teamplaces) != 0): ?>
						<div><h3>Collaborative Projects</h3></div>
						<?php
							foreach($teamplaces as $place) {
								if($place instanceof Asset) {
									$place_id = $place->id;
									$place_name = $place->name;
									echo <<<EOT
									<div class="template" placeid="$place_id">
										<a class="game-image">
											<img width="197" src="/thumbs/?id=$place_id&sx=197&sy=111">
										</a>
										<p>$place_name</p>
									</div>
									EOT;
								}
							}
							
						?>
						<div><h3>Your Projects</h3></div>
						<?php endif ?>
						<?php
							
							if(count($places) != 0) {
								foreach($places as $place) {
									if($place instanceof Place) {

										$place_id = $place->id;
										$place_name = $place->name;

										echo <<<EOT
										<div class="template" placeid="$place_id">
											<a class="game-image">
												<img width="197" src="/thumbs/?id=$place_id&sx=197&sy=111">
											</a>
											<p>$place_name</p>
										</div>
										EOT;
									}
								}
							} else {
								echo "<div><span>You have no published projects!</span></div>";
							}
						?>
						
					</div>
				</div>
				<div id="ButtonRow" class="divider-top divider-left divider-bottom">
					<a class="btn-medium btn-primary" id="EditButton">Edit <span class="btn-text">Edit</span>
					</a>
					<a class="btn-medium btn-primary" id="BuildButton">Build <span class="btn-text">Build</span>
					</a>
					<a class="btn-medium btn-negative" id="CollapseButton">Cancel <span class="btn-text">Cancel</span>
					</a>
				</div>
			</div>
		</div>
		<div class="GenericModal modalPopup unifiedModal smallModal" style="display:none;">
			<div class="Title"></div>
			<div class="GenericModalBody">
				<div>
					<div class="ImageContainer">
						<img class="GenericModalImage" alt="generic image">
					</div>
					<div class="Message"></div>
				</div>
				<div class="clear"></div>
				<div id="GenericModalButtonContainer" class="GenericModalButtonContainer">
					<a class="ImageButton btn-neutral btn-large roblox-ok">OK <span class="btn-text">OK</span></a>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			$(function() {
				Roblox.Client.Resources = {
					here: "here",
					youNeedTheLatest: "You need Our Plugin for this.  Get the latest version from Ohio",
					plugInInstallationFailed: "Plugin installation failed!",
					errorUpdating: "Error updating: "
				};
				if (typeof Roblox.IDEWelcome === "undefined") Roblox.IDEWelcome = {};
				Roblox.IDEWelcome.Resources = {
					openProject: "Open Project",
					openProjectText: "To open your project, open to this page in ",
					robloxStudio: "ANORRL Studio",
					editPlace: "Edit Place",
					toEdit: "To edit ",
					openPage: ", open to this page in ",
					buildPlace: "Build Place",
					toBuild: "To build on ",
					placeInactive: "Place Inactive",
					activate: ", activate this place by going to File->My Published Projects.",
					emailVerifiedTitle: "Verify Your Email",
					emailVerifiedMessage: "You must verify your email before you can work on your place. You can verify your email on the  < a href = '/My/Account.aspx?confirmemail=1' > Account < /a> page.",
					verify: "Verify",
					cancel: "Cancel"
				};
			});
		</script>
		<div class="ConfirmationModal modalPopup unifiedModal smallModal" data-modal-handle="confirmation" style="display:none;">
			<a class="genericmodal-close ImageButton closeBtnCircle_20h"></a>
			<div class="Title"></div>
			<div class="GenericModalBody">
				<div class="TopBody">
					<div class="ImageContainer roblox-item-image" data-image-size="small" data-no-overlays="" data-no-click="">
						<img class="GenericModalImage" alt="generic image">
					</div>
					<div class="Message"></div>
				</div>
				<div class="ConfirmationModalButtonContainer">
					<a href="" roblox-confirm-btn="">
						<span></span>
					</a>
					<a href="" roblox-decline-btn="">
						<span></span>
					</a>
				</div>
				<div class="ConfirmationModalFooter"></div>
			</div>
			<script type="text/javascript">
				Roblox.GenericConfirmation.Resources = {
					yes: "Yes",
					No: "No"
				}
			</script>
		</div>
	</body>
</html>