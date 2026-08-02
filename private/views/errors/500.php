<?php
	use anorrl\Page;

	$page = new Page("500");
	$page->clearAll();
	$page->addStylesheet("/css/error.css");
	$page->loadHeader2();
?>
<div class="box" id="error-container">
	<div id="main">
		<h1>500</h1>
		<a href="/public/images/would-you-error.jpg"><img src="/public/images/would-you-error.jpg" alt="Error" width="220"></a>
		<h1>uh oh!</h1>
		<h3>a fucky wucky occurred! (do NOT spam refresh). <i>tell kuro to FIX IT!</i></h3>
		<hr>
		<br>
		<b>try doing something else next time...</b>
	</div>
	<div class="buttons">
		<button class="button" onclick="window.history.back();">Back</button>
		<form action="/my/home" method="get">
			<input  class="button" id="HomeSubmit" type="submit" value="go home!">
		</form>
	</div>
</div>
<?php $page->loadFooter2() ?>