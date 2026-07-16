<?php
//note from skyler: /images/noassets.png is temporary until a new image is made

	use anorrl\Page;

	$page = new Page("404");
	$page->clearAll();
	$page->addStylesheet("/css/error.css");
	$page->loadHeader2();
?>
<div class="box" id="error-container">
	<div id="main">
		<h1>404</h1>
		<a href="/public/images/would-you-error.jpg"><img src="/public/images/would-you-error.jpg" alt="Error" width="220"></a>
		<h1>awww shucks!</h1>
		<h3><?php echo "you tried to access \"" . $_SERVER['REQUEST_URI'] . "\" aaaand that failed..."; ?></h3>
		<hr>
		<br>
		<b>try doing something else next time...</b>
	</div>
	<div class="buttons">
		<button class="button" onclick="window.history.back();">Back</button>
		<form action="/my/home" method="get">
			<input  class="button" id="HomeSubmit" type="submit" value="Home">
		</form>
	</div>
</div>
<?php $page->loadFooter2() ?>