<?php

	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$randomsplash = new FileSplasher("titles/catalog")->getRandomSplash();

	$page = new Page("Catalog", "catalog");
	$page->clearAll();

	$page->addScript("/js/core/jquery.js");
	$page->addScript("/js/catalog.js?t=1776186351");

	$page->addStylesheet("/css/catalog.css");

	$page->loadHeader2();
?>
<div class="asset" title="" template>
	<a href="">
		<img src="/public/images/spinner100x100_white.gif" width="130">
		<div id="name"></div>
	</a>
	<div id="price"></div>
	
	<div id="info">
		<hr>
		<div id="creator"><a></a></div>
		<hr>
		<table>
			<tr id="sales">
				<td>sold:</td>
				<td id="count"> times</td>
			</tr>
			<tr id="favourites">
				<td>favourited:</td>
				<td id="count"> times</td>
			</tr>
		</table>
	</div>
</div>
<h2 class="page-title">.catalog</h2>
<h3 class="page-slogan"><?= $randomsplash ?></h3>
<div style="display: flex; gap: 10px;  align-items: flex-start;">
	<div style="flex: 0.35">
		<div class="box">
			<h3 id="filters-heading">.categories</h3>
			<hr>
			<ul class="special">
				<li class="button" data-category="8" selected="selected"><span>.hats</span></li>
				<li class="button" data-category="18"><span>.faces</span></li>
				<li class="button" data-category="11"><span>.shirts</span></li>
				<li class="button" data-category="2" ><span>.t-shirts</span></li>
				<li class="button" data-category="12"><span>.pants</span></li>
				<li class="button" data-category="19"><span>.gears</span></li>
				<li class="button" data-category="61"><span>.emotes</span></li>
				<li class="button" data-category="99"><span>.body_parts</span></li>
				<li class="button" data-category="32"><span>.packages</span></li>
			</ul>
		</div>
		<div class="box" style="margin-top: 5px;">
			<h3 id="filters-heading">.filters</h3>
			<hr>
			<ul class="special">
				<li class="button" data-filter="1" selected="selected"><span>.recently_uploaded</span></li>
				<li class="button" data-filter="2"><span>.recently_updated</span></li>
				<li class="button" data-filter="5"><span>.most_sold</span></li>
				<li class="button" data-filter="6"><span>.most_favourited</span></li>
				<li class="button" data-filter="3"><span>.oldest_uploaded</span></li>
				<li class="button" data-filter="4"><span>.oldest_updated</span></li>
			</ul>
		</div>
	</div>
	<div style="flex: 1">
		<div class="box" style="margin-bottom: 5px;">
			<div style="margin: 5px auto; text-align: center">
				<input class="box input" id="search-box" name="query" type="text" placeholder="look for awesome items!!!" style="width: 460px;">
				<input class="button" type="submit" value="search" onclick="ANORRL.Catalog.Submit(); return false;">
			</div>
		</div>
		<div class="box" style="padding: 10px;">
			<h2 id="category-name">.hats</h2>
			<h3 id="filter-name" style="font-size: 16px; margin-top: 0px; letter-spacing: 4px;">.random</h3>
			<hr>
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
			<div id="catalog-container"></div>
			<div id="pager">
				<hr>
				<a href="javascript:ANORRL.Catalog.PrevPage()" id="back-pager">&lt;&lt; back</a>
				<input class="box input" type="text" maxlength="3" value="1"> of <span id="page-counter">1</span>
				<a href="javascript:ANORRL.Catalog.NextPage()" id="next-pager">next &gt;&gt;</a>
			</div>
		</div>
	</div>
</div>

<?php $page->loadFooter2(); ?>