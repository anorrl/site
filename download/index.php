<?php
	session_start();

	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user == null) {
		die(header("Location: /login"));
	}

	$randomclientsplashes = [
		"Now with 100% viruses!",
		"Ohhh you gotta install my clients...",
		"Colle t my 8 cliens...",
		"Smells like malware in here...",
		"Will be detected by all antiviruses (I'm coming for you.)",
		"Download RIGHT NOW!",
		"Now with 100% the fun or something",
		"ANORRL FREE DOWNLOAD 100% 2026 UPDATED HAPPYMOD!",
		"Better than anything else",
		"Defender crying in the corner guranteed!",
		"0.00$, Awesome deal!"
	];

	$randomclientsplash = $randomclientsplashes[array_rand($randomclientsplashes)];
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Download - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/download.css">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
	</head>
	<body>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<h2><?= $randomclientsplash ?></h2>
					<div id="DownloadContainer">
						<p id="Splasher">So much malware!!!!!!!!!!</p>
						<span id="Note">(Unfortunately, it is windows only. But wine works fine on linux! Mac builds may come soon...)</span>
						<hr>
						<h3>Clients</h3>
						<div id="DownloadContainer" style="background: #161616;">
							<table style="width: 100%">
								<tr>
									<td>
										<div>
											<a href="2016/ANORRLPlayerLauncher.exe">
												<img src="/images/download/2016client.png">
												<span>2016</span>
											</a>
										</div>
									</td>
									
									<!--<td>
										<div>
											<a href="javascript:alert('2018 idk bro')">
												<img src="/images/placeholder.png">
												<span>2018 (later)</span>
											</a>
										</div>
									</td>-->
									
									<td>
										<div title="Icon created by Wiz!">
											<a href="2013/ANORRL2013PlayerLauncher.exe">
												<img src="/images/download/2013client.png">
												<span>2013</span>
											</a>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<hr>
						<h3>Studio</h3>
						<div id="DownloadContainer" style="background: #161616;">
							<table style="width: 100%">
								<tr>
									<td>
										<div>
											<a href="2016/ANORRLStudioLauncher.exe">
												<img src="/images/download/2016studio.png">
												<span>2016</span>
											</a>
										</div>
									</td>

									<!--<td>
										<div>
											<a href="javascript:alert('2018 Studio idk bro')">
												<img src="/images/placeholder.png">
												<span>2018 (later)</span>
											</a>
										</div>
									</td>-->

									<td>
										<div title="Icon created by Cu-bp!">
											<a href="2013/ANORRL2013StudioLauncher.exe">
												<img src="/images/download/2013studio.png">
												<span>2013</span>
											</a>
										</div>
									</td>
								</tr>
							</table>
						</div>
						
					</div>
					
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
