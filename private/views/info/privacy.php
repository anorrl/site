<?php
	use anorrl\Page;

	$page = new Page("ANORRL Privacy Policy");
	$page->setIgnoreANORRL(false);

	$page->loadHeader();

	$domain = CONFIG->baseurl;
?>
<style>
	h2 {
		margin: 10px 0px;
	}

	p {
		font-family: 'Fira Mono',monospace;
		line-height: 16px
	}
</style>
<h2 class="page-title">.privacy_policy</h2>
<h3 class="page-slogan">whats that again? PRIVACY?? (last updated: 22nd August 2026)</h3>
<div class="box" style="padding: 15px;">
	<h2>.about_anorrl</h2>
	<hr>
	<p>
		So privacy, we find ourselves in this strange time of where everything is trying to track you and get as much info about you...
		We at ANORRL care about privacy unlike literally any other platform, here's our list of policies on this exact thing!
		Just so you can be at minds peace...
	</p>
</div>		
<?php
	$page->loadFooter();
?>