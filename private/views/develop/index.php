<?php
	use anorrl\Page;

	$page = new Page("Develop", "develop");

	$page->loadHeader();
?>
<style>
	#cards {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap:10px;
		margin-top: 10px;
	}
	#cards .box {
		padding: 15px;
		position:relative;
	}

	#cards .box h2 {
		margin: 5px 0px;
		text-align: center;
	}

	#cards .box p {
		font-size: 13px;
		text-align: center;
	}

	#cards .bg-image {
		position: absolute;
		inset: 0px;
		background-size: 100%;
		background-position: 10px;
		opacity: 0.075;
		pointer-events: none;
		background-repeat: no-repeat;
	}
</style>
<h2 class="page-title">.develop</h2>
<div class="box" style="text-align: center; padding: 15px;position: relative;">
	<img src="/public/images/characters/construction.png" style="position: absolute;right: 19px;width: 130px;image-rendering: crisp-edges;" title="hey! back off!">
	<img src="/public/images/download/studio.png" width="190">
	<h2>up for something new? try developing a game for once!</h2>
	<h4>what's it up for you? well me of course! get on with it!</h4>
	<input class="button" type="submit" value="Open ANORRL Studio"><br><br>
	<a href="/develop/creations/games">oh just want to check your items? yeah ok thats fine i guess.</a>
</div>
<div id="cards">
	<div class="box">
		<h2>have fun!</h2>
		<p>try pushing yourself for new things!<br>create the game of your dreams!</p>
		<p>(or not) the choice is yours!</p>
		<div class="bg-image" style="background-image: url('/public/images/characters/atlas.png');"></div>
	</div>
	<div class="box">
		<h2>cloud editing!</h2>
		<p>create stuff with your friends TOGETHER!</p>
		<p>at the same time! crazy right???</p>
		<div class="bg-image" style="background-image: url('/public/images/characters/vic_em.png');"></div>
	</div>
	<div class="box">
		<h2>constant support!</h2>
		<p>need help with trying to get started?<br>we're here!</p>
		<p>as long as you put the effort in, we're here to help!</p>
		<div class="bg-image" style="background-image: url('/public/images/characters/em.png');"></div>
	</div>
</div>
<div style="margin-top:10px">
	<div style="float:left"><h2 style="margin:0px;background: black; padding: 5px 15px;">.resources &gt;&gt;&gt;</h2></div>
	<div style="text-align: right">
		<button class="button">ANORRL Wiki</button>
		<button class="button">ANORRL Tutorials</button>
	</div>
	<div style="clear: both"></div>
</div>
<!-- TODO: when the site releases, add games created on the platform here 
<div class="box" style="margin-top: 10px;">
	<h2>.curated_games</h2>
	<p>this is bleh (todo)</p>
	<p>add popular games to this area!</p>
</div>-->
<?php
	$page->loadFooter();
?>