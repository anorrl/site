<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/asset.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/user.php";

	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);
	
	$rcc_settings = $settings['renderer'];
	$access = $settings['asset']['ACCESSKEY'];

	function getServerDetailsFromJobID(string $jobID): array|null {
		include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";

		$stmt_getsessiondetails = $con->prepare("SELECT * FROM `active_servers` WHERE `server_jobid` = ?");
		$stmt_getsessiondetails->bind_param("s", $jobID);
		$stmt_getsessiondetails->execute();

		$result_getsessiondetails = $stmt_getsessiondetails->get_result();

		if($result_getsessiondetails->num_rows != 0) {
			return $result_getsessiondetails->fetch_assoc();
		}

		return null;
	}

	if(isset($_GET['access']) && isset($_GET['jobID']) && isset($_GET['userID'])) {
		if($_GET['access'] == $access) {
			$server_details = getServerDetailsFromJobID($_GET['jobID']);
			$user = User::FromID(intval($_GET['userID']));

			if($server_details != null) {
				include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
				$stmt_createnewserver = $con->prepare("DELETE FROM `active_players` WHERE `session_serverid` = ? AND `session_playerid` = ?;");
				$stmt_createnewserver->bind_param("si", $server_details['server_id'], $user->id);
				$stmt_createnewserver->execute();
			}
			
		}
		
	}
?>