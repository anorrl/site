<?php
	use anorrl\PageOld;
	use anorrl\User;
	
	// No id parameter? GET OUT!
	if(!isset($id)) {
		redirect("/my/home");
	}

	$get_user = User::FromID($id);

	if($get_user == null) {
		redirect("/my/home");
	}

	if(isset($_GET['page'])) {
		if(intval($_GET['page']) == 1) {
			die(include get_path_sitefile("private/gameapis/users/friends.php"));
		} else {
			set_content_type(ARLTYPEJSON);
			die("{}");
		}
	}

	if(!SESSION)
		redirect("/goodbye");
	
	$user = SESSION->user;

	$header_data = $get_user;

	$friends = $get_user->getFriends();

	$page = new PageOld("{$get_user->name}'s Friends");
	$page->addStylesheet("/css/new/my/friends.css?v=1");

	$page->loadHeader();
?>
<h2><?= $get_user->name ?>'s Friends</h2>
<div id="FriendsContainer">
	<?php if(count($friends) != 0): ?>
	<table>
	<?php 
		$count = 0;
		foreach($friends as $friendo) {
			if($count == 0) {
				echo "<tr>";
			}

			$status = $friendo->isOnline() ? "Online" : "Offline";
			
			echo <<<EOT
			<td>
				<div class="Friend">
					<a href="{$friendo->getURL()}" title="{$friendo->name}" target="_blank">
						<img src="{$friendo->getThumbsUrl(100)}">
						<span><img src="/public/images/OnlineStatusIndicator_Is$status.png"> {$friendo->name}</span>
					</a>
				</div>
			</td>
			EOT;

			$count++;

			if($count == count($friends) && $count%6 < 6) {
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
			<p style="font-size: 16px">Seems like <?= $get_user->id != $user->id ? "{$get_user->name} has" : "you have" ?> no friends! :[</p>
		</center>
	<?php endif ?>
</div>
<?php $page->loadFooter(); ?>
