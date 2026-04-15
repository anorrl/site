<?php
//note from skyler: /images/noassets.png is temporary until a new image is made

	use anorrl\Page;

	$page = new Page("404");
	$page->addStylesheet("/css/new/error.css");
	$page->loadHeader();
?>

<div id="ErrorContainer">
	<img src="/public/images/noassets.png" alt="Error">
	<h1>Ahhh Shucks!</h1>
	<b><?php echo "You tried to access \"" . $_SERVER['REQUEST_URI'] . "\" and that failed."; ?></b>
	<div class="buttons">
		<button id="BackSubmit" onclick="window.history.back();">Back</button>
		<form action="/my/home" method="get">
			<input id="HomeSubmit" type="submit" value="Home">
		</form>
	</div>
</div>
<?php $page->loadFooter() ?>