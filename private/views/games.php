<?php
	
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$randomsplash = new FileSplasher("titles/games", false, 'Games$Random$Splash')->getRandomSplash();

	$page = new Page("Games");
	$page->clearAll();

	$page->addScript("/js/core/jquery.js");

	$page->addScript("/js/games.js?t=1777052041");

	$page->loadHeader2();
?>
<style>

	#games-container {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
	}

	.game {
		text-align: center;
		padding: 10px;
		user-select: none;
	}
	
	.game #name {
		font-size: 13px;
		font-weight: bold;
		width: 181px;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
		padding-top: 5px;
		text-align: center;
		margin: 0 auto;
	}

	.game img {
		width: 180px;
	}

	.game:hover {
		border: 2px solid var(--border-color);
		margin: -2px;
		background: linear-gradient(180deg,rgb(26, 12, 35) 0%, rgb(73, 34, 101) 100%);
		margin-bottom: -31px;
		z-index: 9999;
	}

	.game #info {
		display: none;
	}

	.game:hover #info {
		display: block;
	}

	.game #currently-playing {
		color: #d269e7;
		letter-spacing: 1px;
		
	}

	.game #currently-playing[active] {
		font-weight: bold;
	}

	.game #info #visit-count {
		font-style: italic;
		color: #d3a5e7;
	}

	.game #info #creator {
		font-weight: bold;
		font-size: 13px;
	}

	ul {
		list-style-type: none;
		margin: 0px;
		padding: 5px;
	}

	li[data-filter] {
		margin: 5px;
		text-align: center;
		font-size: 13px;
	}
	li[data-filter][selected] {
		font-weight: bold;
		font-size: 14px;
		text-decoration: underline;
		filter:brightness(1.5);
		background: linear-gradient(0deg,rgb(156, 55, 223) 0%, rgb(81, 34, 112) 100%);
		letter-spacing: 2px;
	}

	li[data-filter]:hover {
		text-decoration: underline;
	}

	li[data-filter] span {
		width: 160px;
		text-align: left;
		display: block;
		margin: 0 auto;
	}

	li[data-filter] span img {
		position: absolute;
		left: 7px;
	}

	#filters-heading, #filter-name {
		margin-top: 6px;
		margin-bottom: 4px;
		letter-spacing: 6px;
		font-size: 15px;
		margin-left: 10px;
	}

	#filter-name {
		letter-spacing: 8px;
		font-size: 18px;
		margin-left: 6px;
	}

	#pager .box.input {
		padding: 2px;width: 21px;border-width: 2px;text-align: center;
	}

	.status {
		font-size: 18px;
		text-align: center;
		margin: 30px auto;
		display: none;
	}

	#pager hr {
		margin:-15px; margin-bottom: 15px;margin-top:-10px;
	}
</style>
<div class="game" title="" template>
	<a href="">
		<img src="" width="128">
		<div id="name"></div>
	</a>
	<div id="currently-playing"></div>
	<div id="info">						
		<div id="creator"><a></a></div>
		<div id="visit-count"></div>
	</div>
</div>
<h2 class="page-title">.games</h2>
<h3 class="page-title" style="margin-left: 1px"><?= $randomsplash ?></h3>
<div style="display: flex; gap: 10px;  align-items: flex-start;">
	<div class="box" style="flex: 0.35;">
		<h3 id="filters-heading">.filters</h3>
		<hr>
		<ul>
			<li data-filter="9" class="button" selected="selected"><span>.random</span></li>
			<li data-filter="7" class="button"><span>.most_popular</span></li>
			<li data-filter="8" class="button"><span>.most_visited</span></li>
			<li data-filter="6" class="button"><span>.most_favourited</span></li>
			<li data-filter="1" class="button"><span>.recently_created</span></li>
			<li data-filter="2" class="button"><span>.recently_updated</span></li>
		</ul>
	</div>
	<div style="flex: 1;">
		<div class="box" style="margin-bottom: 5px;">
			<div style="margin: 5px auto; text-align: center">
				<input class="box input" id="search-box" name="query" type="text" placeholder="look for awesome games!!!" style="width: 460px;">
				<input class="button" type="submit" value="search" onclick="ANORRL.Games.Submit(); return false;">
			</div>
		</div>
		<div class="box" style="padding: 10px;">
			<h3 id="filter-name">.random</h3>
			<hr>
			<div id="statuses">
				<div class="status" id="loading-status">
					<img src="/public/images/ProgressIndicator4White.gif" width="90">
					<br>
					<b>finding games...</b>
				</div>
				<div class="status" id="nothing-status">
					<img src="/public/images/noassets.png" width="110">
					<br>
					<b>couldn't find any games like that...</b>
				</div>
			</div>
			<div id="games-container"></div>
			
			<div id="pager">
				<hr>
				<a href="javascript:ANORRL.Games.PrevPage()" id="back-pager">&lt;&lt; back</a>
				<input class="box input" type="text" maxlength="3" value="1"> of <span id="page-counter">1</span>
				<a href="javascript:ANORRL.Games.NextPage()" id="next-pager">next &gt;&gt;</a>
			</div>
		</div>
	</div>
</div>
<?php $page->loadFooter2() ?>
