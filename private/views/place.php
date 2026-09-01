<?php
	use anorrl\utilities\Utilities;
	
	if(!isset($id))
		redirect("/my/stuff");

	use anorrl\Asset;
	use anorrl\Page;
	use anorrl\Place;
	use anorrl\Universe;

	$user = ARLAUTH ? SESSION->user : null;

	$place = Place::FromID($id);

	$is_creator = false;
	$is_favourited = false;
	$is_editable = false;

	if($place != null) {
		
		if($place->getURLTitle() != $name) {
			redirect($place->getURL());
		}

		$universe = Universe::FromID($place->universe);
		$is_starting_place = $universe->starting_place->id == $place->id;

		if($user != null) {
			$is_creator = $place->isOwner($user);
			$is_favourited = $place->hasUserFavourited($user);
			$is_editable = $place->isEditable($user);
		}

		$favourites_label = $place->favourites_count . " time". ($place->favourites_count != 1 ? "s" : "");
		
		$place_creator_name = $place->creator->name;
		$place_description = $place->description;
		$place_thumbnail = $is_starting_place ? $place->getThumbsUrl() : $universe->starting_place->getThumbsUrl();
		$place_playable_id = $is_starting_place ? $place->id : $universe->starting_place->id;
		if(strlen(trim($place_description)) == 0) {
			$place_description = <<<EOT
			<b>Seems like $place_creator_name hasn't put anything here...</b>
			EOT;
		} else {
			$place_description = str_replace(PHP_EOL, "<br>", $place_description);
		}
	} else {

		$new_asset = Asset::FromID($id);
		if($new_asset == null) {
			redirect("/my/stuff");
		} else {
			redirect($new_asset->getURL());
		}
	}

	if(ARLAUTH) {
		$_SESSION['ANORRL$Asset$ID'] = $place->id;
	}
	else {
		if(isset($_SESSION['ANORRL$Asset$ID']))
			unset($_SESSION['ANORRL$Asset$ID']);
	}

	$place_short_name = null;
	if(strlen($place->name) > 35 ) {
		$place_short_name = trim(substr($place->name, 0, 35)). "...";
	}

	$page = new Page(htmlspecialchars($place->name, ENT_QUOTES), "anorrl_asset");
	$page->addStylesheet("/css/comments.css");
	$page->addScript("/js/comments.js");
	$page->addScript("/js/ratings.js");
	$page->addValue("asset", $place->id);
	$page->addValue("launch", $place_playable_id);
	$place->loadEmbed($page);
	
	$page->loadHeader();
?>
<?php $page->loadTemplate("layouts/comments/templates"); ?>
<style>
	.cog-dropdown ul {
		left: 30px;
	}
</style>
<style>
	.play-btn, .edit-btn {
		font-size: 14px;
		padding: 7px 50px;
		text-align: center;
		margin-bottom: 5px;
		color: white;
		width: 220px;
		padding: 0px;
		background: none;
		background-size: 100%;
		cursor: pointer;
		height: 55px;
		border: 0px;
	}

	.play-btn {
		background-image: url("/public/images/buttons/play.png");
	}

	.edit-btn {
		background-image: url("/public/images/buttons/edit.png");
	}

	.play-btn:hover,
	.edit-btn:hover {
		filter: brightness(1.15);
	}

	.play-btn:active,
	.edit-btn:active {
		background-position: 0 56px;
	}

	table#controls {
		width: 100%;
		text-align: left;
		vertical-align: middle
	}

	#controls td {
		vertical-align: middle;
		height: 48px;
	}

	#controls button {
		display: flex;
		align-items: center;
		gap: 10px;
		background: none;
		border: none;
		cursor: pointer;
		user-select: none;
		font-size: 14px;
		font-family: 'Fira Mono';
	}

	#controls button:hover {
		filter: brightness(1.25);
	}

	#controls button:active {
		cursor:grab;
		text-decoration:underline;
	}

	.ratings-bar {
		border-radius: 2px;
		height: 6px;
		margin: 0px auto;
		background: #666;
		width: 86px;
	}

	.ratings-bar[red] {
		background: #ff0944;
	}

	.ratings-bar[green] {
		background: #19ac19;
	}

	.ratings-bar > div {
		border-radius: 2px;
		height: 100%;
		background: #19ac19;
	}

	.image-container {
		width: 576px;
		height: 324px;
		margin-right: 10px;
	}

	.image-container img {
		width:100%;
		height: 100%;
		border: 1px solid var(--lighter-border-color);
	}

	#place-name, #place-short-name {
		font-size: 25px;
		font-weight: 400;
		margin-top: 5px;
		margin-bottom: 5px;
	}

	#place-short-name {
		max-height: 69px;
		overflow:hidden;
		text-overflow:ellipsis;
	}

	#asset-short-name {
		max-height: 69px;
		overflow:hidden;
		text-overflow:ellipsis;
	}

	#asset-name {
		height: auto;
		background: linear-gradient(0deg,#1a0c23 0%, #492265 100%);
		border: 1px solid var(--border-color);
		margin-top:4px;
	}

	#asset-name-container:hover #asset-short-name {
		display: none;
	}

	#asset-name-container .hidden {
		display: none;
	}

	#asset-name-container:hover .hidden h2 {
		position: absolute;
	}

	#asset-name-container:hover .hidden {
		display: block;
		
	}
