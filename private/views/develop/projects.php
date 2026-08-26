<?php
	use anorrl\PageOld;
	use anorrl\Universe;
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\UtilUtils;

	$user = SESSION->user;

	$isclient = ClientDetector::IsAClient();
	if(!$isclient)
		die("Hey something isn't right here... You sure you're using the studio?");

	$universe = null;

	if(!isset($_GET['universeId'])) {
		$extra_places = [];
		if(isset($_GET['filepath'])) {
			$raw_crap = explode("&", str_replace("/develop/projects?", "", $_SERVER['REQUEST_URI']));

			$filenames = [];
			$filepaths = [];

			foreach($raw_crap as $bit) {
				if(strlen(trim($bit)) != 0) {
					if(str_starts_with($bit, "filename="))
						$filenames[] = urldecode(str_replace("filename=", "", $bit));
					elseif(str_starts_with($bit, "filepath="))
						$filepaths[] = urldecode(str_replace("filepath=", "", $bit));
				}
			}

			if(count($filenames) == count($filepaths)) {
				for($i = 0; $i < count($filenames); $i++) {
					$extra_places[] = [
						"name" => $filenames[$i],
						"path" => $filepaths[$i]
					];
				}
			}
		}
		//print_r($extra_places);

		$places = $user->getPlaces(false);
		$teamplaces = $user->getPlaces(true);
	} else {
		$universe = Universe::FromID(intval($_GET['universeId']));

		if(!$universe)
			redirect("/develop/projects");

		if(!$universe->hasAccess($user) || count($universe->getAllPlaces()) <= 1)
			redirect("/develop/projects");

		$places = $universe->getAllPlaces();
	}



	$domain = CONFIG->domain;

	$splash = new FileSplasher("didyouknow")->getRandomSplash();

	$page = new PageOld($universe ? $universe->starting_place->name : "ANORRL Studio", "ide/projects");
	$page->addStylesheet("/css/new/my/places.css?v=1");
	$page->loadBasicHeader();
?>
<script src="qrc:/qtwebchannel/qwebchannel.js"></script>
<style>
	.Place {
		margin: 5px;
	}
</style>
<script>
	$(function() {
		$(".Place").on("click", function() {
			var placeid = $(this).data("place-id");
			if(!Number(placeid)) {
				window.location.href = "/develop/projects?universeId="+$(this).data("universe-id");
			} else {
				window.external.StartGame("http://<?= $domain ?>/","http://<?= $domain ?>/","http://<?= $domain ?>/game/edit.slua?placeId=" + placeid);
			}
		});

		function onResizeWindow() {
			var n = $("#Places");

			if($(window).height() < n.height()) {
				$("#Sidebar").height(n.height()+30);
			}
			else {
				if(n.height()+30+114 > $(window).height()) {
					$("#Sidebar").height($(window).height()-40);
				}
				else {
					$("#Sidebar").height($(window).height()-114);
				}
			}

			var j = $("#Places");
			$(window).width() < 300 ?
				j.width(300) :
				j.width($(window).width() - 280);
		}

		$(window).resize(onResizeWindow);

		onResizeWindow(); // set the heights and stuff when it loads
		
		window.setInterval(function() { onResizeWindow(); }, 250); // whatever bruh

		$("#Sidebar a").each(function() {
			if($(this).attr("href") != "")
				return;

			$(this).attr("href", "#");

			$(this).on("click", function() {
				var view = $(this).data("view");

				$("#Places > div").each(function() {
					$(this).css("display", "none");
				});

				$("#Sidebar a").each(function() {
					$(this).removeAttr("selected");
				})

				$(this).attr("selected", "true");

				$("#"+view+"ProjectsView").css("display", "block");
			})
		})
	});
</script>
<div id="Header">
	<img src="/public/images/ide/studio_title.png">
