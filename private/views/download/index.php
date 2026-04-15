<?php
	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$randomsplash = new FileSplasher("client")->getRandomSplash();

	$page = new Page("Download", "download/index");
	$page->addStylesheet("/css/new/download.css");

	$page->loadHeader();
?>
<h2><?= $randomsplash ?></h2>
<div id="DownloadContainer">
	<p id="Splasher">So much malware!!!!!!!!!!</p>
	<span id="Note">(Unfortunately, it is windows only. But wine works fine on linux! Mac builds may come soon...)</span>
	<hr>
	<h3>Windows</h3>
	<div id="DownloadContainer" style="background: #161616;">
		<table style="width: 100%">
			<tr>
				<td>
					<div>
						<a href="/public/download/ANORRLPlayerLauncher.exe">
							<img src="/public/images/download/2016client.png">
							<span>Client</span>
						</a>
					</div>
				</td>
				<td>
					<div>
						<a href="/public/download/ANORRLStudioLauncher.exe">
							<img src="/public/images/download/2016studio.png">
							<span>Studio</span>
						</a>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php $page->loadFooter() ?>