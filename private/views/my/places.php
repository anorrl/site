<?php
	use anorrl\Page;
	use anorrl\utilities\Splasher;
	use anorrl\utilities\ClientDetector;
	use anorrl\utilities\UtilUtils;

	if(!SESSION) {
		die(header("Location: /login"));
	}

	$user = SESSION->user;

	$isclient = ClientDetector::IsAClient();
	if(!$isclient)
		die("Hey something isn't right here... You sure you're using the right studio?");
	
	$places = $user->GetPlaces(false);
	$teamplaces = $user->GetPlaces(true);

	$domain = CONFIG->domain;

	$splash = new Splasher("didyouknow")->getRandomSplash();

	$page = new Page("ANORRL Studio");
	$page->clearAll();
	$page->addScript("/js/core/jquery.js");
	$page->addStylesheet("/css/new/my/places.css");
	$page->loadBasicHeader();
?>

<script>
	$(function() {
		$(".Place").on("click", function() {
			var placeid = $(this).attr("data-place-id");
			window.external.startGame("http://<?= $domain ?>/","http://<?= $domain ?>/","http://<?= $domain ?>/game/edit.ashx?placeId=" + placeid);
		});
	});
</script>
<div id="Header">
	<img src="/images/ide/studio_title.png">
</div>
<div id="Separator"></div>
<div id="PlacesContainer">
	<div id="Sidebar">
		<div id="SidewaySeparator"></div>
		<ul>
			<li><a href="" selected>Your Projects</a></li>
			<li><a href="">Collaborated Projects</a></li>
		</ul>
		<div id="DidYouKnow">
			<p style="font-size: 16px"><b>Did you know?</b></p>
			<p><?= $splash ?></p>
		</div>
	</div>
	<div id="Places">
		<div id="MainProjectsView">
			<table style="width: calc(100vw - 280px);">
				<?php
					$places_count = count($places);
					$count = 0;
					$pre_count = 0;
					foreach($places as $place) {
						if($count == 0) {
							echo "<tr>";
						}

						$place_timeago = UtilUtils::GetTimeAgo($place->last_updatetime);

						echo <<<EOT
						<td>
							<div class="Place" data-place-id="{$place->id}">
								<a href="#">
									<img src="/thumbs/?id={$place->id}&sx=229&sy=132">
									<div id="Name">{$place->name}</div>
									<div id="LastEdited">Last edited: {$place_timeago}</div>
								</a>
							</div>
						</td>
						EOT;

						$pre_count++;

						$count = $pre_count % 4;

						if($count == 4) {
							echo "</tr>";
						}
					}
				?>
			</table>
		</div>
		<div id="CollaborativeProjectsView" style="display: none">
			<table style="width: calc(100vw - 280px);">
				<?php
					$places_count = count($teamplaces);
					$count = 0;
					$pre_count = 0;
					foreach($teamplaces as $place) {
						if($count == 0) {
							echo "<tr>";
						}

						$place_timeago = UtilUtils::GetTimeAgo($place->last_updatetime);

						echo <<<EOT
						<td>
							<div class="Place" data-place-id="{$place->id}">
								<a href="#">
									<img src="/thumbs/?id={$place->id}&sx=229&sy=132">
									<div id="Name">{$place->name}</div>
									<div id="LastEdited">Last edited: {$place_timeago}</div>
								</a>
							</div>
						</td>
						EOT;

						$pre_count++;

						$count = $pre_count % 4;

						if($count == 4) {
							echo "</tr>";
						}
					}
				?>
			</table>
		</div>
	</div>
</div>

