<?php
	use anorrl\User;
	use anorrl\utilities\UserUtils;
	use anorrl\UserSettings;
	use anorrl\Page;
	use anorrl\utilities\UtilUtils;

	
	if(!UtilUtils::HasBeenRewritten()) {
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

	$user = SESSION->user;

	$header_data = $get_user;

	$followers = $get_user->getFollowers();

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

			$status = $friendo->isOnline() ? "Online" : "Offline";
			
			echo <<<EOT
			<td>
				<div class="Friend">
					<a href="/users/{$friendo->id}/profile" title="{$friendo->name}" target="_blank">
						<img src="{$friendo->getThumbsUrl(100)}">
						<span><img src="/public/images/OnlineStatusIndicator_Is$status.png"> {$friendo->name}</span>
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
	<?php else: ?>
		<center>
			<p style="font-size: 16px">Seems like <?= $get_user->id != $user->id ? "{$get_user->name} has" : "you have" ?>  no followers! :[</p>
		</center>
	<?php endif ?>
</div>
<?php
	$page->loadFooter();
?>