</div>
<div id="Separator"></div>
<div id="PlacesContainer">
	<div id="Sidebar">
		<div id="SidewaySeparator"></div>
		<?php if(!isset($_GET['universeId'])): ?>
		<ul>
			<li><a href="" data-view="Main" selected>Your Projects</a></li>
			<li><a href="" data-view="Collaborative">Collaborated Projects</a></li>
			<?php if(count($extra_places) != 0): ?>
			<li><a href="" data-view="RecentlyOpened">Recently Opened Files</a></li>
			<?php endif ?>
		</ul>
		<?php else: ?>
		<div id="DidYouKnow" style="margin: 10px 0px; margin-left: 8px;">
			<p style="margin-bottom: 0px; font-style: italic; font-size: 12px; color: #DDD;">Viewing:</p>
			<p style="font-size: 14px; margin-top: 3px"><b><?= $universe->starting_place->name ?></b></p>
			<p><b><a href="/develop/projects">&gt;&gt;&gt; Go back &lt;&lt;&lt;</a></b></p>
		</div>
		<?php endif ?>
		<div id="DidYouKnow">
			<p style="font-size: 16px"><b>Did you know?</b></p>
			<p><?= $splash ?></p>
		</div>
	</div>
	<div id="Places">
		<?php if(isset($_GET['universeId'])): ?>
		<div id="MainProjectsView">
			<?php
				foreach($places as $place) {
					$place_timeago = UtilUtils::GetTimeAgo($place->last_updatetime);
					$place_name = $universe->starting_place->id == $place->id ? ">> Starting Place <<" : $place->name;

					echo <<<EOT
					<div class="Place" data-place-id="{$place->id}" title="{$place_name}">
						<a href="#">
							<img src="{$place->getThumbsUrl(229, 132)}">
							<div id="Name">{$place_name}</div>
							<div id="LastEdited">Last edited: {$place_timeago}</div>
						</a>
					</div>
					EOT;
				}
			?>
		</div>
		<?php else: ?>
		<div id="MainProjectsView">
			<?php
				foreach($places as $place) {

					$place_timeago = UtilUtils::GetTimeAgo($place->last_updatetime);
					$universe = Universe::FromID($place->universe);

					$universeplace = <<<EOT
					data-place-id="{$place->id}"
					EOT;

					if(count($universe->getAllPlaces()) > 1) {
						$universeplace = <<<EOT
						data-universe-id="{$universe->id}"
						EOT;
					}

					// todo: make this nicer because this is UGLY!

					if(count($universe->getAllPlaces()) == 1 && !$universe->teamcreate) {
						echo <<<EOT
						<div class="Place" {$universeplace} title="{$place->name}">
							<a href="#">
								<img src="{$place->getThumbsUrl(229, 132)}">
								<div id="Name">{$place->name}</div>
								<div id="LastEdited">Last edited: {$place_timeago}</div>
							</a>
						</div>
						EOT;
					}
					else {
						echo <<<EOT
						<div class="Place" {$universeplace} title="{$place->name}">
							<a href="#">
								<div style="position: relative;">
									<img src="{$place->getThumbsUrl(229, 132)}">
						EOT;

						if($universe->teamcreate) {
							if(count($universe->getAllPlaces()) == 1) {
								echo <<<EOT
											<img src="/public/images/icons/cloud.png" style="border: none;height: 32px;position: absolute;right: 12px;">
								EOT;
							}
							else {
								echo <<<EOT
											<img src="/public/images/icons/world.png" style="border: none;height: 32px;position: absolute;right: 12px;">
											<img src="/public/images/icons/cloud.png" style="border: none;height: 32px;position: absolute;right: 46px;">
								EOT;
							}
							
						}
						else {
							if(count($universe->getAllPlaces()) > 1) {
								echo <<<EOT
											<img src="/public/images/icons/world.png" style="border: none;height: 32px;position: absolute;right: 7px;top: 5px;">
								EOT;
							}
						}

						echo <<<EOT
								</div>
								<div id="Name">{$place->name}</div>
								<div id="LastEdited">Last edited: {$place_timeago}</div>
							</a>
						</div>
						EOT;
					}
				}
			?>
		</div>
		<div id="CollaborativeProjectsView" style="display: none">
			<?php
				foreach($teamplaces as $place) {

					$place_timeago = UtilUtils::GetTimeAgo($place->last_updatetime);

					echo <<<EOT
					<div class="Place" data-place-id="{$place->id}">
						<a href="#">
							<img src="{$place->getThumbsUrl(229, 132)}">
							<div id="Name">{$place->name}</div>
							<div id="LastEdited">Last edited: {$place_timeago}</div>
						</a>
					</div>
					EOT;
				}
			?>
		</div>
		<div id="RecentlyOpenedProjectsView" style="display: none">
			<?php
				foreach($extra_places as $place) {
					$filename = $place["name"];
					$filepath = $place["path"];
					echo <<<EOT
					<td>
						<div class="Place" data-place-id="$filepath">
							<a href="#">
								<img src="/public/images/rejected.png">
								<div id="Name">$filename</div>
							</a>
						</div>
					</td>
					EOT;
				}
			?>
		</div>
		<?php endif ?>
	</div>
</div>

