<?php
	use anorrl\Page;

	if(!isset($type))
		redirect("/develop/creations/games");

	if(!file_exists(__DIR__."/library/$type.php"))
		redirect("/develop/creations/games");

	$user = SESSION->user;

	$page = new Page("Creations");

	$page->clearAll();
	$page->addScript("/js/core/jquery.js");

	$page->addScript("/js/creations.js?t=1777052041");

	$page->loadHeader2();

	function generate($type, array $items = []) {

		foreach($items as $item) {
			if(strcmp($type, $item) == 0) {
				echo <<<EOT
				<li data-category="$item" class="button" selected>
					<span>
						<img src="/public/images/icons/selection.png">
						.$item
					</span>
				</li>
				EOT;	
			}
			else{
				echo <<<EOT
				<li data-category="$item" class="button"><span>.$item</span></li>
				EOT;
			}
		}
	}

	function generate_statuses() {
		echo <<<EOT
		<div id="statuses">
			<div class="status" id="loading-status">
				<img src="/public/images/ProgressIndicator4White.gif" width="90">
				<br>
				<b>finding items...</b>
			</div>
			<div class="status" id="nothing-status">
				<img src="/public/images/noassets.png" width="110">
				<br>
				<b>couldn't find any items like that...</b>
			</div>
		</div>
		EOT;
	}
?>
<style>
	ul.special .heading {
		user-select: none;
	}

	ul.special .heading h3 {
		letter-spacing: 5px;
		font-size: 13px;

		margin: -2px 3px;
		padding: 5px;
		text-align: center;
		font-weight: bold;
		font-style: italic;
	}
	/* prolly just make the h3 have borders... */
	ul.special .heading hr {
		margin: 5px 5px;
	}

	#panel {
		flex: 1;
	}

	#panel .box h2 {
		margin: 5px 0px;
	}

	div[data-loadtype] {
		padding: 5px;
	}

	div[data-loadtype][grid] {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
	}

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
		left: 49px;
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
<script>
	$(function() {
		$("li[data-category]").on("click", function() {
			var type = $(this).attr("data-category");
			if(type == "<?= $type ?>")
				return;

			window.location.href = "/develop/creations/"+type;
		})
	})
</script>
<h2 class="page-title">.creations</h2>
<div style="display: flex; align-items: flex-start; gap: 10px;">
	<div class="box" style="flex: 0.35">
		<h3 id="filters-heading">.categories</h3>
		<hr>
		<ul class="special">
			<div class="heading"><hr><summary><h3>development</h3></summary><hr></div>
			<?php generate($type, [
				"places",
				"games",
				"decals",
				"audio",
				"meshes",
				"models",
				"animations"
			]); ?>
			<div class="heading"><hr><h3>accessories</h3><hr></div>
			<?php generate($type, [
				"hats",
				"faces",
				"shirts",
				"t-shirts",
				"pants",
				"body_type",
				"emotes"
			]); ?>
			<?php if($user->isAdmin()): ?>
			<div class="heading"><hr><h3>admin</h3><hr></div>
			<li data-category="19"class="button"><span>.gears</span></li>
			<li data-category="1" class="button"><span>.images</span></li>
			<li data-category="5" class="button"><span>.lua</span></li>
			<?php endif ?>
		</ul>
	</div>
	<?php require __DIR__."/library/$type.php" ?>
</div>		
<?php
	$page->loadFooter2();
?>