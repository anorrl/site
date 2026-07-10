<?php

	use anorrl\Page;
	use anorrl\utilities\FileSplasher;

	$randomsplash = new FileSplasher("titles/catalog")->getRandomSplash();

	$page = new Page("Catalog");
	$page->clearAll();

	$page->addScript("/js/core/jquery.js");
	$page->addScript("/js/old/catalog.js?t=1776186351");

	$page->loadHeader2();
?>

<?php $page->loadFooter2(); ?>