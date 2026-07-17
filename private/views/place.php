<?php
	if(!isset($id))
		redirect("/my/stuff");

	use anorrl\Asset;
	use anorrl\Comment;
	use anorrl\Page;
	use anorrl\Place;
	use anorrl\Universe;

	$user = SESSION->user;

	$place = Place::FromID($id);
	$domain = CONFIG->domain;

	if($place != null) {
		
		if($place->getURLTitle() != $name) {
			redirect("{$place->getURL()}");
		}

		$universe = Universe::FromID($place->universe);

		if($user != null) {
			$is_creator = $place->isOwner($user);
			$is_favourited = $place->hasUserFavourited($user);
			$is_bought = $user->owns($place);
			
			if(
				isset($_POST['ANORRL$Comment$Post$Contents']) &&
				isset($_POST['ANORRL$Comment$Post$Submit']) &&
				$place->comments_enabled
			) {
				$result = Comment::Post($place, $_POST['ANORRL$Comment$Post$Contents']);
				
				if($result['error']) {
					$_SESSION['ANORRL$Comment$Post$Error'] = $result['reason'];
				}

				redirect("{$place->getURL()}");
			}

			$comments = Comment::GetCommentsOn($place);
			$comments_count = count($comments);
		}

		$favourites_label = $place->favourites_count . " time". ($place->favourites_count != 1 ? "s" : "");
		
		$place_creator_name = $place->creator->name;
		$place_description = $place->description;
		if(strlen(trim($place_description)) == 0) {
			$place_description = <<<EOT
			<span id="NoDescription">Seems like $place_creator_name hasn't put anything here...</span>
			EOT;
		} else {
			$place_description = str_replace(PHP_EOL, "<br>", $place_description);
		}
	} else {

		$new_asset = Asset::FromID($id);
		if($new_asset == null) {
			redirect("/my/stuff");
		} else {
			redirect("/{$new_asset->getURL()}");
		}
	}
	$header_data = $place;

	$page = new Page(htmlspecialchars($place->name, ENT_QUOTES));
	
	$page->addStylesheet("/css/new/comments.css?v=1");
	$page->addStylesheet("/css/new/item/item.css?v=2");
	$page->addStylesheet("/css/new/item/place.css?v=5");
	$page->addStylesheet("/css/new/my/home.css?v=2");
	$page->addStylesheet("/css/new/window.css");
	$page->addStylesheet("/css/new/placelauncher.css?");
	

	$page->addScript("/js/item.js?t=1776186351");
	$page->addScript("/js/placelauncher.js?t=1777822582");

	$page->addMeta("title", htmlspecialchars($place->name, ENT_QUOTES));
	$page->addMeta("description", htmlspecialchars(substr($place->description, 0, 128), ENT_QUOTES));
	$page->addMeta("og:type", "website");
	$page->addMeta("og:site_name", "ANORRL");
	$page->addMeta("og:url", "https://{$domain}{$place->getURL()}");
	$page->addMeta("og:title", htmlspecialchars($place->name, ENT_QUOTES));
	$page->addMeta("og:description", htmlspecialchars(substr($place->description, 0, 128), ENT_QUOTES));
	$page->addMeta("og:image", "https://{$domain}{$place->getThumbsUrl()}");

	$page->loadHeader();

	if($user == null) {
		die();
	}

