<?php 
	if(!isset($id))
		$id = intval($_GET['id']);
	if(!isset($_GET['id']) && !isset($id))
		die(header("Location: /my/stuff"));

	use anorrl\Asset;
	use anorrl\Comment;
	use anorrl\Page;
	use anorrl\Place;

	$user = SESSION->user;

	$asset = Place::FromID($id);
	$domain = CONFIG->domain;

	if($asset != null) {
		
		if($asset->getURLTitle() != $name) {
			die(header("Location: /{$asset->getUrl()}"));
		}

		if($user != null) {
			$is_creator = $user->id == $asset->creator->id || $user->isAdmin();
			$is_favourited = $asset->hasUserFavourited($user);
			$is_bought = $user->owns($asset);
			
			if(
				isset($_POST['ANORRL$Comment$Post$Contents']) &&
				isset($_POST['ANORRL$Comment$Post$Submit']) &&
				$asset->comments_enabled
			) {
				$result = Comment::Post($asset, $_POST['ANORRL$Comment$Post$Contents']);
				
				if($result['error']) {
					$_SESSION['ANORRL$Comment$Post$Error'] = $result['reason'];
				}

				die(header("Location: /{$asset->getUrl()}"));
			}

			$comments = Comment::GetCommentsOn($asset);
			$comments_count = count($comments);
		}

		$favourites_label = $asset->favourites_count . " time". ($asset->favourites_count != 1 ? "s" : "");
		
		$asset_creator_name = $asset->creator->name;
		$asset_description = $asset->description;
		if(strlen(trim($asset_description)) == 0) {
			$asset_description = <<<EOT
			<span id="NoDescription">Seems like $asset_creator_name hasn't put anything here...</span>
			EOT;
		} else {
			$asset_description = str_replace(PHP_EOL, "<br>", $asset_description);
		}
	} else {

		$new_asset = Asset::FromID($id);
		if($new_asset == null) {
			die(header("Location: /my/stuff"));
		} else {
			die(header("Location: /{$new_asset->getUrl()}"));
		}
	}
	$header_data = $asset;

	$page = new Page(htmlspecialchars($asset->name, ENT_QUOTES));
	
	$page->addStylesheet("/css/new/comments.css?v=1");
	$page->addStylesheet("/css/new/item/item.css?v=2");
	$page->addStylesheet("/css/new/item/place.css?v=3");
	$page->addStylesheet("/css/new/my/home.css?v=2");
	$page->addStylesheet("/css/new/window.css");
	

	$page->addScript("/js/item.js?t=1776186351");
	$page->addScript("/js/placelauncher.js?t=1776434501");

	$page->addMeta("title", htmlspecialchars($asset->name, ENT_QUOTES));
	$page->addMeta("description", htmlspecialchars(substr($asset->description, 0, 128), ENT_QUOTES));
	$page->addMeta("og:type", "website");
	$page->addMeta("og:site_name", "ANORRL");
	$page->addMeta("og:url", "https://{$domain}{$asset->getUrl()}");
	$page->addMeta("og:title", htmlspecialchars($asset->name, ENT_QUOTES));
	$page->addMeta("og:description", htmlspecialchars(substr($asset->description, 0, 128), ENT_QUOTES));
	$page->addMeta("og:image", "https://{$domain}{$asset->getThumbsUrl()}");

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
		window.alert("Committing render! (Press ok to continue)");
		$("#RenderButton").html("Rendering...");
		$.post( "/api/asset/render", { id: <?= $asset->id ?> }).done(function( data ) {
			if(data['error']) {
				window.alert(data['reason']);
			}
			window.location.reload();
		});
	}
	
	function Delete() {
		if(window.confirm("Are you sure you want to delete this??")) {
			$.post( "/api/asset/delete", { id: <?= $asset->id ?> }).done(function( data ) {
				if(data['error']) {
					window.alert(data['reason']);
				}
				window.location.reload();
			});
		}
	}
	<?php endif ?>
