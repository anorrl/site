<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/utilities/userutils.php';
	$user = UserUtils::RetrieveUser();

	if($user != null) {
		die(header("Location: /my/home"));
	}

	if(session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}
	
	$istoomany = count(UserUtils::GetAllUsers()) > 80;

	if(isset($_POST['ANORRL$Signup$Username']) &&
	   isset($_POST['ANORRL$Signup$Password']) &&
	   isset($_POST['ANORRL$Signup$ConfirmPassword']) &&
	   isset($_POST['ANORRL$Signup$AccessKey']) &&
	   isset($_POST['ANORRL$Signup$Submit'])) {
		$username = trim($_POST['ANORRL$Signup$Username']);
		$password = trim($_POST['ANORRL$Signup$Password']);
		$confirm_password = trim($_POST['ANORRL$Signup$ConfirmPassword']);
		$accesskey = trim($_POST['ANORRL$Signup$AccessKey']);

		$result = UserUtils::RegisterUser($username, $password, $confirm_password, $accesskey);

		if($result == "success") {
			die(header("Location: /my/home"));
		} else {
			$_SESSION['signup_errors'] = $result;
			die(header("Location: /register"));
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/forms.css">
		<script src="/js/core/jquery.js"></script>
		<?php if(!$istoomany): ?>
		<script src="/js/forms.js"></script>
		<script>
			$(function(){
				$("#ANORRL_Signup_Username").on("input change", function() {
					ANORRL.Register.CheckUsername(this, $(this).val());
				})

				$("#ANORRL_Signup_Password").on("input change", function() {
					ANORRL.Register.CheckMainPassword(this, $(this).val());
				})

				$("#ANORRL_Signup_ConfirmPassword").on("input change", function() {
					ANORRL.Register.CheckSecondPassword(this, $(this).val());
				})

				$("#ANORRL_Signup_AccessKey").on("input change", function() {
					ANORRL.Register.CheckAccessKey(this, $(this).val());
				})

				$("form").submit(function (e) {
					// Basically, IE literally doesn't want to check if anything has been changed to an input unless directly by keys
					// This just runs all the checks before submission.
					ANORRL.Login.CheckUsername(document.getElementById("ANORRL_Signup_Username"), $("#ANORRL_Signup_Username").val());
					ANORRL.Login.CheckMainPassword(document.getElementById("ANORRL_Signup_Password"), $("#ANORRL_Signup_Password").val());
					ANORRL.Login.CheckSecondPassword(document.getElementById("ANORRL_Signup_ConfirmPassword"), $("#ANORRL_Signup_ConfirmPassword").val());
					ANORRL.Login.CheckAccessKey(document.getElementById("ANORRL_Signup_AccessKey"), $("#ANORRL_Signup_AccessKey").val());
					
					if(!($(".Invalid").length == 0 && $(".Valid").length == 4)) {
						e.preventDefault();
						alert("Holy shit you have so much wrong");
					}
				});

			});
		</script>
		<?php else: ?>
		<script>
			window.alert("There's too many users on the site! Don't even try! >:P");
			window.location.href = "/login";
		</script>
		<?php endif ?>
		<style>
			.FormImage {
				width: 265px;
				height: 410px;
				border: 2px solid black;
			}
		
			#BodyContainer > h2 {
				margin: 0px;
				width: calc(100% - 48px);
				margin-bottom: 20px;
				text-align: center;
				background: none repeat-x;
				background-size: 49px auto;
				border: 4px solid black;
				height: 21px;
				background-blend-mode: difference;
				background-image: linear-gradient(#ffb300,#ffb300),url(/images/header/navbar.jpg);
				overflow: hidden;
			}
		</style>
	</head>
	<body>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<?php if(!$istoomany): ?>
					<h2>&nbsp;</h2>
					<img class="FormImage" src="/images/register/left.png" style="float: left;" >
					<img class="FormImage" src="/images/register/right.png" style="float: right;" >
					<div id="FormPanel" style="width: 240px;">
						<form method="POST">
							<div>
								<h2>Registration</h2>
								<span>You should have been direct messaged by our discord bot for the access key!</span>
							</div>
							<div>
								<h4>Username</h4>
								<span class="Validator" id="v_username">
									<?php 
										if(isset($_SESSION['signup_errors'])) {
											echo $_SESSION['signup_errors']['username'];
										}
									?>
								</span>
								<input type="text" id="ANORRL_Signup_Username" name="ANORRL$Signup$Username" placeholder="a-z A-Z 0-9 and 3-20 characters!" maxlength="20" minlength="3" required>
							</div>
							<div>
								<h4>Password</h4>
								<span class="Validator" id="v_password">
									<?php 
										if(isset($_SESSION['signup_errors'])) {
											echo $_SESSION['signup_errors']['password'];
										}
									?>
								</span>
								<span class="Validator" id="v_confirmpassword"></span>
								<input type="password" id="ANORRL_Signup_Password"        name="ANORRL$Signup$Password"        placeholder="Should be a really solid one!" required>
								<input type="password" id="ANORRL_Signup_ConfirmPassword" name="ANORRL$Signup$ConfirmPassword" placeholder="Needs to match the first one!" required>
							</div>
							<div>
								<h4>Access Key</h4>
								<span class="Validator" id="v_access">
									<?php 
										if(isset($_SESSION['signup_errors'])) {
											echo $_SESSION['signup_errors']['accesskey'];
										}
									?>
								</span>
								<input type="password" id="ANORRL_Signup_AccessKey" name="ANORRL$Signup$AccessKey" placeholder="Check dms from ANORRLBot!" maxlength="36" required>
							</div>
							<div style="margin-top: 10px;">
								<input type="submit" id="ANORRL_Signup_Submit" name="ANORRL$Signup$Submit" value="Register">
							</div>
						</form>
					</div>
					<h2>&nbsp;</h2>
					<?php endif ?>
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
<?php 
	unset($_SESSION['login_errors']);
	session_destroy();
?>