?>
<script>
	function ChangeTab(tabName) {
		var tabToGoTo = tabName.toLowerCase();
		$("#InfoHeaders td").each(function() {
			if($(this).html().toLowerCase() != tabToGoTo) {
				$(this).removeAttr("selected");
			} else {
				$(this).attr("selected", "true");
			}
		})

		$("#InfoBox[content]").each(function() {
			if($(this).attr("content").toLowerCase() != tabToGoTo) {
				$(this).css("display", "none");
			} else {
				$(this).css("display", "block");
				<?php if($user != null): ?>
				if($(this).attr("content") == "Servers") {
					ANORRL.PlaceLauncher.GrabGameservers(<?= $id ?>);
				}
				<?php endif ?>
			}
		});

		ANORRL.ChangeUrl("", window.location.pathname+window.location.search+"#"+tabToGoTo);
	}

	$(function() {

		var tab = window.location.hash != "" ? window.location.hash.replace("#", "") : "info";
		//alert(tab);
		ChangeTab(tab);

		$("#InfoHeaders td").click(function() {
			ChangeTab($(this).html());
			return false;
		});
	})
	
	<?php if($is_creator): ?>
	var rendering = false;
	function Render() {
		if(rendering) {
			return;
		}

		rendering = true;
		if(window.confirm("Are you sure you want to render this asset?")) {
			$("#RenderButton").html("Rendering...");
			$.post( "/api/asset/render", { id: <?= $place->id ?> }).done(function( data ) {
				if(data['error']) {
					window.alert(data['reason']);
				}
				window.location.reload();
			});
		}
	}
	
	function Delete() {
		if(window.confirm("Are you sure you want to delete this??")) {
			$.post( "/api/asset/delete", { id: <?= $place->id ?> }).done(function( data ) {
				if(data['error']) {
					window.alert(data['reason']);
				}
				window.location.reload();
			});
		}
	}

	function Shutdown() {
		if(window.confirm("Are you sure you want to shutdown ALL servers??")) {
			$.post( "/api/gameservers/shutdown", { placeID: <?= $place->id ?> }).done(function( data ) {
				ANORRL.PlaceLauncher.GrabGameservers(<?= $place->id ?>);
				toggleToolbar();
				if(data['error']) {
					window.alert(data['reason']);
				}
			});
		}
	}

	function toggleToolbar() {
		if($("#OptionsToolbar").attr("enabled") == undefined) {
			$("#OptionsToolbar").attr("enabled", "");
		} else {
			$("#OptionsToolbar").removeAttr("enabled");
		}
	}
	<?php endif ?>
</script>

<div id="LaunchingGameContainer">
	<div class="Window">
		<div id="Name">ANORRL</div>
		<div id="Contents" style="padding: 20px;">
			<div id="LoadingAreaContainer">
				<div id="RunningGuy">
					<img src="/public/images/ProgressIndicator4White.gif" width="100">
				</div>
				<p id="LaunchingTextContainer">
					<span id="LaunchingText">ANORRL is launching!</span>
					<img src="/public/images/spinner16x16.gif">
				</p>
				<p id="LauncherQuote">Have you checked the oven recently?</p>
			</div>
			<div id="DownloadClientContainer" style="display: none">
				<img src="/public/images/download/client.png" width="100">
				<p>You should probably <a href="/download">download</a> the client if you haven't already...</p>
			</div>
			<div id="DownloadStudioContainer" style="display: none">
				<img src="/public/images/download/studio.png" width="100">
				<p>You should probably <a href="/download">download</a> the studio if you haven't already...</p>
			</div>
		</div>
	</div>
</div>

