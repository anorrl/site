
<?php
require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";
header('Content-type: application/json');
$userId = intval($_GET['id']);

$user = User::FromID($userId);

if($user != null) {
	$friends = $user->GetFriends();
	$result = [];
	foreach($friends as $friend) {
		array_push($result, [
			"Id" => $friend->id,
			"Username" => $friend->id
		]);
	}

	die(json_encode($result));
}

echo "{}";

?>
