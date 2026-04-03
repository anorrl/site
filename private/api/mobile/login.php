
<?php 
	use anorrl\utilities\UserUtils;
	
	header("Content-Type: application/json"); 
	
	$user = UserUtils::RetrieveUser();
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$result = UserUtils::LoginUser($_POST['username'], $_POST['password']);
		$user = UserUtils::RetrieveUser();
	}
?>

<?php 
ob_clean();
if($result["login"] != "Incorrect details provided!"): ?>
{
	"Status":"OK", 
	"UserInfo": {
		"UserID": <?= $user->id ?>,
		"UserName": "<?= trim($user->name) ?>",
		"RobuxBalance": 69,
		"TicketsBalance": 420,
		"IsAnyBuildersClubMember": false,
		"ThumbnailUrl": "http://arl.lambda.cam/thumbs/player?id=<?= $user->id ?>"
	}
}
<?php else: ?>
{ "Status":"<?php print_r($result) ?>" }
<?php endif ?>