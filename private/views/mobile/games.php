<!DOCTYPE html>
<html> 
	<head>
		<link rel="stylesheet" href="/public/css/new/main.css">
		<link rel="stylesheet" href="/public/css/new/forms.css">
		<link rel="stylesheet" href="/public/css/new/stuff.css?v=1">
		<title>Games - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<script src="/public/js/core/jquery.js"></script>
		<script src="/public/js/main.js?t=1776011774"></script>
		<script src="/public/js/games.js?t=1776011774"></script>
		<script>
			ANORRL.Games.MobileEnabled = true;
		</script>
		<style>
			body {
				color: white;
			}

			.Game[template] {
				display: none;
			}

			.Game {
				border: 2px solid black;
				text-align: center;
				padding: 10px;
				background: #2a2a2a;
				margin: 5px;
				color: white;
			}
		</style>
	</head>
	<body>
		<div class="Game" template>
			<div id="ImageContainer">
				<!--<div id="FavouritesArea"><img src="/images/favourite_star.gif" style="width:16px; margin-bottom: -2px;"> <span>0</span></div>-->
				<img src="" style="width: 100%">
			</div>
			<div id="Info">
				<a href="" id="GameName">Game Name</a>
				<hr style="border: none; margin: 2px">
				
				<span>By <a href="" id="GameCreator">creator</a></span>
				<div style="font-weight: bold;letter-spacing: 1px;font-size:10px;">
					<span id="ActivePlayerCountLabel" style="color: #a93cac;"><b id="ActivePlayerCount" style="letter-spacing: 0;">0</b> Player<span id="Plural">s</span> online...</span><br>
					<span id="VisitCountLabel" style="color:#8a8a8a;font-style:italic;"><b id="VisitCount" style="letter-spacing: 0;">0</b> Visit<span id="Plural">s</span></span>
				</div>
				
			</div>
		</div>
		<div id="Container" style="width:unset;margin:10px">
			<div id="Games">
				<div method="GET" id="FormPanel" style="margin: 5px auto;width: 100%;padding: 5px 0px;">
					<input id="SearchBox" name="query" type="text" placeholder="Look for awesome games!!!" style="width: 70%;">
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
	</body>
</html>