<?php
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

	if($place != null) {
		
		if($place->getURLTitle() != $name) {
			redirect($place->getURL());
		}

		$universe = Universe::FromID($place->universe);

		if($user != null) {
			$is_creator = $place->isOwner($user);
			$is_favourited = $place->hasUserFavourited($user);
		}

		$favourites_label = $place->favourites_count . " time". ($place->favourites_count != 1 ? "s" : "");
		
		$place_creator_name = $place->creator->name;
		$place_description = $place->description;
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
	$header_data = $place;

	$place_short_name = null;
	if(strlen($place->name) > 35 ) {
		$place_short_name = trim(substr($place->name, 0, 35)). "...";
	}

	$page = new Page(htmlspecialchars($place->name, ENT_QUOTES));
	$page->clearAll();
	$page->addScript("/js/core/jquery.js");
	$page->addScript("/js/comments.js");
	$page->addScript("/js/ratings.js");
	$page->addValue("asset", $place->id);
	$place->loadEmbed($page);
	
	$page->loadHeader2();
?>
<div class="comment" template>
	<div class="profile-container">
		<a target="__blank"><img width="100"></a>
	</div>
	<div class="contents-flex-container">
		<div class="contents-container box">
			<div style="padding: 10px">
				<div id="details">
					<div style="float: left">posted <span id="date"></span> by <a id="name"></a></div>
					<div style="display: inline-block; width: 15px">&nbsp;</div>
					<div style="float: right"><a>.report</a></div>
					<div style="clear:both"></div>
				</div>
				<div id="contents"></div>
			</div>
		</div>
	</div>
</div>
<div class="comment-right" template>
	<div class="contents-flex-container">
		<div class="contents-container box">
			<div style="padding: 10px">
				<div id="details">
					<div style="float: left">posted <span id="date"></span> by <a id="name"></a></div>
					<div style="display: inline-block; width: 15px">&nbsp;</div>
					<div style="float: right"><a>.report</a></div>
					<div style="clear:both"></div>
				</div>
				<div id="contents"></div>
			</div>
		</div>
	</div>
	<div class="profile-container">
		<a target="__blank"><img width="100"></a>
	</div>
</div>
<style>
		.cog img {
		margin-bottom: -3px;
		transition: transform 0.4s;
	}

	.cog[active] img {
		transform: rotate(90deg)
	}

	.cog[active] {
		background: linear-gradient(0deg,rgb(156, 55, 223) 0%, rgb(81, 34, 112) 100%);
	}

	.cog-dropdown {
		position: relative;
	}

	.cog-dropdown ul {
		display:none;
		position: absolute;
		list-style: none;
		text-align: left;
		width: 130px;
		min-width:fit-content;
		background: magenta;
		margin: 0px;
		padding: 0px;
		z-index: 10;
		border: 2px solid var(--border-color);
		left: 30px;
		top: 0px;
	}

	.cog-dropdown li {
		padding: 5px;
		user-select: none;
		background: linear-gradient(0deg,rgb(26, 12, 35) 0%, rgb(73, 34, 101) 100%);
		border-top: 1px solid var(--border-color);
		cursor: pointer;
	}

	.cog-dropdown li span.title {
		display: inline;
	} 

	.cog-dropdown li span {
		display: none;
	}

	.cog-dropdown li:hover span {
		display: inline;
	}

	.cog-dropdown li:first-child {
		border-top: none;
	}


	.cog-dropdown li:active {
		background: linear-gradient(180deg,rgb(26, 12, 35) 0%, rgb(73, 34, 101) 100%);
	}

	.cog-dropdown li:hover {
		filter: brightness(1.5)
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

	#place-name {
		height: auto;
		background: linear-gradient(0deg,#1a0c23 0%, #492265 100%);
		border: 1px solid var(--border-color);
		margin-top:4px;
	}

	#place-name-container:hover #place-short-name {
		display: none;
	}

	#place-name-container .hidden {
		display: none;
	}

	#place-name-container:hover .hidden h2 {
		position: absolute;
	}

	#place-name-container:hover .hidden {
		display: block;
		
	}
</style>
<h2 class="page-title">.place</h2>
<div class="box" style="display: flex; padding: 10px; position: relative;">
	<div style="flex: 1">
		<div class="image-container">
			<a href="<?= $place->getThumbsUrl() ?>" target="__blank">
				<img src="<?= $place->getThumbsUrl() ?>">
			</a>
		</div>
	</div>
	
	<div style="flex: 1; text-align: center; margin: 10px; position: relative;">
		<div style="min-height:97px; height: 97px; display: flex; flex-direction: column;">
			<div id="place-name-container" style="flex: 1">
				<?php if($place_short_name): ?>
				<h2 id="place-short-name"><?= $place_short_name ?></h2>
				<div class="hidden">
					<h2 id="place-name"><?= $place->name ?></h2>
					<div style="height: 78px;"></div>
				</div>
				<?php else: ?>
					<h2 id="place-name" style="background: none; border: none; flex: 1"><?= $place->name ?></h2>
				<?php endif ?>
			</div>
			<div style="font-size: 14px; font-style: italic; margin-bottom: 15px;">created by <a href=""><?= $place->creator->name ?></a></div>
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
			<?php if($is_creator): ?>
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
		<hr style="margin-left: 5px; margin-right: 5px;">
		<div style="margin: 10px 15px; line-height: 1.5em; font-family: monospace; font-size: 11px; color: #EFD8FF">
			<?= $place_description ?>
		</div>
		<hr style="margin-left: 5px; margin-right: 5px;">
		<table id="place-stats">
			<td>
				<b>.created</b>
				<span><?= $place->created_at->format('d/m/Y'); ?></span>
			</td>
			<td>
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
	</div>
	<div class="box" data-tab="badges">
		<h3 style="margin: 5px;">.badges</h3>
	</div>
	<div class="box" data-tab="servers">
		<h3 style="margin: 5px;">.servers</h3>
	</div>
