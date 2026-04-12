<?php
	use anorrl\User;
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
	
	$following = $get_user->getFollowing();

	$page = new Page("{$get_user->name}'s Following");
	$page->addStylesheet("/css/new/my/friends.css?v=1");

	$page->loadHeader();
?>
<h2><?= $get_user->name ?>'s Following</h2>
<div id="FriendsContainer">
	<?php if(count($following) != 0): ?>
	<table>
	<?php 
		$count = 0;
		foreach($following as $friendo) {
			if($count == 0) {
				echo "<tr>";
			}

			$status = $friendo->isOnline() ? "Online" : "Offline";
			
			echo <<<EOT
			<td>
				<div class="Friend">
					<a href="/users/{$friendo->id}/profile" title="{$friendo->name}" target="_blank">
						<img src="{$friendo->getThumbsUrl(100)}">
						<span><img src="/images/OnlineStatusIndicator_Is$status.png"> {$friendo->name}</span>
					</a>
				</div>
			</td>
			EOT;

			$count++;

			if($count == count($following) && $count%6 < 6) {
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
			<p style="font-size: 16px">Seems like <?= $get_user->id != $user->id ? "{$get_user->name} isn't" : "you aren't" ?> following anyone! :[</p>
		</center>
	<?php endif ?>
</div>
<?php $page->loadFooter() ?>