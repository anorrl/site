<?php
	if(!isset($id))
		redirect("/my/stuff");

	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\Page;
	use anorrl\Universe;
	use anorrl\utilities\Utilities;

	$user = ARLAUTH ? SESSION->user : null;

	$asset = Asset::FromID($id);

	$is_creator = false;
	$is_favourited = false;

	if($asset != null) {
		
		if($asset->getURLTitle() != $name) {
			redirect($asset->getURL());
		}

		if($user != null) {
			$is_creator = $asset->isOwner($user);
			$is_favourited = $asset->hasUserFavourited($user);
		}

		$favourites_label = $asset->favourites_count . " time". ($asset->favourites_count != 1 ? "s" : "");
		
		$asset_creator_name = $asset->creator->name;
		$asset_description = $asset->description;
		if(strlen(trim($asset_description)) == 0) {
			$asset_description = <<<EOT
			<b>Seems like $asset_creator_name hasn't put anything here...</b>
			EOT;
		} else {
			$asset_description = str_replace(PHP_EOL, "<br>", $asset_description);
		}
	} else {

		$new_place = Place::FromID($id);
		if($new_place == null) {
			redirect("/my/stuff");
		} else {
			redirect($new_place->getURL());
		}
	}

	if(ARLAUTH) {
		$_SESSION['ANORRL$Asset$ID'] = $asset->id;
	}
	else {
		if(isset($_SESSION['ANORRL$Asset$ID']))
			unset($_SESSION['ANORRL$Asset$ID']);
	}

	$asset_short_name = null;
	if(strlen($asset->name) > 35 ) {
		$asset_short_name = trim(substr($asset->name, 0, 35)). "...";
	}

	$page = new Page(htmlspecialchars($asset->name, ENT_QUOTES), "anorrl_asset");
	$page->addStylesheet("/css/comments.css");
	$page->addScript("/js/comments.js");
	$page->addScript("/js/ratings.js");
	$page->load3DScripts();
	$page->addValue("asset", $asset->id);
	$asset->loadEmbed($page);
	
	$page->loadHeader();
?>
<?php $page->loadTemplate("layouts/comments/templates"); ?>
<style>
	.cog-dropdown ul {
		left: 30px;
	}
</style>
<style>
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

	#asset-name, #asset-short-name {
		font-size: 25px;
		font-weight: 400;
		margin-top: 5px;
		margin-bottom: 5px;
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

<h2 class="page-title">.<?= strtolower($asset->type->label()); ?></h2>

<div class="box" style="display: flex; padding: 10px; position: relative;">
	<div style="flex: 1">
		<div class="thumbnail-holder" width="320" height="320" style="text-align: center">
			<button id="thumbnail-switcher" data-3d></button>
			<span class="thumbnail-span" data-3d-url="/thumbnail/get?asset=<?= $asset->id ?>" style="width:300px;height:300px"></span>
			<img data-src="<?= $asset->getThumbsUrl() ?>" width="320" height="320" style="object-fit: cover">
		</div>
	</div>
	
	<div style="flex: 1; text-align: center; margin: 10px; position: relative;">
		<div style="min-height:97px; height: 97px; display: flex; flex-direction: column;">
			<div id="asset-name-container" style="flex: 1">
				<?php if($asset_short_name): ?>
				<h2 id="asset-short-name"><?= $asset_short_name ?></h2>
				<div class="hidden">
					<h2 id="asset-name"><?= $asset->name ?></h2>
					<div style="height: 78px;"></div>
				</div>
				<?php else: ?>
					<h2 id="asset-name" style="background: none; border: none; flex: 1"><?= $asset->name ?></h2>
				<?php endif ?>
			</div>
			<div style="font-size: 14px; font-style: italic; margin-bottom: 15px;">created by <a href="<?= $asset->creator->getURL() ?>"><?= $asset_creator_name ?></a></div>
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
		</div>
		<hr>
		<table id="controls">
			<tr>
				<td width="90">
					<button style="color: #ffdb5b;" id="fav-btn">
						<img src="/public/images/buttons/favourite_star.gif" width="32">
						<span id="fav-count"><?= $asset->favourites_count ?></span>
					</button>
				</td>
			</tr>
		</table>
	</div>
	<?php if($is_creator): ?>
	<div class="cog-dropdown" style="position: absolute; right: 10px">
		<button class="button cog" style="padding: 2px 4px" class=""><img src="/public/images/icons/cog.png" ></button>
		<ul>
			<li data-actionid="1"><span>&gt;</span> configure</li>
			<li data-actionid="2"><span>&gt;</span> advertise</li>
			<li data-actionid="3"><span>&gt;</span> shutdown all servers</li>
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
	<a class="button" href="#sales" data-tab="sales">sales</a>
</div>
<div style="margin-top: 5px;">
	<div class="box" data-tab="info" >
		
	</div>
	<div class="box" data-tab="sales">
		
	</div>
</div>
<div style="margin-top: 5px;">
	<?php $page->loadTemplate("layouts/comments/main"); ?>
</div>
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

	$(".cog-dropdown li").click(function() {
		var action = $(this).data("actionid");

		if(action == 1) {
			window.location.href = "/develop/<?= $asset->id ?>/configure";
		}
	});
	

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
		$.post("/asset/<?= $asset->id ?>/favourite", function (data) {
			if(!data['success'])
				ANORRL.MessageBox.Show(ANORRL.MessageBox.Type.ERROR, data['reason']);
			else
				$("#fav-count").html(data['count']);
		})
	});
</script>
<?php $page->loadFooter(); ?>
