<?php
	use anorrl\enums\ANORRLBadge;
	use anorrl\Page;

	$page = new Page("Badges");

	$page->loadHeader();

	function createBadge(ANORRLBadge $badge) {
		$badgenamefile = str_replace(" ", "", $badge->name());
		echo <<<EOT
			<li id="Badge{$badge->ordinal()}" class="Badge">
				<div class="BadgePadding">&nbsp;</div>
				<div class="BadgeContent">
					<div class="BadgeImage">
						<img src="/images/Badges/$badgenamefile.png" alt="{$badge->name()}" width="100" height="100">
					</div>
					<div class="BadgeDescription">
						<h3>{$badge->name()} Badge</h3>
						<p class="notranslate">{$badge->description()}</p>
					</div>
					<div style="clear:both"></div>
				</div>
			</li>
		EOT;
	}
?>
<style>
	#BadgesContainer {
		border: 2px solid black;
		background: #222;
		padding: 10px;
	}

	#BadgesContainer h2, #BadgesContainer h3 {
		margin: 0px;
	}

	#BadgesContainer .BadgeCategory {
		background: #1a1a1a;
		border: 2px solid black;
		margin-bottom: 15px;
	}

	#BadgesContainer .BadgeCategory .Badge {
		background: #1a1a1a;
	}

	#BadgesContainer .BadgeCategory .Badge .BadgePadding {
		display: none;
	}

	#BadgesContainer .BadgeCategory .Badge .BadgeImage {
		float: left;
		width: 75px;
		height: 75px;
	}

	#BadgesContainer .BadgeCategory .Badge .BadgeImage img {

		margin-left: -25px;
		margin-top: -15px;
	}

	#BadgesContainer .BadgeCategory .Badge .BadgeDescription {
		margin-left: 85px;
		min-height: 75px;
	}

	#BadgesContainer .BadgeCategory .Badge .BadgeDescription p {
		background: #111;
		margin: 0px;
		padding: 10px;
		margin-right: 10px;
		border: 1px solid black;
		margin-bottom: 5px;
	}
	
	#BadgesContainer .BadgeCategory ul {
		list-style: none;
	}
</style>
<h1 style="margin: 0px">Badges</h1>
<div id="BadgesContainer" class="text">
	<div class="BadgeCategory">
		<h2>Community Badges</h2>
		<ul>
			<?php
				createBadge(ANORRLBadge::ADMINISTRATOR);
				createBadge(ANORRLBadge::VETERAN);
				/*createBadge(ANORRLBadge::FORUM_MOD);
				createBadge(ANORRLBadge::IMAGE_MOD);*/
			?>
		</ul>
	</div>
	<div class="BadgeCategory" style="display: none">
		<h2>Builders Club Badges</h2>
		<ul>
			<li id="Badge11" class="Badge">
			<div class="BadgePadding">&nbsp;</div>
			<div class="BadgeContent">
				<div class="BadgeImage">
				<img src="" alt="Builders Club" width="75" height="75">
				</div>
				<div class="BadgeDescription">
				<h3>Builders Club Badge</h3>
				<p class="notranslate">Members of the illustrious Builders Club display this badge proudly. The Builders Club is a paid premium service. Members receive several benefits: they get ten places on their account instead of one, they earn a daily income of 15 ROBUX, they can sell their creations to others in the ROBLOX Catalog, they get the ability to browse the web site without external ads, and they receive the exclusive Builders Club construction hat.</p>
				</div>
				<div style="clear:both"></div>
			</div>
			</li>
			<li id="Badge15" class="Badge">
			<div class="BadgePadding">&nbsp;</div>
			<div class="BadgeContent">
			<div class="BadgeImage">
			<img src="" alt="Turbo Builders Club" width="75" height="75">
			</div>
			<div class="BadgeDescription">
			<h3>Turbo Builders Club Badge</h3>
			<p class="notranslate">Members of the exclusive Turbo Builders Club are some of the most dedicated ROBLOXians. The Turbo Builders Club is a paid premium service. Members receive many of the benefits received in the regular Builders Club, in addition to a few more exclusive upgrades: they get twenty-five places on their account instead of ten from regular Builders Club, they earn a daily income of 35 ROBUX, they can sell their creations to others in the ROBLOX Catalog, they get the ability to browse the web site without external ads, they receive the exclusive Turbo Builders Club red site managers hat, and they receive an exclusive gear item.</p>
			</div>
			<div style="clear:both"></div>
			</div>
			</li>
			<li id="Badge16" class="Badge">
			<div class="BadgePadding">&nbsp;</div>
			<div class="BadgeContent">
			<div class="BadgeImage">
			<img src="" alt="Outrageous Builders Club" width="75" height="75">
			</div>
			<div class="BadgeDescription">
			<h3>Outrageous Builders Club Badge</h3>
			<p class="notranslate">Members of Outrageous Builders Club are VIP ROBLOXians. They are the cream of the crop. The Outrageous Builders Club is a paid premium service. Members receive 100 places, 100 groups, 60 ROBUX per day, unlock the Outrageous website theme, get access to the CEO and devs of ROBLOX through Outrageous-cast, and many other benefits.</p>
			</div>
			<div style="clear:both"></div>
			</div>
			</li>
		</ul>
	</div>
	<div class="BadgeCategory">
		<h2>Builder Badges</h2>
		<ul>
			<?php
				createBadge(ANORRLBadge::HOMESTEAD);
				createBadge(ANORRLBadge::BRICKSMITH);
			?>
		</ul>
	</div>
	<div class="BadgeCategory">
		<h2>Friendship Badges</h2>
		<ul>
			<?php
				createBadge(ANORRLBadge::FRIENDSHIP);
				createBadge(ANORRLBadge::INVITER);
			?>
		</ul>
	</div>
	<div class="BadgeCategory">
		<h2>Combat Badges</h2>
		<ul>
			<?php
				createBadge(ANORRLBadge::COMBAT_INITIATION);
				createBadge(ANORRLBadge::WARRIOR);
				createBadge(ANORRLBadge::BLOXXER);
			?>
		</ul>
	</div>
</div>
<?php
	$page->loadFooter();
?>