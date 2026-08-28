<?php 	
	use anorrl\Asset;
	use anorrl\Page;
	use anorrl\Place;
	use anorrl\Universe;
	use anorrl\utilities\AssetTypeUtils;
	use anorrl\enums\AssetType;

	$user = SESSION->user;

	if(!isset($id))
		redirect("/develop/creations");

	$asset = Asset::FromID($id);
	$universe = Universe::FromID($asset->universe);

	if(!$asset)
		redirect("/my/creations");

	if($asset->type == AssetType::PLACE) {
		$asset = Place::FromID($id);
		$universe = Universe::FromID($asset->universe);
	}

	if(!$asset->isOwner($user))
		redirect("/develop/creations");

	$_SESSION['ANORRL$Asset$ID'] = $asset->id;

	$sellable = AssetTypeUtils::IsSellable($asset->type) && !$asset->owner_only;
	$updateable = AssetTypeUtils::IsUpdateable($asset->type);

	$page = new Page("editing: ".htmlspecialchars($asset->name, ENT_QUOTES), "anorrl_asset");
	$page->addScript("/js/versions.js");

	$page->loadHeader();
?>
<script>
	function loadTab(type) {
		window.location.hash = type;
		var element = $("li[data-category=\""+type+"\"]")

		$("li[data-category]").each(function() {
			$(this).removeAttr("selected");
			$(this).find("img").remove();
		});
		
		element.attr("selected", "");
		element.find("span").prepend('<img src="/public/images/icons/selection.png">');
		$("[data-tabname]").css("display", "none")
		$("[data-tabname=\""+type+"\"]").css("display", "block");
	}
	$(function() {
		if(window.location.hash == "")
			loadTab("basic")
		else
			loadTab(window.location.hash.substring(1));

		$("li[data-category]").on("click", function() {
			var type = $(this).data("category");
			

			$("li[data-category]").each(function() {
				$(this).removeAttr("selected");
				$(this).find("img").remove();
			});

			
			if(type != "universe") {
				window.location.hash = type;
				$(this).attr("selected", "");
				$(this).find("span").prepend('<img src="/public/images/icons/selection.png">');
			}

			

			if(type == "universe") {
				var universe = $(this).data("uid");
				// still wondering about /develop/place/<id>/configure... i think ill scrap it...
				// but anyways universes are different stuff i dont want to fuck with and they have their OWN options sooo
				// yeah
				window.location.href = "/develop/universes/"+universe+"/configure";
			} else {
				$("[data-tabname]").css("display", "none")
				$("[data-tabname=\""+type+"\"]").css("display", "block");
			}
		})
	})

	ANORRL.Versions.CurrentAssetID = <?= $asset->id ?>
</script>
<style>
	.fields input[type="checkbox"] {
		margin: 0px;
	}

	.fields {
		*padding: 5px;
	}

	[data-tabname] {
		display: none;
	}
