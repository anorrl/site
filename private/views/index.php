<?php
	use anorrl\utilities\UserUtils;
	use anorrl\UserSettings;
	use anorrl\Page;

	$page = new Page("Welcome to ANORRL!");
	$page->addStylesheet("/css/new/frontpage.css?v=3");
	$page->loadHeader();
?>
<div id="IntroductoryArea">
	<h2>&nbsp;</h2>
	<div id="FirstRow">
		<div id="LogoPitch">
			<a href="/images/header/logo.png" target="_blank"><img src="/images/header/logo.png" title="welcome to anorrl!"></a>
		</div>
		<div id="TeapotsMayhem">
			<a href="/images/frontpage/gang_clean.png" target="_blank"><img src="/images/frontpage/gang.png" title="created by grace and power!"></a>
		</div>
	</div>
	
	<div id="SecondRow">
		<div id="GracingIt">
			<a href="/images/frontpage/grace_clean.png" target="_blank"><img src="/images/frontpage/grace.png" title="what a bitch!"></a>
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
				<?php  
					foreach($users as $user) {
						$user_id = $user->id;
						$user_name = $user->name;
						$profile = $user->setprofilepicture ? "profile" : "headshot";
						if(UserSettings::Get(UserUtils::RetrieveUser())->headshots_enabled && UserUtils::RetrieveUser() != null) {
							$profile = "headshot";
						}
						echo <<<EOT
							<td>
								<div class="User" title="$user_name">
									<a href="/users/$user_id/profile">
										<img src="/thumbs/$profile?id=$user_id&sxy=100">
										<span>$user_name</span>
									</a>
								</div>
							</td>
						EOT;
					}

					if($users_count < 6) {
						$count = 6 - $users_count;
						for($i = 0; $i < $count; $i++) {
							echo <<<EOT
								<td></td>
							EOT;
						}
					}
				?>
			</tr>
		</table>
	</div>
	<h2>&nbsp;</h2>
</div>
<div style="margin: 10px auto;width: 60%;"><img src="/images/epicbazookaquote.png" style="width: 100%;border: 2px solid black;"></div>
<?php $page->loadFooter() ?>