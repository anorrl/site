<?php
	use anorrl\Page;

	$page = new Page("ANORRL Privacy Policy");
	$page->setIgnoreANORRL(false);
	$page->loadHeader();

	$domain = CONFIG->baseurl;
?>
<style>
	.box h2 {
		margin: 10px 0px;
	}

	.box[privacy] {
		padding: 15px;
	}

	#body [privacy] {
		font-family: 'Fira Mono',monospace;
		line-height: 16px
	}

	[privacy] p {
		margin: 12px 8px;
	}

	[privacy] ul {
		font-size: 11px;
	}

	[privacy] ul li {
		margin: 5px 0px;
	}
</style>
<h2 class="page-title">.privacy_policy</h2>
<h3 class="page-slogan">whats that again? PRIVACY?? (last updated: 27nd August 2026)</h3>
<div class="box" privacy>
	<h2>.about_anorrl</h2>
	<hr>
	<p>
		So privacy, we find ourselves in this strange time of where everything is trying to track you and get as much info about you...
		We at ANORRL care about privacy unlike literally any other platform, here's our list of policies on this exact thing!
		This is going to explain how we collect info and if we share it with anyone (we dont) just so you can be at minds peace...
	</p>
	<p>
		This privacy policy covers all of <?= $domain ?>, apps and such as well, anywhere that has this privacy policy linked on them!
		Just so yous are aware, we do not allow under 15s to be on this site, as explained <a href="/info/terms">here</a> but this
		applies to anyone using this site.
	</p>
	<p>ANORRL works like this....</p>
	<ul>
		<li>any new coming user needs to submit an application to be able to register to the site</li>
		<li>our moderators check any applications sent through and either reject or deny said forms, if accepted the user gets a temporary key to use to able to register properly</li>
		<li>said user creates a username and a virtual character of their identity on the ANORRL platform</li>
		<li>users can interact with others via use of the in game chat or on site comment sections.</li>
		<li>each user is given virtual real estate on which they can build various items such as buildings or vehicles, anything really if built and/or coded; these places are called games...</li>
		<li>users can search for, find and play games made by others on the platform. they can play these games with other users around the world and are identitied by their username.</li>
		<li>on site, there a virtual currency based off <i>traffic cones</i> that users on site can earn but NEVER via real world money. this currency can be earned by selling items or playing games/doing checklists on site, it can be spent on accessories/items to be used on their avatar or for other uses.</li>
		<li>users who have earned a large amount of traffic cones can spend it on a lifetime subscription that can be refunded as to gain the ability to create more places</li>
		<li>for more info, check out the <a href="/info/terms">terms of service</a> ;)</li>
	</ul>
</div>
<br>
<div class="box" privacy>
	<h2>.requested_info</h2>
	<hr>
	<p>
		Users must have an acccount to have full access to use the site; this includes sharing stats/models and participating in the economy/events we host whatsoever.
		It's highly discouraged for users to register with a username that may reveal personal information about themselves in the real world,
		and also to have highly guessable passwords/sharing passwords.
		For basic security reasons, we store encrypted passwords or any sensitive info via the use of <a href="https://en.wikipedia.org/wiki/Argon2" target="__blank">ARGON2ID</a>.
	</p>
	<p>
		Usernames are used for participating in site features and we don't use them to identity individuals outside of their activity on our platform and for purposes relating to offering our platform and services.
		Any info about our users are not passed to any third parties, we do not work with any third party whatsoever as we are independent.
	</p>
	<p>
		However, we do request the birthdate and emails of our users ONLY during the application process.
		We do not use this information for anything else other than the initial signup to ensure we are allowing of age users to access the site
		and also to ensure correct contact is made when asked for support such as resetting passwords or other account features.
	</p>
	<p>
		Our mobile applications do not ask for permission to use, nor do they have access to, percise geolocation information (e.g, GPS or cellular network location) from your networked device.
	</p>
</div>
<br>
<div class="box" privacy>
	<h2>.collected_info</h2>
	<hr>
	<p>
		We use cookies to store user preferences, we do not track our users and their activities.
		We do not engage with accessing sensitive information such as IP addresses, althought our client may attempt to send tracking information we do not intend to use and might remove.
		However, we may store in game chat messages to be used to corroborate any reports made with in an active play session.
		We store this data up to a week MAX, database backups may include this information however this is to ensure if anything were to happen to our servers we are able to restore any corrupt data present.
	</p>
	<h3>.log_files</h3>
	<p>
		As a natural behaviour of all webservers, our webserver which runs on <a href="https://nginx.org/">NGINX</a>; logs information such as access times and errors created on site.
		Rest assured, sensitive information such as IP addresses are proxied via <a href="https://www.cloudflare.com/">Cloudflare</a> so we do not know where requests are coming from.
		It does however, store the date time and user agent the site was accessed with and we clear our log files every month.
	</p>
</div>
<br>
<div class="box" privacy>
	<h2>.changes_and_updates</h2>
	<hr>
	<p>
		We may update this privacy policy to reflect changes to our information practices at any time, so please review it frequently.
		If we update this policy, changes will be reflected on this page and we will update the “last updated” date posted above.
		If we make any material changes to this privacy policy, we will attempt to notify via use of site alerts.
	</p>
	<p>feel free to contact us at <a href="mailto:info@<?= $domain ?>">info@<?= $domain ?></a> if you feel anything is wrong with these policies or have general questions about it.</p>
</div>
<?php $page->loadFooter(); ?>