</script>
<div id="ItemContainer">
	<h4>ANORRL <?= $asset->type->label(); ?></h4>
	<h2><a class="FavouriteButton" href="#" data-assetid="<?= $asset->id ?>" <?= $is_favourited ? 'favourited="true"' : "" ?>></a><?= $asset->name ?></h2>
	<div id="PlaceDetails">
		<div id="Content">
			<div id="PlaceImageContainer">
				<img src="<?= $asset->getThumbsUrl(623, 350) ?>&nocompress">
				<?php if($asset->is_original): ?>
				<div id="OriginalLabel">Original</div>
				<?php endif ?>
			</div>
		</div>
		<div id="Information">
			<div id="UserCard">
				<a href="/users/<?= $asset->creator->id ?>/profile"><img src="<?= $asset->creator->getThumbsUrlService("player", 110)?>" style="width: 110px;display:block;margin:0 auto;"></a>
				<div id="AssetInfoStuff">
					<span>Created by <a href="/users/<?= $asset->creator->id ?>/profile"><?= $asset_creator_name ?></a></span>
					<span><b>Favourited</b>: <?= $favourites_label ?></span>
					<?php if($asset->gears_enabled): ?>
					<span id="GearsEnabled">Gears enabled!</span>
					<?php endif ?>
				</div>
				<hr>
				<?php if($asset->isUsable()): ?>
					<button class="PlaceButton" onclick="ANORRL.PlaceLauncher.LetsJoinAndPlay(<?= $id ?>)" Play></button>
					<?php if($is_creator || !$asset->copylocked): ?>
					<button class="PlaceButton" onclick="ANORRL.PlaceLauncher.EditPlace(<?= $id ?>)" Edit></button>
					<?php endif ?>
				<?php else: ?>
				<div id="NotOnSale">This place is broken and needs to be republished.</div>
				<?php endif?>
				<hr>
				<div id="ManageOptions">
					<?php if($is_creator): ?>
					<a href="/edit?id=<?= $asset->id ?>">Configure</a>
					<?php if($asset->isUsable()): ?>
					<a href="javascript:Render()" id="RenderButton">Render this asset</a>
					<?php endif ?>
					<a href="javascript:Delete()">Delete this asset</a>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>

	<?php
	$teamcreate = $asset->teamcreate_enabled && count($asset->getCloudEditors());
	if($user != null && $teamcreate): ?>
	<div id="CommentsContainer">
		<h3>Users worked on this!</h3>
		<div id="CommentSection">
				<div id="FriendsContainer">
					<ul id="Friends" style="width: 848px;border: 0px;background: none;padding: 0px;text-align: center;height: 140px;">
						<?php $users = $asset->getCloudEditors(); foreach($users as $u): ?>
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
				<?= $asset_description ?>
			</div>
			<hr>
			<table id="BigNumbersArea">
				<td id="Detail">
					<b>Created</b>
					<span><?= $asset->created_at->format('d/m/Y H:i'); ?></span>
				</td>
				<td id="Detail">
					<b>Updated</b>
					<span><?= $asset->last_updatetime->format('d/m/Y H:i'); ?></span>
				</td>
				<td id="Detail">
					<b>Visits</b>
					<span><?= $asset->visit_count ?></span>
				</td>
				<td id="Detail">
					<b>Active</b>
					<span><?= $asset->current_playing_count ?></span>
				</td>
				<td id="Detail">
					<b>Server Size</b>
					<span><?= $asset->server_size ?></span>
				</td>
				<td id="Detail">
					<b>Copylocked</b>
					<span><?= $asset->copylocked ? "Yes" : "No" ?></span>
				</td>
			</table>
		</div>
		<div id="InfoBox" content="Badges" style="display:none">
			<b>Badges</b><br>
			Badges content in here
		</div>
		<div id="InfoBox" content="Servers" style="display:none">
			

			<div class="Window" style="margin: 0 auto; width: 100%">
				<div id="Name">Servers<?php if($user): ?> <button onclick="ANORRL.PlaceLauncher.GrabGameservers(<?= $id ?>);">Refresh</button><?php endif ?></div>
				<div id="Contents">
					<div id="ServersBox" style="border: none; background: none;">
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
		<?php if($user == null || !$asset->comments_enabled): ?>
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
