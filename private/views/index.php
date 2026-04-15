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
<div id="IntroductoryArea">
	<h2>&nbsp;</h2>
	<div id="FirstRow">
		<div id="LogoPitch">
			<a href="/public/images/header/logo.png" target="_blank"><img src="/public/images/header/logo.png" title="welcome to anorrl!"></a>
		</div>
		<div id="TeapotsMayhem">
			<iframe width="480" height="280" src="<?= $video_splash ?>" title="I CANT LET THESE BITCHES" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
		</div>
	</div>
	
	<div id="SecondRow">
		<div id="GracingIt">
			<a href="/public/images/frontpage/grace_clean.png" target="_blank"><img src="/public/images/frontpage/grace.png" title="what a bitch!"></a>
			<div id="Label">
				<span>Grace</span>
				<div id="Notice">
					.owner&nbsp;&nbsp;.developer&nbsp;&nbsp;.bitch
				</div>
			</div>
		</div>
		<div id="Details">
			<h2>So what the heck is ANORRL?</h2>
			<code>
				ANORRL is an acronym stands for <b>AN</b>other <b>O</b>ld <b>R</b>oblox <b>R</b>evival <b>L</b>ol.
				<br><br>
				This is a 2016E friends only one-person revival created by <a href="/users/1/profile">grace</a> that prioritizes creativity over popularity.
				<br><br>
				We also support expressionism (we allow anyone to upload their own hats and stuff as long as it follows the rules :P)
				<br><br>
				<span style="font-size: 10px; color: #CCC; display: block; width: 100%; text-align: center">(P.S: the 2016E client is custom built and uses the leaked source code :P)</span>
			</code>
		</div>
	</div>
	<br style="clear:both">

	<div id="NewUsersContainer">
		<h3>Random Users!</h3>
		<table id="NewUsersBox">
			<?php 
				$users = UserUtils::GetRandomUsers(6);
				$users_count = count($users);
			?>
			<tr>
				<?php foreach($users as $user): ?>
				<td>
					<div class="User" title="<?= $user->name ?>">
						<a href="/users/<?= $user->id ?>/profile">
							<img src="<?= $user->getThumbsUrl(100) ?>">
							<span><?= $user->name ?></span>
						</a>
					</div>
				</td>
				<?php endforeach;
					if($users_count < 6) {
						$count = 6 - $users_count;
						for($i = 0; $i < $count; $i++) {
							echo "<td></td>";
						}
					}
				?>
			</tr>
		</table>
	</div>
	<h2>&nbsp;</h2>
</div>
<div style="margin: 10px auto;width: 60%;"><img src="/public/images/epicbazookaquote.png" style="width: 100%;border: 2px solid black;"></div>
<?php $page->loadFooter() ?>