<div id="ItemContainer">
	<h4>ANORRL <?= $place->type->label(); ?></h4>
	<h2><a class="FavouriteButton" href="#" data-assetid="<?= $place->id ?>" <?= $is_favourited ? 'favourited="true"' : "" ?>></a><?= $place->name ?></h2>
	<?php if($universe->starting_place->id != $place->id): ?>
	<h3 style="color: #CCC;font-style: italic;width: 830px;text-align: center;">This place is belongs to: <a href="<?= $universe->starting_place->getURL() ?>"><?= $universe->starting_place->name ?></a></h3>
	<?php endif ?>
	<div id="PlaceDetails">
		<div id="Content">
			<div id="PlaceImageContainer">
				<img src="<?= $place->getThumbsUrl(623, 350) ?>&nocompress">
				<?php if($universe->original): ?>
				<div id="OriginalLabel">Original</div>
				<?php endif ?>
			</div>
		</div>
		<div id="Information">
			<div id="UserCard">
				<?php if($is_creator): ?>
				<div id="OptionsToolbar">
					<button id="OptionsClicker" onclick="toggleToolbar()">
						<img src="/public/images/icons/cog.png">
					</button>
					<div id="OptionsMenu">
						
						<div class="Row">
							<img src="/public/images/icons/wrench_orange.png">
							<a href="/edit?id=<?= $place->id ?>">Configure</a>
						</div>
						<?php if($place->isUsable()): ?>
						<div class="Row">
							<img src="/public/images/icons/camera.png">
							<a href="javascript:Render()" id="RenderButton">Render this asset</a>
						</div>
						<div class="Row">
							<img src="/public/images/icons/world.png">
							<a href="javascript:Shutdown()">Shutdown all servers</a>
						</div>
						<?php endif ?>
						<div class="Row">
							<img src="/public/images/icons/delete.png">
							<a href="javascript:Delete()">Delete this asset</a>
						</div>
						
					</div>
				</div>
				<?php endif ?>
				<a href="/users/<?= $place->creator->id ?>/profile"><img src="<?= $place->creator->getThumbsUrlService("player", 110)?>" style="width: 110px;display:block;margin:0 auto;"></a>
				<div id="AssetInfoStuff">
					<span>Created by <a href="/users/<?= $place->creator->id ?>/profile"><?= $place_creator_name ?></a></span>
					<span><b>Favourited</b>: <?= $favourites_label ?></span>
					<?php if($place->gears_enabled): ?>
					<span id="GearsEnabled">Gears enabled!</span>
					<?php endif ?>
				</div>
				<hr>
				<div id="GameButtons" >
					<?php if($place->isUsable()): ?>
						<button class="PlaceButton" onclick="ANORRL.PlaceLauncher.LetsJoinAndPlay(<?= $id ?>)" Play></button>
						<?php if($is_creator || !$place->copylocked): ?>
						<button class="PlaceButton" onclick="ANORRL.PlaceLauncher.EditPlace(<?= $id ?>)" Edit></button>
						<?php endif ?>
					<?php else: ?>
					<div id="NotOnSale">This place is broken and needs to be republished.</div>
					<?php endif?>
				</div>
			</div>
		</div>
	</div>

	<?php
	$teamcreate = $universe->teamcreate && count($universe->getCloudEditors()) > 1; // assume creator if 1
	if($user != null && $teamcreate): ?>
	<div id="CommentsContainer">
		<h3>Users worked on this!</h3>
		<div id="CommentSection">
			<div id="FriendsContainer">
				<ul id="Friends" style="width: 848px;border: 0px;background: none;padding: 0px;text-align: center;height: 140px;">
					<?php $users = $universe->getCloudEditors(); foreach($users as $u): ?>
						<li class="Friend">
							<a id="ProfileLink" href="/users/<?= $u->id ?>/profile">
								<img id="Profile" src="<?= $u->getThumbsUrl(100) ?>">
								<div id="Name"><?= $u->name ?></div>
							</a>
						</li>							
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>
	<?php endif ?>

	<div id="PlaceInfoArea">
		<table id="InfoHeaders">
			<td>Info</td>
			<td>Badges</td>
			<td>Servers</td>
		</table>
		<div id="InfoBox" content="Info" style="display:none">
			<b>Description</b>
			<hr>
			<div id="Description">
				<?= $place_description ?>
			</div>
			<hr>
			<table id="BigNumbersArea">
				<td id="Detail">
					<b>Created</b>
					<span><?= $place->created_at->format('d/m/Y H:i'); ?></span>
				</td>
				<td id="Detail">
					<b>Updated</b>
					<span><?= $place->last_updatetime->format('d/m/Y H:i'); ?></span>
				</td>
				<td id="Detail">
					<b>Visits</b>
					<span><?= $place->visit_count ?></span>
				</td>
				<td id="Detail">
					<b>Active</b>
					<span><?= $place->current_playing_count ?></span>
				</td>
				<td id="Detail">
					<b>Server Size</b>
					<span><?= $place->server_size ?></span>
				</td>
				<td id="Detail">
					<b>Copylocked</b>
					<span><?= $place->copylocked ? "Yes" : "No" ?></span>
				</td>
			</table>
		</div>
		<div id="InfoBox" content="Badges" style="display:none">
			<b>Badges <?php if($place->isOwner($user, true)): ?> <a href="/create/<?= $id ?>/badge">[[ Create ]]</a><?php endif ?></b>
			<hr>
			<?php if(count($place->getBadges()) != 0): ?>
			<table id="Badges">
				<?php foreach($place->getBadges() as $badge): ?>
				<tr>
					<td width="128">
						<img src="<?= $badge->getThumbsUrl(128) ?>">
					</td>
					<td>
						<div class="BadgeName"><h3><?= $badge->name ?></h3></div>
						<div class="BadgeDesc"><?= strlen(trim($badge->description)) == 0 ? "<b>Seems like no description was set...</b>" : $badge->description ?></div>
					</td>
					<td width="200">
						<table style="border: 2px solid black; padding: 21px; width: 200px; background: #111;">
							<tr>
								<td>Rarity</td>
								<td><?= $badge->getRarity() ?>%</td>
							</tr>
							<tr>
								<td>Won Yesterday</td>
								<td><?= $badge->getWonYesterdayTimes() ?></td>
							</tr>
							<tr>
								<td>Won Ever</td>
								<td><?= $badge->getWonEverTimes() ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<?php endforeach ?>
			</table>
			<?php else: ?>
				<div style="font-size: 14px;margin: 25px 10px;text-align: center;">This place has no badges! Find one that does I guess...</div>
			<?php endif ?>
		</div>
		<div id="InfoBox" content="Servers" style="display:none">
			<div class="Window" style="margin: 0 auto; width: 100%">
				<div id="Name">Servers<?php if($user): ?> <button onclick="ANORRL.PlaceLauncher.GrabGameservers(<?= $id ?>);">Refresh</button><?php endif ?></div>
				<div id="Contents">
					<div id="ServersBox" style="border: none; background: none; padding: none;">
						<?php if($user == null): ?>
						<p id="NoGamesWarning">You need to be logged in to see the servers for this game!</p>
						<?php else: ?>
							<p id="NoGamesWarning">There are no servers for this game!</p>
						<?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="CommentsContainer">
		<?php if($user == null || !$place->comments_enabled): ?>
		<h3>Comments</h3>
		<div id="CommentSection">
			<?php if($user == null): ?>
			<div id="CommentsDisabled">You need to be logged in to comment on this item!</div>
			<?php else: ?>
			<div id="CommentsDisabled">Comments have been disabled for this item.</div>
			<?php endif ?>
		</div>
		<?php else: ?>
		<h3>Comments (<?= $comments_count ?>)</h3>
		<div id="CommentPostArea">
			<?php if(isset($_SESSION['ANORRL$Comment$Post$Error'])): ?>
			<div class="Error">Error: <?= $_SESSION['ANORRL$Comment$Post$Error'] ?></div>
			<?php endif ?>
			<form method="POST">
				<h4 style="margin: 0; letter-spacing: 5px;">Post a comment or something</h4>
				<textarea placeholder="Write a wonderful comment about this place!" name="ANORRL$Comment$Post$Contents" maxlength="256" minlength="4"></textarea>
				<input type="submit" value="Submit!" name="ANORRL$Comment$Post$Submit">
			</form>
		</div>
		<div id="CommentSection">
			<?php if($comments_count != 0):
				foreach($comments as $comment) {
					if($comment instanceof Comment) {
						$comment->PrintComment();
					}
				}
			else: ?>
			<div id="CommentsDisabled">It's pretty empty in here... :<</div>
			<?php endif ?>
		</div>
		<?php endif ?>
	</div>
</div>
<?php 
$page->loadFooter();
unset($_SESSION['ANORRL$Comment$Post$Error']); ?>
