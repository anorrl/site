<?php

	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/renderer.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/rcclib.php";

	$settings = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../settings.env", true);
	
	$rcc_settings = $settings['renderer'];

	$access = $settings['asset']['ACCESSKEY'];
	
	$arbiter_ip = $settings['arbiter']['LOC'];
	$arbiter_token = $settings['arbiter']['token'];

	if(isset($_GET['access']) && isset($_GET['jobID'])) {
		if($_GET['access'] == $access) {
			include $_SERVER["DOCUMENT_ROOT"]."/core/connection.php";
			
			$stmt_getactiveservers = $con->prepare("SELECT * FROM `active_servers` WHERE `server_jobid` = ?");
			$stmt_getactiveservers->bind_param("s", $_GET['jobID']);
			$stmt_getactiveservers->execute();

			$result_getactiveservers = $stmt_getactiveservers->get_result();

			if($result_getactiveservers->num_rows != 0) {
				$row = $result_getactiveservers->fetch_assoc();

				if(!isset($_GET['dontcall'])) {
					$data = json_encode([
						"pid" => intval($row['server_pid'])
					]);

					$ch = curl_init("http://$arbiter_ip/api/v1/gameserver/kill");
					curl_setopt($ch, CURLOPT_HTTPHEADER, [
						"Authorization: Bearer $arbiter_token",
						"Content-Type: application/json",
						"User-Agent: ANORRL/1.0"
					]);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					$response = curl_exec($ch);
					$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);

					if($code != 200) {
						die(http_response_code(503));
					}
				}

				$stmt_createnewserver = $con->prepare("DELETE FROM `active_servers` WHERE `server_jobid` = ?;");
				$stmt_createnewserver->bind_param("s", $_GET['jobID']);
				$stmt_createnewserver->execute();
			}


		}
		
	}
?>