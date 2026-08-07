<?php
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$page = new Page("Vandals", "vandals");
	$page->clearAll();

	$page->addScript("/js/core/jquery.js");
	$page->addScript("/js/vandals.js?t=1776253888");
	$page->addStylesheet("/css/vandals.css");

	$page->loadHeader2();
?>
<h2 class="page-title">.vandals</h2>
<h3 class="page-slogan"><?= new FileSplasher("titles/vandals")->getRandomSplash() ?></h3>
<div class="box" style="margin-bottom: 5px;">
	<div style="margin: 5px auto; text-align: center">
		<input class="box input" id="search-box" name="query" type="text" placeholder="look for users lol" style="width: 460px;">
		<input class="button" type="submit" value="search" onclick="ANORRL.Vandals.Submit(); return false;">
	</div>
</div>
<div class="box">
	<table id="users-container">
		<tr>
			<th width="80" style="border:0">.avatar</th>
			<th width="200" style="border:0">.name</th>
			<th style="border:0; width: 600px; max-width: 600px;">.blurb</th>
			<th width="150" style="border:0">.active</th>
		</tr>
	</table>
	<div id="pager">
		<hr>
		<a href="javascript:ANORRL.Vandals.PrevPage()" id="back-pager">&lt;&lt; back</a>
		<input class="box input" type="text" maxlength="3" value="1"> of <span id="page-counter">1</span>
		<a href="javascript:ANORRL.Vandals.NextPage()" id="next-pager">next &gt;&gt;</a>
	</div>
</div>
<?php $page->loadFooter2(); ?>