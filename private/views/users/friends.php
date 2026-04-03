<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/comment.php';

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
	if(!isset($_GET['id'])) {
		die(header("Location: /my/home"));
	}

	$get_user = User::FromID(intval($_GET['id']));

	if($get_user == null) {
		die(header("Location: /my/home"));
	}

	if($get_user->id == 1) {
		die(require $_SERVER['DOCUMENT_ROOT']."/core/venturing.html");
	}

	if(isset($_GET['page'])) {
		if(intval($_GET['page']) == 1) {
			die(include($_SERVER['DOCUMENT_ROOT']."/users/api/friends.php"));
		} else {
			header("Content-Type: application/json");
			die("{}");
		}
	}
	
	$user = UserUtils::RetrieveUser($get_user);

	if($user == null) {
		die(header("Location: /login"));
	}

	$header_data = $get_user;

	$friends = $get_user->GetFriends();
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= $get_user->name ?>'s Friends - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/my/friends.css?v=1">
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
	</head>
	<body>
		<div id="Container">
			<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
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

								if($count == $result_stmt->num_rows && $count%6 < 6) {
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
					
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
