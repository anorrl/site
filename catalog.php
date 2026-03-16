<?php
	session_start();

	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die(header("Location: /login"));
	}
	// very unfortunate skid situation, sorry skylerclock
	$randomcatalogsplashes = [
		"Teh Catalog",
		"Buy Somethin' Will Ya!", // earthbound reference
		"smoke shop",
		"Everything is free here somehow",
		"BUY MY MERCH"
	];

	$randomcatalogsplash = $randomcatalogsplashes[array_rand($randomcatalogsplashes)];
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Catalog - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/forms.css">
		<link rel="stylesheet" href="/css/new/stuff.css?v=1">
		<link rel="stylesheet" href="/css/new/catalog.css">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/catalog.js?t=1771413807"></script>
		<style>
			h5 {
				font-family: punk;
				text-align: center;
				background: black;
				padding: 8px 0px;
				margin: 0px;
			}
		</style>
	</head>
	<body>
		<div class="Asset" template>
			<a id="NameAndThumbs">
				<div id="FavouritesArea"><img src="/images/favourite_star.gif"> <span>0</span></div>
				<img src="">
				<div id="Pricing">
					<span id="Cones" ><img src="/images/icons/traffic_cone.png" > <span id="Costing"></span></span>
					<span id="Lights"><img src="/images/icons/traffic_light.png"> <span id="Costing"></span></span>
				</div>
				<span>AssetName</span>
			</a>
			<a id="Creator"><span>AssetCreator</span></a>
		</div>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<h2 style="margin: 0px"><?= $randomcatalogsplash ?></h2>
					<div id="CatalogContainer">
						<div id="OptionsPanel">
							<div id="CategoriesChooser">
								<h4>Categories</h4>
								<h5 style="margin-top: 5px">Accoutrement</h5>
								<ul>
									<li data_category="8" ><a>Hats</a></li>
									<li data_category="18"><a>Faces</a></li>
									<li data_category="11"><a>Shirts</a></li>
									<li data_category="2" ><a>T-Shirts</a></li>
									<li data_category="12"><a>Pants</a></li>
									<li data_category="19"><a>Gears</a></li>
									<li data_category="17"><a>Heads</a></li>
								</ul>
								<h5>Development</h5>
								<ul>
									<li data_category="3" ><a>Audio</a></li>
									<li data_category="4" ><a>Meshes</a></li>
									<li data_category="24"><a>Animations</a></li>
									<li data_category="13"><a>Decals</a></li>
									<li data_category="10"><a>Models</a></li>
								</ul>
							</div>
							<div id="FiltersChooser" style="margin-top: 10px;">
								<h4>Filters</h4>
								<ul>
									<li data_filter="1"><a>Recently Uploaded</a></li>
									<li data_filter="2"><a>Recently Updated</a></li>
									<li data_filter="5"><a>Most Sold</a></li>
									<li data_filter="6"><a>Most Favourited</a></li>
									<li data_filter="3"><a>Oldest Uploaded</a></li>
									<li data_filter="4"><a>Oldest Updated</a></li>
								</ul>
							</div>
						</div>
						<div id="AssetsContainer">
							<div method="GET" id="FormPanel" style="margin: 5px auto;">
								<input id="SearchBox" name="query" type="text" placeholder="Look for awesome games!!!" style="width: 460px;">
								<input id="Submit" type="submit" value="Search" onclick="ANORRL.Catalog.Submit(); return false;">
							</div>
							<div id="StatusText">
								<b id="Loading" style="display: none">Loading assets...</b>
								<b id="NoAssets" style="display: none"><img src="/images/noassets.png" style="width: 110px;display: block;margin: 0 auto;margin-bottom: -92px;margin-top: 23px;">No <span id="AssetType"></span> like that here!</b>
							</div>
						
							<table id="Assets">
								
							</table>
							
							<div id="Paginator" style="display: block;">
								<a id="PrevPager" href="javascript:ANORRL.Catalog.PrevPage()" style="display: none;">&lt;&lt; Back</a> <input type="text" id="NumberPutter" maxlength="3"> of <span id="Counter">1</span> <a id="NextPager" href="javascript:ANORRL.Catalog.NextPage()" style="display: none;">Next &gt;&gt;</a>
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