</style>

<h2 class="page-title">.place</h2>
<?php if(!$is_starting_place): ?>
<div class="box" alert="" style="font-family: 'Fira Mono';padding: 15px;font-size: 13px;margin-bottom: 5px;">
	This place is part of <?= $universe->starting_place->name ?>! <a href="<?= $universe->starting_place->getURL() ?>" style="float: right;">Take me there!</a>
</div>
<?php endif ?>

<div class="box" style="display: flex; padding: 10px; position: relative;">
	<div style="flex: 1">
		<div class="image-container">
			<a href="<?= $place_thumbnail ?>" target="__blank">
				<img src="<?= $place_thumbnail ?>">
			</a>
		</div>
	</div>
	
	<div style="flex: 1; text-align: center; margin: 10px; position: relative;">
		<div style="min-height:97px; height: 97px; display: flex; flex-direction: column;">
			<div id="asset-name-container" style="flex: 1">
				<?php if($place_short_name): ?>
				<h2 id="place-short-name"><?= $place_short_name ?></h2>
				<div class="hidden">
					<h2 id="asset-name"><?= $place->name ?></h2>
					<div style="height: 78px;"></div>
				</div>
				<?php else: ?>
					<h2 id="asset-name" style="background: none; border: none; flex: 1"><?= $place->name ?></h2>
				<?php endif ?>
			</div>
			<div style="font-size: 14px; font-style: italic; margin-bottom: 15px;">created by <a href="<?= $place->creator->getURL() ?>"><?= $place_creator_name ?></a></div>
		</div>
		<hr>
		<div style="
				padding-top: 5px;
				height: 120px;
				display: flex;
				align-items: center;
				justify-content: center;
				flex-direction: column;
			">
			<button class="play-btn"></button>
			<?php if($is_editable): ?>
			<button class="edit-btn"></button>
			<?php endif ?>
		</div>
		<hr>
		<table id="controls">
			<tr>
				<td width="90">
					<button style="color: #ffdb5b;" id="fav-btn">
						<img src="/public/images/buttons/favourite_star.gif" width="32">
						<span id="fav-count"><?= $place->favourites_count ?></span>
					</button>
				</td>
				<td>
					<div style="display: flex;align-items: center" class="ratings-container">
						<button style="color: #19ac19;" id="up-btn">
							<span id="up-count">--</span>	
							<img src="/public/images/buttons/thumbs_up.gif" width="32">	
						</button>
						<div style="flex: 1">
							<div class="ratings-bar">
								
							</div>
						</div>
						<button style="color: #ff0944;" id="down-btn">
							<img src="/public/images/buttons/thumbs_down.gif?v=2" width="32">
							<span id="down-count">--</span>
						</button>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php if($is_creator): ?>
	<div class="cog-dropdown" style="position: absolute; right: 10px">
		<button class="button cog" style="padding: 2px 4px" class=""><img src="/public/images/icons/cog.png" ></button>
		<ul>
			<?php if($place->isStartingPlace()): ?>
			<li data-actionid="1"><span>&gt;</span> configure</li>
			<li data-actionid="2"><span>&gt;</span> advertise</li>
			<li data-actionid="3"><span>&gt;</span> create badge</li>
			<li data-actionid="4"><span>&gt;</span> shutdown all servers</li>
			<li data-actionid="5"><span>&gt;</span> sex update</li>
			<?php else: ?>
			<li data-actionid="1"><span>&gt;</span> configure</li>
			<li data-actionid="3"><span>&gt;</span> create badge</li>
			<li data-actionid="4"><span>&gt;</span> shutdown all servers</li>
			<?php endif ?>
		</ul>
	</div>
	<?php endif ?>
