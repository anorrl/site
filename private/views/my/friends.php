<?php
	use anorrl\Page;
	use anorrl\User;
	use anorrl\Database;

	if(!SESSION) {
		die(header("Location: /login"));
	}

	$user = SESSION->user;

	$fetch = Database::singleton()->run(
		"SELECT * FROM `friends` WHERE (`sender` = :id OR `reciever` = :id) ORDER BY `status` ASC",
		[ ":id" => $user->id ]
	)->fetchAll(\PDO::FETCH_OBJ);

	$number_of_friends = count($fetch);

	$page = new Page("Your Friends");
	$page->addStylesheet("/css/new/my/friends.css?v=1");
	$page->addScript("/js/friends.js?t=1771413807");

	$page->loadHeader();
?>

<h2>Your Friends</h2>
<div id="FriendsContainer">
	<?php if($number_of_friends != 0): ?>
	<table>
	<?php 
		$count = 0;
		foreach($fetch as $row) {
			if($count == 0) {
				echo "<tr>";
			}

			$controlPanel = "";

			$friendo = User::FromID($row->reciever == $user->id ? $row->sender : $row->reciever);
			
			if($friendo == null) {
				// There's a person that's non existent somehow!
				continue;
			}

			$fid = $friendo->id;

			if($row->status == 1) {
				$controlPanel = <<<EOT
				<hr>
				<div id="ControlPanel" style="font-size: 11px">
					<a href="javascript:ANORRL.Friends.Remove($fid)">Remove</a>
				</div>
				EOT;
			} else {
				if($row->reciever == $user->id) {
					$controlPanel = <<<EOT
					<hr>
					<div id="ControlPanel" style="font-weight: bold;font-size: 13px">
						<a href="javascript:ANORRL.Friends.Accept($fid)">Accept</a>
						<span>|</span>
						<a href="javascript:ANORRL.Friends.Reject($fid)">Reject</a>
					</div>
					EOT;
				} else {
					$controlPanel = <<<EOT
					<hr>
					<div id="ControlPanel" style="font-weight: bold;font-size: 13px">
						<a href="javascript:ANORRL.Friends.Cancel($fid)">Cancel</a>
					</div>
					EOT;
				}
				
			}
			
			$profile = $friendo->setprofilepicture ? "profile" : "headshot";

			if(SESSION->settings->headshots_enabled) {
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
					$controlPanel
				</div>
					
			</td>
			EOT;

			$count++;

			if($count == $number_of_friends && $count%6 < 6) {
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
			<p style="font-size: 16px">Seems like you have no friends! :[</p>
		</center>
	<?php endif ?>
</div>
<?php $page->loadFooter(); ?>