</style>
<h2 class="page-title">.editing: <?= htmlspecialchars($asset->name, ENT_QUOTES) ?></h2>
<div style="display: flex; align-items: flex-start; gap: 10px;">
	<div style="flex: 0.35; width:250px;min-width: 250px;max-width: 250px;">
		<div class="box">
			<h3 id="filters-heading">.configure</h3>
			<hr>
			<ul class="special">
				<li data-category="basic" class="button" selected><span><img src="/public/images/icons/selection.png"> .basic_settings</span></li>
				<?php if($sellable): ?>
					<li data-category="currency" class="button"><span>.currency</span></li>
				<?php endif ?>
				<?php if($asset instanceof Place): ?>
					<li data-category="place" class="button"><span>.place_settings</span></li>
					<?php if($asset->isStartingPlace()): ?>
						<li data-category="universe" title="configure the WHOLE game (external)" data-uid="<?= $asset->universe ?>" class="button"><span>-> .universe_settings</span></li>
					<?php endif ?>
				<?php endif ?>
				<?php if($updateable): ?>
					<li data-category="version" class="button"><span>.version_history</span></li>
				<?php endif ?>
			</ul>
		</div>
		<div class="box" style="margin-top:5px">
			<img src="/public/images/randoms/jermafwoomp.png" width="100%">
		</div>
	</div>
	<div style="flex: 1;">
		<div data-tabname="basic" >
			<div class="box" style="padding: 15px;">
				<h3 id="filter-name">.basic_settings</h3>
				<hr>
				<div data-action="/develop/<?= $asset->id ?>/configure/settings">
					<div style="padding: 5px;">
						<div class="fields">
							<span>name</span>
							<input class="box input" name="ANORRL$EditItem$Name" type="text" placeholder="whats a cool name" minlength="2" maxlength="110" value="<?= $asset->name ?>" style="width:662px">
						</div>
						<div class="fields">
							<span>description</span>
							<textarea class="box input" name="ANORRL$EditItem$Description" style="width:662px; max-width: 662px; height:60px"><?= $asset->description ?></textarea>
						</div>
					</div>
					<?php if($asset->type != AssetType::BADGE): ?>
					<div class="box" style="margin: 5px; padding: 10px;">
						<div class="fields" style="padding: 5px">
							<input type="checkbox" name="ANORRL$EditItem$PublicBox" <?php if($asset->public): ?>checked<?php endif ?>>
							<span>public</span>
						</div>
						<div class="fields" style="padding: 5px; padding-top: 0px;">
							<input type="checkbox" name="ANORRL$EditItem$CommentsBox" <?php if($asset->comments_enabled): ?>checked<?php endif ?>>
							<span>comments</span>
						</div>
					</div>
					<?php else: ?>
					<div class="fields">
						<span>secret</span>
						<input type="checkbox" name="ANORRL$EditItem$PublicBox" <?php if($asset->secret): ?>checked<?php endif ?>>
					</div>
					<?php endif ?>
					<input class="button" type="submit" value="update" style="margin-top:5px;">
				</div>
			</div>
		</div>
		<?php if($sellable): ?>
		<div data-tabname="currency">
			<div class="box" style="padding: 15px;">
				<h4 id="filter-name">.pricing</h4>
				<hr>
				<form data-action="/develop/<?= $asset->id ?>/configure/pricing">
					<input class="button" type="submit" value="update" style="margin-top:5px;">
				</form>
			</div>
		</div>
		<?php endif ?>
		<?php if($asset instanceof Place): ?>
		<div data-tabname="place">
			<div class="box" style="padding: 15px;">
				<h3 id="filter-name">.place_settings</h3>
				<hr>
				<form method="POST" action="/develop/<?= $asset->id ?>/configure/place">
					<select class="box input">
						<option>Genre</option>
					</select>
					<input class="button" type="submit" value="update" style="margin-top:5px;">
				</form>
			</div>
		</div>
		<?php endif ?>
		<?php if($updateable): ?>
		<div data-tabname="version">
			<div class="box" style="padding: 15px;">
				<h3 id="filter-name">.version_history</h3>
				<hr>
				
				<table cellspacing="10" style="width: 60%; margin: 0 auto" id="versions-container">
					<tr header>
						<td align="center">
							<strong>.action</strong>
						</td>
						<td align="center">
							<strong>.version</strong>
						</td>
						<td align="center">
							<strong>.created</strong>
						</td>
					</tr>
				</table>
				<div id="pager">
					<hr>
					<a href="javascript:ANORRL.Versions.PrevPage()" id="back-pager">&lt;&lt; back</a>
					<input class="box input" type="text" maxlength="3" value="1"> of <span id="page-counter">1</span>
					<a href="javascript:ANORRL.Versions.NextPage()" id="next-pager">next &gt;&gt;</a>
				</div>
			</div>
		</div>
		<?php endif ?>
	</div>
</div>		
<?php $page->loadFooter(); ?>