<?php
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\utilities\Utilities;
	
	if(!isset($id))
		redirect("/my/stuff");

	use anorrl\Asset;
	use anorrl\Page;
	use anorrl\Place;
	use anorrl\Universe;

	$user = ARLAUTH ? SESSION->user : null;

	$asset = Asset::FromID($id);

	$is_creator = false;
	$is_favourited = false;
	$is_editable = false;

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
		$asset_thumbnail =  $asset->getThumbsUrl();
		if(strlen(trim($asset_description)) == 0) {
			$asset_description = <<<EOT
			<b>Seems like $asset_creator_name hasn't put anything here...</b>
			EOT;
		} else {
			$asset_description = str_replace(PHP_EOL, "<br>", $asset_description);
		}
	} else {
		$new_asset = Place::FromID($id);
		redirect($new_asset ? $new_asset->getURL() : "/my/stuff");
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
	$page->load3DScripts();
	$page->addStylesheet("/css/comments.css");
	$page->addStylesheet("/css/thumbnail.css");
	$page->addScript("/js/comments.js");
	$page->addScript("/js/thumbnails.js");
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
	.play-btn, .edit-btn, .purchase-btn {
		font-size: 14px;
		padding: 7px 50px;
		text-align: center;
		margin-bottom: 5px;
		color: white;
		width: 212px;
		padding: 0px;
		background: none;
		background-size: 636px;
		cursor: pointer;
		height: 54px;
		border: 0px;
		background-image: url("/public/images/buttons/item_buttons.png");
	}

	.play-btn:disabled,.play-btn[disabled],
	.edit-btn:disabled,.edit-btn[disabled],
	.purchase-btn:disabled, .purchase-btn[disabled] {
		filter: grayscale(1);
	}

	.edit-btn {
		background-position: -212px 0px;
	}

	.purchase-btn {
		background-position: -424px 0px;
	}

	.play-btn:hover,
	.edit-btn:hover,
	.purchase-btn:hover, {
		filter: brightness(1.15);
	}

	.play-btn:active {
		background-position: 0 54px;
	}

	.edit-btn:active {
		background-position: -212px 54px;
	}

	.purchase-btn:active {
		background-position: -424px 54px;
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

	.thumbnail-holder img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
</style>

<h2 class="page-title">.<?= strtolower($asset->type->label()) ?></h2>

<div class="box" style="display: flex; padding: 10px; position: relative;">
	<div style="flex: 1">
		<div style="text-align: center;">
			<div class="thumbnail-holder" width="300" height="300" style="width: 300px;height: 300px;">
				<?php if(AssetTypeUtils::IsRenderable($asset->type)): ?>
				<button id="thumbnail-switcher" data-3d></button>
				<span class="thumbnail-span" data-3d-url="/thumbnail/get?asset=<?= $asset->id ?>" style="width:300px;height:300px"></span>
				<?php endif ?>
				<img data-src="<?= $asset->getThumbsUrl() ?>" width="240">
			</div>
			<table id="controls" style="width: auto">
			<tr>
				<td width="90">
					<button style="color: #ffdb5b;" id="fav-btn">
						<img src="/public/images/buttons/favourite_star.gif" width="32">
						<span id="fav-count">0</span>
					</button>
				</td>
				<td width="90">
					<button style="color: #ed4b4b;" id="report-btn">
						<img src="/public/images/buttons/report_flag.gif" width="32">
					</button>
				</td>
			</tr>
		</table>
		</div>
	</div>

	<div style="flex: 1; text-align: center; margin: 10px; position: relative;">
		<div style="display: flex; flex-direction: column;">
			<h2 id="asset-name" style="background: none; border: none; "><?= $asset->name ?></h2>
			<div style="font-size: 14px; font-style: italic; margin-bottom: 15px;">created by <a href="<?= $asset->creator->getURL() ?>"><?= $asset_creator_name ?></a></div>
		</div>
		<hr>

		<button class="purchase-btn" disabled></button>
		
		<hr>
		<div style="text-align: left">
	
			<div style="margin: 10px 15px; line-height: 1.5em; font-family: monospace; font-size: 11px; color: var(--special-pink)">
				<?= $asset_description ?>
			</div>
			<hr>
			<table id="place-stats">
				<td title="<?= Utilities::GetTimeAgo($asset->created_at) ?>">
					<b>.created</b>
					<span><?= $asset->created_at->format('d/m/Y'); ?></span>
				</td>
				<td title="<?= Utilities::GetTimeAgo($asset->last_updatetime) ?>">
					<b>.updated</b>
					<span><?= $asset->last_updatetime->format('d/m/Y'); ?></span>
				</td>
			</table>
		</div>
	</div>
	
	<?php if($is_creator): ?>
	<div class="cog-dropdown" style="position: absolute; right: 10px">
		<button class="button cog" style="padding: 2px 4px" class=""><img src="/public/images/icons/cog.png" ></button>
		<ul>
			<li data-actionid="1"><span>&gt;</span> configure</li>
			<li data-actionid="2"><span>&gt;</span> advertise</li>
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

	.button[selected] {
		background: linear-gradient(0deg,#9c37df 0%, #512270 100%);
		filter: brightness(1.15);
		letter-spacing: 2px;
		text-decoration: underline !important;
	}
	.nothing {
		margin: 25px auto;
		font-size: 14px;
		font-family:monospace;
		text-align: center;
		color: var(--special-pink);
	}
</style>
<div style="margin-top: 5px;">
	<?php $page->loadTemplate("layouts/comments/main"); ?>
</div>
<script>
	<?php if($is_creator): ?>
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
	<?php endif ?>

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
