<?php
	use anorrl\Page;

	$page = new Page("Page");

	$page->loadHeader();
?>
<h1>Hello world</h1>
<p>Hiii</p>				
<?php
	$page->loadFooter();
?>