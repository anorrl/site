<?php
	session_start();

	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die(header("Location: /login"));
	}

	$randomclientsplashes = [
		"Games",
		"Games!",
		"RETRO GAMES RAHHH!!!!!",
		"VIDEO GAMES!!!!",
		"i wonder if there's some good games today...",
		"mmm time for sum of dat goooood shit"
	];

	$randomclientsplash = $randomclientsplashes[array_rand($randomclientsplashes)];
?>
<!DOCTYPE html>
<html> 
	<head>
		<title>Games - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/forms.css">
		<link rel="stylesheet" href="/css/new/games.css?v=5">
		
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/games.js?t=1773595203"></script>
	</head>
	<body>
		<div class="Game" template>
			<div id="ImageContainer">
				<div id="FavouritesArea"><img src="/images/favourite_star.gif"> <span>0</span></div>
				<div id="OriginalArea"><span>Original</span></div>
				<div id="YearArea"><span></span></div>
				<img src="">
			</div>
			<div id="Info">
				<a href="" id="GameName">Game Name</a>
				<hr>
				<span>By <a href="" id="GameCreator">creator</a></span>
				<div id="Stats">
					<span id="ActivePlayerCountLabel" style="color: #a93cac;"><b id="ActivePlayerCount">0</b> Player<span id="Plural">s</span> online...</span><br>
					<span id="VisitCountLabel" style="color:#8a8a8a;font-style:italic;"><b id="VisitCount">0</b> Visit<span id="Plural">s</span></span>
				</div>
				
			</div>
		</div>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<h2 style="margin: 0px;width: 850px;"><marquee behavior="alternate" scrollamount="10"><?= $randomclientsplash ?></marquee></h2>
					<div id="GamesContainer">
						<div id="GamesFilterPanel">
							<h4>Filters</h4>
							<div style="text-align: center;margin: 5px;border: 2px solid black;padding: 5px;padding-top: 3px;background: #111;color: #ffc63f;text-decoration: none;color: #ffa634;">
								<label for="ANORRL_Games_OriginalGamesInput">Original Only</label>
								<input id="ANORRL_Games_OriginalGamesInput" type="checkbox">
							</div>
							<ul>
								<li data_filter="7"><a>Most Popular</a></li>
								<li data_filter="8"><a>Most Visited</a></li>
								<li data_filter="6"><a>Most Favourited</a></li>
								<li data_filter="1"><a>Recently Created</a></li>
								<li data_filter="2"><a>Recently Updated</a></li>
							</ul>
						</div>
						<div id="Games">
							<div method="GET" id="FormPanel" style="margin: 5px auto;">
								<input id="SearchBox" name="query" type="text" placeholder="Look for awesome games!!!" style="width: 460px;">
								<input id="Submit" type="submit" value="Search" onclick="ANORRL.Games.Submit(); return false;">
							</div>
							<div id="StatusText">
								<b id="Loading" style="display: none">Loading assets...</b>
								<b id="NoAssets" style="display: none"><img src="/images/noassets.png" style="width: 110px;display: block;margin: 0 auto;margin-bottom: -92px;margin-top: 23px;">No games like that here!</b>
							</div>
						
							<div id="ContainerThingy">
								
							</div>
							
							<div id="Paginator" style="display: block;">
								<a id="BackPager" href="javascript:ANORRL.Games.PrevPage()" style="display: none;">&lt;&lt; Back</a> <input type="text" id="NumberPutter" maxlength="3"> of <span id="Counter">1</span> <a id="NextPager" href="javascript:ANORRL.Games.NextPage()" style="display: none;">Next &gt;&gt;</a>
							</div>
							
						</div>
						<br style="display:block; clear: both;">
					</div>
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>