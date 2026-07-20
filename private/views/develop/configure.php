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

	$sellable = AssetTypeUtils::IsSellable($asset->type) && !$asset->owner_only;

	$page = new Page("editing: ".htmlspecialchars($asset->name, ENT_QUOTES));
	$page->loadHeader2();
?>
<script>
	$(function() {
		$("li[data-category]").on("click", function() {
			var type = $(this).attr("data-category");
			var universe = $(this).attr("data-uid");

			if(type == "universe") {
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
</script>
<h2 class="page-title">.editing: <?= htmlspecialchars($asset->name, ENT_QUOTES) ?></h2>
<div style="display: flex; align-items: flex-start; gap: 10px;">
	<div class="box" style="flex: 0.35">
		<h3 id="filters-heading">.configure</h3>
		<hr>
		<ul class="special">
			<li data-category="basic" class="button"><span>.basic_settings</span></li>
			<?php if($sellable): ?>
			<li data-category="currency" class="button"><span>.currency</span></li>
			<?php endif ?>
			<?php if($asset instanceof Place): ?>
				<li data-category="place" class="button"><span>.place_settings</span></li>
				<?php if($asset->isStartingPlace()): ?>
				<li data-category="universe" data-uid="<?= $asset->universe ?>" class="button"><span>-> .universe_settings</span></li>
				<?php endif ?>
			<?php endif ?>
		</ul>
	</div>
	<div style="flex: 1;">
		<div data-tabname="basic" >
			<div class="box" style="padding: 15px;">
				<h3 id="filter-name">.basic_settings</h3>
				<hr>
				<form method="POST" action="/develop/<?= $asset->id ?>/configure/settings">
					<input class="box input" type="text" placeholder="whats a cool name" minlength="2" value="<?= $asset->name ?>" style="width:662px">
					<textarea class="box input" style="margin-top:5px;width:662px; max-width: 662px; height:60px"><?= $asset->description ?></textarea>
					<input class="button" type="submit" value="update" style="margin-top:5px;">
				</form>
			</div>
		</div>
		<?php if($sellable): ?>
		<div data-tabname="currency" style="display: none">
			<div class="box" style="padding: 15px;">
				<h4 id="filter-name">.pricing</h4>
				<hr>
				<form method="POST" action="/develop/<?= $asset->id ?>/configure/pricing">
					<input class="button" type="submit" value="update" style="margin-top:5px;">
				</form>
			</div>
		</div>
		<?php endif ?>
		<?php if($asset instanceof Place): ?>
		<div data-tabname="place" style="display: none">
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
	</div>
</div>		
<?php $page->loadFooter2(); ?>