</div>
<?php if($is_starting_place): ?>
<style>
	#buttons {
		display: flex;
		gap: 5px;
		margin-top: 5px;
		text-align: center;
	}

	#buttons .button {
		flex: 1;
		text-decoration: none;
	}

	#place-stats {
		width: 100%;
		text-align: center;
		table-layout: fixed;
		margin-bottom: 5px;
	}

	#place-stats td * {
		display: block;
	}

	#place-stats td b {
		font-family: monospace;
		font-size: 11px;
		border-bottom: 1px solid #6c3a8c;
		width: 75px;
		margin: 0 auto;
		padding-bottom: 2px;
		margin-bottom: 5px;
	}

	#place-stats td span {
		*font-size: 13px;
	}

	.box[data-tab] {
		display: none;
	}

	.button[selected] {
		background: linear-gradient(0deg,#9c37df 0%, #512270 100%);
		filter: brightness(1.15);
		letter-spacing: 2px;
		text-decoration: underline !important;
	}

	div[data-tab] hr {
		margin-left: 5px;
		margin-right: 5px;
	}

	.nothing {
		margin: 25px auto;
		font-size: 14px;
		font-family:monospace;
		text-align: center;
		color: var(--special-pink);
	}
</style>
<div class="box" id="buttons">
	<a class="button" href="#info" data-tab="info">info</a>
	<a class="button" href="#store" data-tab="store">store</a>
	<a class="button" href="#badges" data-tab="badges">badges</a>
	<a class="button" href="#servers" data-tab="servers">servers</a>
</div>
<div style="margin-top: 5px;">
	<div class="box" data-tab="info" >
		<h3 style="margin: 5px;">.description</h3>
		<hr>
		<div style="margin: 10px 15px; line-height: 1.5em; font-family: monospace; font-size: 11px; color: var(--special-pink)">
			<?= $place_description ?>
		</div>
		<hr>
		<table id="place-stats">
			<td title="<?= Utilities::GetTimeAgo($place->created_at) ?>">
				<b>.created</b>
				<span><?= $place->created_at->format('d/m/Y'); ?></span>
			</td>
			<td title="<?= Utilities::GetTimeAgo($place->last_updatetime) ?>">
				<b>.updated</b>
				<span><?= $place->last_updatetime->format('d/m/Y'); ?></span>
			</td>
			<td>
				<b>.visits</b>
				<span><?= $place->visit_count ?></span>
			</td>
			<td>
				<b>.genre</b>
				<span><?= $place->genre->label() ?></span>
			</td>
			<td>
				<b>.server_size</b>
				<span><?= $place->server_size ?></span>
			</td>
			<td>
				<b>.copylocked</b>
				<span><?= $place->copylocked ? "Yes" : "No" ?></span>
			</td>
		</table>
	</div>
	<div class="box" data-tab="store">
		<h3 style="margin: 5px;">.store</h3>
		<hr>
		<?php if(count($universe->getDeveloperProducts()) > 0): ?>
			<?php // foreach loop them and make em buyable ?>
		<?php else: ?>
			<div class="nothing">This place has no items!</div>
		<?php endif ?>
	</div>
	<div class="box" data-tab="badges">
		<h3 style="margin: 5px;">.badges</h3>
		<hr>
		<?php if(count($universe->getBadges()) > 0): ?>
			<?php // foreach loop them and make em buyable ?>
		<?php else: ?>
			<div class="nothing">This place has no badges!</div>
		<?php endif ?>
	</div>
	<div class="box" data-tab="servers">
		<h3 style="margin: 5px;">.servers</h3>
		<hr>
	</div>
</div>
<div style="margin-top: 5px;">
	<?php $page->loadTemplate("layouts/comments/main"); ?>
</div>
<?php endif ?>
<script>
	function cogDisable() {
		$(".cog").removeAttr("active");
		$(".cog-dropdown ul").css("display", "none");
	}

	$(window).click(cogDisable);

	$(".cog").click(function(event) {
		event.stopPropagation();
		var was_active = typeof($(this).attr("active")) == "undefined";
		
		cogDisable();
		if(was_active) {
			$(this).attr("active",true);
			$(this).parent().find("ul").css("display", "block");
		}
	})

	<?php if($is_starting_place): ?>
	$(".cog-dropdown li").click(function() {
		var action = $(this).data("actionid");

		if(action == 1) {
			window.location.href = "/develop/<?= $place->id ?>/configure";
		}
		else if(action == 4) {
			$.post("/api/universes/"+universe+"/shutdown", function(data) {
				if(!data['success'])
					ANORRL.MessageBox.Show(ANORRL.MessageBox.Type.ERROR, data['reason']);
			})
		}
		else if(action == 5) {
			ANORRL.MessageBox.Show(ANORRL.MessageBox.Type.ERROR, "not legible for sex...");
		}
	});
	<?php endif ?>

	$(".button[data-tab]").click(function() {
		var type = $(this).data("tab");
		$(".box[data-tab]").hide();
		$(".box[data-tab='"+type+"']").show();
		$(".button[data-tab]").removeAttr("selected");
		$(this).attr("selected", "yes");
	})
	
	function setType(type) {
		if($(".button[data-tab='"+type+"']").length != 0) {
			$(".button[data-tab='"+type+"']").attr("selected", "yes");
			$(".box[data-tab='"+type+"']").show();
		}
		else {
			setType("info");
			window.location.hash = "info";
		}
	}

	var hash = window.location.hash;
	if(hash.startsWith("#"))
		hash = hash.substring(1);

	setType(hash);

	$("#fav-btn").click(function() {
		$.post("/asset/<?= $place->id ?>/favourite", function (data) {
			if(!data['success'])
				ANORRL.MessageBox.Show(ANORRL.MessageBox.Type.ERROR, data['reason']);
			else
				$("#fav-count").html(data['count']);
		})
	});

	$("#up-btn").click(function() {
		ANORRL.Ratings.Rate(true);
	})

	$("#down-btn").click(function() {
		ANORRL.Ratings.Rate(false);
	})
</script>
<?php $page->loadFooter(); ?>
