<?php
	
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$randomsplash = new FileSplasher("titles/games", false, 'Games$Random$Splash')->getRandomSplash();

	$page = new Page("Games", "games");
	$page->clearAll();

	$page->addScript("/js/core/jquery.js");
	$page->addScript("/js/jquery.lazy.min.js");

	$page->addScript("/js/games.js?t=1777052041");
	$page->addStylesheet("/css/games.css");

	$page->loadHeader2();
?>
<div class="game" title="" template>
	<a href="">
		<img src="/public/images/spinner100x100_white.gif" width="128">
		<div id="name"></div>
	</a>
	<div id="currently-playing"></div>
	<div id="info">
		<div id="creator">by <a></a></div>
		<div id="visit-count"></div>
	</div>
</div>
<h2 class="page-title">.games</h2>
<h3 class="page-slogan"><?= $randomsplash ?></h3>
<div style="display: flex; gap: 10px;  align-items: flex-start;">
	<div class="box" style="flex: 0.35;">
		<h3 id="filters-heading">.filters</h3>
		<hr>
		<ul class="special">
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
