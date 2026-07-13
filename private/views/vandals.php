<?php
	
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;
	
    //took this from games.php but idrc atp -skylerclock
	$randomsplash = new FileSplasher("titles/vandals")->getRandomSplash();

	$page = new Page("Vandals");

	$page->addStylesheet("/css/new/forms.css");
	$page->addStylesheet("/css/new/people.css");

	$page->addScript("/js/people.js?t=1776253888");

	$page->loadHeader();
?>
<h2 style="margin: 0px; margin-top: 10px; width: 850px;"><marquee behavior="alternate" scrollamount="10"><?= $randomsplash ?></marquee></h2>
<div id="Users">
	<div method="GET" id="FormPanel">
		<input id="SearchBox" name="query" type="text" placeholder="Look for users lol">
		<input id="Submit" type="submit" value="Search" onclick="ANORRL.People.Submit(); return false;">
	</div>
	<table id="UsersDataTable">
		<tr>
			<th width="80" style="border:0">Avatar</th>
			<th width="200" style="border:0">Name</th>
			<th style="border:0; width: 600px; max-width: 600px;">Blurb</th>
			<th width="150" style="border:0">Active</th>
		</tr>
	</table>
	<div id="UsersNavLinks">
		<a id="BackPager" href="javascript:ANORRL.People.DeadvanceFeed()">&lt;&lt; Back</a> <input maxlength="4" id="NumberPutter"> of <span id="Counter"></span> <a id="NextPager" href="javascript:ANORRL.People.AdvanceFeed()">Next &gt;&gt;</a>
	</div>
</div>
<?php $page->loadFooter(); ?>