<?php
	use anorrl\utilities\FileSplasher;
	use anorrl\utilities\UserUtils;
	use anorrl\UserSettings;
	use anorrl\Page;

	$page = new Page("Welcome to ANORRL!");
	$page->addStylesheet("/css/new/frontpage.css?v=6");
	$page->loadHeader();

	$settings = SESSION ? SESSION->settings : UserSettings::Get();

	$video_splash = new FileSplasher("videos", false, "JaneVideos")->getRandomSplash()
?>
<?php ?>

<?php $page->loadFooter() ?>