</div>
<div style="margin-top: 5px;">
	<h4 class="page-title">.commentary</h4>
	<div class="box" style="padding: 10px 20px" id="comment-post-container">
		<h3 class="page-slogan">.post_something_cool_about_this!</h3>
		<textarea maxlength="256" minlength="4" class="box input" style="width: 914px" placeholder="hurr durr i love this thing!"></textarea>
		<div class="comment-error">you did something bad: <span></span></div>
		<button class="button" style="margin-top: 5px">submit</button>
	</div>
	<style>
		.comment-error {
			padding: 5px 2px;
			padding-top: 8px;
			font-weight: bold;
			color: red;
			display: none;
		}
		.comment {
			padding: 5px;
			display: flex;
			font-family: "Fira Mono", monospace;
		}

		.comment .profile-container img {
			border: 2px solid var(--border-color);
		}

		.comment .contents-flex-container {
			flex: 1;
			padding: 0px 5px;
			padding-left: 22px;
		}

		.comment .contents-container, .comment[right] .contents-container {
			border: 2px solid var(--border-color);
			max-height: 150px;
			position: relative;
			width: fit-content;
		}

		.comment[right] .contents-container {
			margin-left: auto;
			margin-right: 20px;
		}

		.comment .contents-container:after, .comment[right] .contents-container:after {
			content: '';
			position: absolute;
			top: 0px;
			width: 0;
			height: 0;
			border: 20px solid transparent;
			border-top: 0;
			margin-top: -2px;
		}

		.comment .contents-container:after {
			left: 0;
			right: auto;
			border-right-color: var(--border-color);
			border-left: 0;
			margin-left: -20px;
		}

		.comment[right] .contents-container:after {
			left: auto;
			right: 0;
			border-left-color: var(--border-color);
			border-right: 0;
			margin-right: -20px;
		}

		.comment #details {
			font-size: 12px;
			border-bottom: 1px solid var(--lighter-border-color);
			padding-bottom: 5px;
			color: #CCC;
			font-size: 11px;
			font-style: italic;
		}

		.comment #contents {
			padding: 5px;
			max-width: 730px;
			max-height: 101px;
			overflow: auto;
			overflow-wrap: break-word;
		}
	</style>
	<div style="padding: 5px">
		<div id="statuses">
			<div class="status" id="loading-status">
				<img src="/public/images/ProgressIndicator4White.gif" width="90">
				<br>
				<b>loading comments...</b>
			</div>
			<div class="status" id="nothing-status">
				<img src="/public/images/noassets.png" width="110">
				<br>
				<b>there's no discussion here!</b>
			</div>
		</div>
		<div id="comments-container"></div>
		<div id="comments-pager" style="margin: 0 auto;text-align: center">
			<hr>
			<a href="javascript:ANORRL.Comments.PrevPage()" id="back-pager">&lt;&lt; back</a>
			<input class="box input" type="text" maxlength="3" value="1" style="width: 25px;text-align: center;padding: 2px 4px;"> of <span id="page-counter">1</span>
			<a href="javascript:ANORRL.Comments.NextPage()" id="next-pager">next &gt;&gt;</a>
		</div>
	</div>
</div>
<script>
	$(window).click(function() {
		$(".cog").each(function() {
			$(this).removeAttr("active");
		})
		$(".cog-dropdown ul").each(function() {
			$(this).css("display", "none");
		})
	});
	<?php if($place->isStartingPlace()): ?>
	$(".cog-dropdown li").click(function() {
		var action = $(this).data("actionid");

		if(action == 1) {
			window.location.href = "/develop/<?= $place->id ?>/configure";
		}
		else if(action == 4) {
			$.post("/api/universes/"+universe+"/shutdown", function(data) {
				if(!data['success'])
					alert(data['reason']);
				else
					alert("success!");
			})
		}
		else if(action == 5) {
			alert("not legible for sex...");
		}
	});
	<?php endif ?>

	$(".cog").click(function() {
		event.stopPropagation();

		$(".cog").each(function() {
			$(this).removeAttr("active");
			$(this).parent().find("ul").css("display", "none");
		});

		$(this).attr("active",true);
		$(this).parent().find("ul").css("display", "block");
	})
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
				alert(data['reason']);
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
<?php $page->loadFooter2(); ?>
