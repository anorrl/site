<?php
	
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;
	
    //took this from games.php but idrc atp -skylerclock
	$randomsplash = new FileSplasher("titles/vandals")->getRandomSplash();

	$page = new Page("Vandals");
	$page->clearAll();

	$page->addScript("/js/core/jquery.js");
	$page->addScript("/js/vandals.js?t=1776253888");

	$page->loadHeader2();
?>
<style>
	#users-container a {
		font-weight: bold;
		font-size: 12px;
		letter-spacing: 2px;
	}

	#users-container td {
		text-align: center;
	}

	.user #status {
		word-break: break-word; overflow-wrap: anywhere;
		text-align: left;
	}

	.user #activity {
		font-size: 13px;
		font-weight: bold;
	}
</style>
<h2 class="page-title">.vandals</h2>
<h3 class="page-slogan"><?= $randomsplash ?></h3>
<div class="box" style="margin-bottom: 5px;">
	<div style="margin: 5px auto; text-align: center">
		<input class="box input" id="search-box" name="query" type="text" placeholder="look for users lol" style="width: 460px;">
		<input class="button" type="submit" value="search" onclick="ANORRL.People.Submit(); return false;">
	</div>
</div>
<div class="box">
	<table id="users-container">
		<tr>
			<th width="80" style="border:0">Avatar</th>
			<th width="200" style="border:0">Name</th>
			<th style="border:0; width: 600px; max-width: 600px;">Blurb</th>
			<th width="150" style="border:0">Active</th>
		</tr>
	</table>
	<div id="pager">
		<hr>
		<a href="javascript:ANORRL.People.PrevPage()" id="back-pager">&lt;&lt; back</a>
		<input class="box input" type="text" maxlength="3" value="1"> of <span id="page-counter">1</span>
		<a href="javascript:ANORRL.People.NextPage()" id="next-pager">next &gt;&gt;</a>
	</div>
</div>
<?php $page->loadFooter2(); ?>