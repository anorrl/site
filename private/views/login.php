<?php
	use anorrl\Page;
	use anorrl\utilities\UserUtils;

	if(SESSION) {
		die(header("Location: /my/home"));
	}

	if(isset($_POST['ANORRL$Login$Username']) &&
	   isset($_POST['ANORRL$Login$Password']) &&
	   isset($_POST['ANORRL$Login$Submit'])) {
		
		$username = trim($_POST['ANORRL$Login$Username']);
		$password = trim($_POST['ANORRL$Login$Password']);

		$result = UserUtils::LoginUser($username, $password);

		if($result["login"] != "Incorrect details provided!") {
			die(header("Location: /my/home"));
		} else {
			$_SESSION['login_errors'] = $result;
			die(header("Location: /login"));
		}
	}

	$page = new Page("Login");

	$page->addStylesheet("/css/new/forms.css");
	$page->addScript("/js/forms.js");

	$page->loadHeader();
?>

<script>
	$(function(){
		$("#ANORRL_Login_Username").on("input change", function() {
			ANORRL.Login.CheckUsername(this, $(this).val());
		});
		$("#ANORRL_Login_Password").on("input change", function() {
			ANORRL.Login.CheckPassword(this, $(this).val());
		});

		$("form").submit(function (e) {
			// Basically, IE literally doesn't want to check if anything has been changed to an input unless directly by keys
			// This just runs all the checks before submission.
			ANORRL.Login.CheckUsername(document.getElementById("ANORRL_Login_Username"), $("#ANORRL_Login_Username").val());
			ANORRL.Login.CheckPassword(document.getElementById("ANORRL_Login_Password"), $("#ANORRL_Login_Password").val());
			if(!($(".Invalid").length == 0 && $(".Valid").length == 2)) {
				e.preventDefault();
				alert("Holy shit you have so much wrong");
			}
		});

	});
</script>
<style>
	.FormImage {
		width: 265px;
		height: 309px;
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
<h2>&nbsp;</h2>

<img class="FormImage" src="/public/images/login/left.png" style="float: left;" >
<img class="FormImage" src="/public/images/login/right.png" style="float: right;" >

<div id="FormPanel" style="width: 240px;">
	<form method="POST">
		<div>
			<h2>Welcome back!</h2>
			<span>If you do not have an account then,<br><a href="/register">register here!</a></span>
			<span class="Validator">
				<?php 
					if(isset($_SESSION['login_errors'])) {
						echo $_SESSION['login_errors']['login'];
					}
				?>
			</span>
		</div>
		<div>
			<h4>Username</h4>
			<span class="Validator" id="v_username">
				<?php 
					if(isset($_SESSION['login_errors'])) {
						if(isset($_SESSION['login_errors']['username'])) {
							echo $_SESSION['login_errors']['username'];
						}
					}
				?>
			</span>
			<input type="text" id="ANORRL_Login_Username" name="ANORRL$Login$Username" minlength="3" maxlength="20" required>
		</div>
		<div>
			<h4>Password</h4>
			<span class="Validator" id="v_password">
				<?php 
					if(isset($_SESSION['login_errors'])) {
						if(isset($_SESSION['login_errors']['password'])) {
							echo $_SESSION['login_errors']['password'];
						}
					}
				?>
			</span>
			<input type="password" id="ANORRL_Login_Password" name="ANORRL$Login$Password" minlength="7" required>
		</div>
		<div>
			<p></p>
			<input type="submit" id="ANORRL_Login_Submit" name="ANORRL$Login$Submit" value="Login">
		</div>
	</form>						
</div>

<h2>&nbsp;</h2>
<?php 
	$page->loadFooter();
	unset($_SESSION['login_errors']);
	session_destroy();
?>