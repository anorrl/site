<?php
//note from skyler: /images/noassets.png is temporary until a new image is made

	use anorrl\Page;

	$page = new Page("500");
	$page->addStylesheet("/css/new/error.css");
	$page->loadHeader();
?>

<div id="ErrorContainer">
	<img src="/images/icons/img-alert-transparent.png" alt="Error">
	<h1>Uh oh!</h1>
	<b><?php echo "A fucky wucky occurred! (Do NOT spam refresh). Tell grace to FIX IT!"; ?></b>
	<div class="buttons">
		<button id="BackSubmit" onclick="window.history.back();">Back</button>
		<form action="/my/home" method="get">
			<input id="HomeSubmit" type="submit" value="Home">
		</form>
	</div>
</div>
<?php $page->loadFooter() ?>