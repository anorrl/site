<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die(header("Location: /login"));
	}

    //took this from games.php but idrc atp -skylerclock
	$randomvandalsplashes = [
		"Vandals",
		"Vandalizers!",
		"i wonder if i can make friends now...",
		"RAAAAAH VANDALS!",
		"the very important vandals!",
		"i need sum friends...",
		"W people",
		"VandGDs????"
	];

	$randomvandalsplash = $randomvandalsplashes[array_rand($randomvandalsplashes)];
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Vandals - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/forms.css">
		<link rel="stylesheet" href="/css/new/people.css">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/people.js?t=1771933381"></script>
	</head>
	<body>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<h2 style="margin: 0; margin-top: 10px;"><?= $randomvandalsplash ?></h2>
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
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
