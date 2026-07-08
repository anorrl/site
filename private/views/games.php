<?php
	
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$randomsplash = new FileSplasher("games")->getRandomSplash();

	$page = new Page("Games");
	$page->clearAll();

	$page->addScript("/js/old/games.js?t=1777052041");

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
	}
</style>
<h2 class="page-title">.games</h2>
<div style="display: flex; gap: 10px;  align-items: flex-start;">
	<div class="box" style="flex: 0.35;">
		<h3 style="margin-top: 6px; margin-bottom: 4px; letter-spacing: 6px; font-size: 15px; margin-left: 10px;">.filters</h3>
		<hr>
		<ul>
			<li data-filter="7" selected="selected"><a>Most Popular</a></li>
			<li data-filter="8"><a>Most Visited</a></li>
			<li data-filter="6"><a>Most Favourited</a></li>
			<li data-filter="1"><a>Recently Created</a></li>
			<li data-filter="2"><a>Recently Updated</a></li>
		</ul>
	</div>
	<div style="flex: 1;">
		<div class="box" style="margin-bottom: 5px;">
			<div id="FormPanel" style="margin: 5px auto; text-align: center">
				<input class="box input" id="SearchBox" name="query" type="text" placeholder="look for awesome games!!!" style="width: 460px;">
				<input class="button" id="Submit" type="submit" value="search" onclick="ANORRL.Games.Submit(); return false;">
			</div>
		</div>
		<div class="box" style="padding: 10px;">
			<h3 style="margin-top: 6px; margin-bottom: 4px; letter-spacing: 8px;font-size: 18px;margin-left: 6px;">.random</h3>
			<hr>
			<div id="games-container">
				<?php for($i = 0; $i < 9; $i ++): ?>
					<?php
						$name = md5(rand());
						$name = substr($name, 0, rand(0, strlen($name)));
					?>
				<div class="game" title="<?= $name ?> by <?= "creator" ?>">
					<a href="">
						<img src="/thumbs/?id=573" width="128">
						<div id="name"><?= $name ?></div>
					</a>
					<div id="currently-playing">0 players online</div>
					<div id="info">						
						<div id="creator">by <a href="/user/1/profile">creator</a></div>
						<div id="visit-count">played 1 billion times</div>
					</div>
				</div>
				<?php endfor ?>
			</div>
			<hr>
			<div id="pager">
				<a href="javascript:ANORRL.Games.PrevPage()" id="back-pager">&lt;&lt; back</a>
				<input class="box input" type="text"  maxlength="3" style="padding: 2px;width: 21px;border-width: 2px;text-align: center;" value="1"> of <span id="page-counter">1</span>
				<a href="javascript:ANORRL.Games.NextPage()" id="next-pager">next &gt;&gt;</a>
			</div>
		</div>
	</div>
</div>
<?php $page->loadFooter2() ?>
