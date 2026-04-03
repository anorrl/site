<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die(header("Location: /login"));
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Your Stuff - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/stuff.css?v=1">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/stuff.js?t=1771413807"></script>
	</head>
	<body>
		<div class="Asset" template>
			<a id="NameAndThumbs">
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
					<div id="StuffContainer">
						<h1><marquee behavior="alternate" scrollamount="15">Your Stuff</marquee></h1>
						<div id="StuffNavigation">
							<div id="CreateArea">
								<a href="/create/">Create</a>
								<a href="/catalog">Shop</a>
							</div>
							
							<ul>
								<li data_category="8" ><a>Hats</a></li>
								<li data_category="18"><a>Faces</a></li>
								<li data_category="11"><a>Shirts</a></li>
								<li data_category="2" ><a>T-Shirts</a></li>
								<li data_category="12"><a>Pants</a></li>
								<hr>
								<li data_category="3" ><a>Audio</a></li>
								<li data_category="13"><a>Decals</a></li>
								<li data_category="10"><a>Models</a></li>
								<li data_category="9" ><a>Places</a></li>
								<hr>
								<li data_category="19"><a>Gears</a></li>
								<li data_category="21"><a>Badges</a></li>
								<li data_category="34"><a>Gamepasses</a></li>
								<li data_category="32"><a>Packages</a></li>
							</ul>
						</div><div id="AssetsContainer">
							<div id="StatusText">
								<b id="Loading" style="display: none">Loading assets...</b>
								<b id="NoAssets" style="display: none"><img src="/images/noassets.png" style="width: 110px;display: block;margin: 0 auto;margin-bottom: -92px;margin-top: 23px;">You have no <span id="AssetType"></span>!</b>
							</div>
						
							<table hidden></table>

							<div id="Paginator" style="display: none">
								<a href="javascript:ANORRL.Stuff.DeadvancePager()" id="PrevPager">&lt;&lt;Previous</a> <input maxlength="4" id="NumberPutter"> of <span id="Pages">1</span> <a href="javascript:ANORRL.Stuff.AdvancePager()" id="NextPager">Next&gt;&gt;</a>
							</div>
						</div>
					</div>
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
