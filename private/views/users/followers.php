<?php
	use anorrl\User;
	use anorrl\utilities\UserUtils;
	use anorrl\UserSettings;
	use anorrl\Page;

	function IsRewrite() {
		if(!empty($_SERVER['IIS_WasUrlRewritten']))
			return true;
		else if(array_key_exists('HTTP_MOD_REWRITE',$_SERVER))
			return true;
		else if( array_key_exists('REDIRECT_URL', $_SERVER))
			return true;
		else
			return false;
	}

	if(!IsRewrite()) {
		die(header("Location: /my/home"));
	}

	// No id parameter? GET OUT!
	if(!isset($id)) {
		die(header("Location: /my/home"));
	}

	$get_user = User::FromID(intval($id));

	if($get_user == null) {
		die(header("Location: /my/home"));
	}

	$user = UserUtils::RetrieveUser($get_user);

	if($user == null) {
		die(header("Location: /login"));
	}

	$header_data = $get_user;

	$followers = $get_user->GetFollowers();

	$page = new Page("{$get_user->name}'s Followers");
	$page->addStylesheet("/css/new/my/friends.css?v=1");

	$page->loadHeader();
?>
					<h2><?= $get_user->name ?>'s Followers</h2>
					<div id="FriendsContainer">
						<?php if(count($followers) != 0): ?>
						<table>
						<?php 
							$count = 0;
							foreach($followers as $friendo) {
								if($count == 0) {
									echo "<tr>";
								}

								$controlPanel = "";

								$fid = $friendo->id;
								
								$profile = $friendo->setprofilepicture ? "profile" : "headshot";

								if(UserSettings::Get($user)->headshots_enabled) {
									$profile = "headshot";
								}

								$status = $friendo->IsOnline() ? "Online" : "Offline";
								
								$fname = $friendo->name;
								echo <<<EOT
								<td>
									<div class="Friend">
										<a href="/users/$fid/profile" title="$fname" target="_blank">
											<img src="/thumbs/$profile?id=$fid&sxy=100">
											<span><img src="/images/OnlineStatusIndicator_Is$status.png"> $fname</span>
										</a>
									</div>
								</td>
								EOT;

								$count++;

								if($count == count($followers) && $count%6 < 6) {
									for($i = 0; $i < 6-($count%6); $i++) {
										echo "<td style=\"width:142px;\"></td>";
									}
								}

								if($count%6 == 0) {
									echo "</tr>";
								}
							}
						?>
						</table>
						<?php endif ?>
					</div>
<?php
	$page->loadFooter();
?>