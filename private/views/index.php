<?php
	use anorrl\Page;
	use anorrl\Session;

	$page = new Page("Welcome to ANORRL!");

	if(isset($_POST['ANORRL$Login$Username']) &&
	   isset($_POST['ANORRL$Login$Password']) &&
	   isset($_POST['ANORRL$Login$Submit'])) {
		
		$username = trim($_POST['ANORRL$Login$Username']);
		$password = trim($_POST['ANORRL$Login$Password']);

		$result = Session::login($username, $password);

		if($result["success"]) {
			redirect("/my/home");
		} else {
			$_SESSION['login_errors'] = $result["errors"];
			redirect("/");
		}
	} else if(isset($_POST['ANORRL$Signup$Username']) &&
	   isset($_POST['ANORRL$Signup$Password']) &&
	   isset($_POST['ANORRL$Signup$ConfirmPassword']) &&
	   isset($_POST['ANORRL$Signup$AccessKey']) &&
	   isset($_POST['ANORRL$Signup$Submit'])) {
		$username = trim($_POST['ANORRL$Signup$Username']);
		$password = trim($_POST['ANORRL$Signup$Password']);
		$confirm_password = trim($_POST['ANORRL$Signup$ConfirmPassword']);
		$accesskey = trim($_POST['ANORRL$Signup$AccessKey']);

		$result = Session::register($username, $password, $confirm_password, $accesskey);

		if($result["success"]) {
			redirect("/my/home");
		} else {
			$_SESSION['signup_errors'] = $result["errors"];
			redirect("/register");
		}
	}

	$page->loadHeader2();
?>
<style>
	#newfrontpage {
		text-align: center;
	}

	#newfrontpage table {
		width: 800px;
		margin: 0 auto;
	}

	#newfrontpage table td {
		vertical-align: top;
	}

	.form-asterisk {
		color: red;
		display: inline-block !important;
	}

	#newfrontpage table .fieldset * {
		display: block;
		text-align: left;
	}

	#newfrontpage table form {
		text-align: center;
	}

	.fieldset input {
		border: 2px solid rgb(141, 29, 216);
		background: #482b5a;
		color:beige;
		margin: 5px;
		padding: 2px 5px;
	}

	.fieldset input:invalid {
		*border-color: red;
	}

	.panel {
		padding: 5px;
		
	}

	#auth[data-selected="register"] a[href="javascript:openRegisterPanel()"],
	#auth[data-selected="login"] a[href="javascript:openLoginPanel()"] {
		font-size: 16px;
		color: #fff;
		text-decoration: underline;
		font-family: 'arial-rounded bold'
	}

	.helperfield {
		font-style:italic;
		font-size: 10px;
		color: #bbb;
	}

	h3 a, h3 a:hover {
		color: #cbc;
		font-size: 13px;
	}

	#auth {
		font-family: arial-rounded;
	}

	.spacer {
		margin: 0px 5px;
		display: inline-block;
	}

</style>
<script>
	function openLoginPanel() {
		if(!$("#login").is(":visible")) {
			$("#login").show();
			$("#register").hide();
			$("#auth").attr("data-selected", "login")
		}
	}

	function openRegisterPanel() {
		if(!$("#register").is(":visible")) {
			$("#register").show();
			$("#login").hide();
			$("#auth").attr("data-selected", "register")
		}
	}

	$(function() {
		$("#login").show();
		$("#register").hide();
		$("#auth").attr("data-selected", "login")
	})

	$(function() {
		$("audio").each(function() {
			if($(this).attr("volume")) {
				$(this)[0].volume = Number($(this).attr("volume"));
			}
		})
	})

	$(function titleScroller(text) {
		document.title = text;
		setTimeout(function () {
			titleScroller(text.substr(1) + text.substr(0, 1));
		}, 500);
	}("Welcome to ANORRL! "));
</script>
<audio src="/public/sonic2013menu.mp3" autoplay volume="0.2" loop></audio>
<div id="newfrontpage">
	<img src="/public/images/header/logo.png" height="200">
	<br>
	<img src="/public/images/slogan.gif" width="440">
	<table>
		<tr>
			<td>
				<div class="box" style="position: relative; padding: 15px;">
					<img src="/public/images/splash_16.png">
					<h3 style="margin-bottom: 0px;">there's creativity to be had here!</h3>
					<h4 style="margin-bottom: 0px;">what will you make?</h4>
					<img src="/public/images/anorrl-fellas1.png" style="width: 245px; position: absolute;left: -56px;bottom: -62px;">
				</div>
			</td>
			<td width="25%">
				<div class="box" id="auth">
					<?php if(!SESSION): ?>
					<h3><a href="javascript:openRegisterPanel()">.register</a> | <a href="javascript:openLoginPanel()">.login</a></h3>
					<div id="login" class="panel">
						<h3 style="margin-top: 0px;">welcome back!</h3>
						<form method="POST">
							<div class="fieldset">
								<label>.username <span class="form-asterisk">*</span></label>
								<input type="text" placeholder="your character's name!" name="ANORRL$Login$Username">
							</div>
							<div class="fieldset">
								<label>.password <span class="form-asterisk">*</span></label>
								<input type="password" placeholder="don't tell me you forgot it..." name="ANORRL$Login$Password">
							</div>
							<input class="button" type="submit" value="login" name="ANORRL$Login$Submit">
						</form>
					</div>
					<div id="register" class="panel">
						<form method="POST">
							<h3 style="margin-top: 0px;">are you new around here?</h3>
							<div class="fieldset">
								<label>.username <span class="form-asterisk">*</span></label>
								<input style="margin-bottom: 0px;" type="text" placeholder="thats your name right?" minlength="3" maxlength="20" required>
								<label class="helperfield"> 3-20 alphanumeric characters, no spaces </label>
							</div>
							<div class="fieldset">
								<label>.password <span class="form-asterisk">*</span></label>
								<input style="margin-bottom: 0px;" type="password" placeholder="thats your password right?" minlength="6" maxlength="20" required>
								<label class="helperfield"> 6-20 characters, min 4 letters & 2 numbers </label>
							</div>
							<div class="fieldset">
								<label>.confirm_password <span class="form-asterisk">*</span></label>
								<input type="password" placeholder="just in case... do it again..."  minlength="6" maxlength="20" required>
							</div>
							<div class="fieldset">
								<label>.invite_key <span class="form-asterisk">*</span></label>
								<input type="password" placeholder="sigh... you know the deal..." required>
							</div>
							<input class="button" type="submit" value="register">
						</form>
					</div>
					<?php else: 
						$user = SESSION->user; ?>
					<h3 style="font-family: 'arial-rounded bold'; margin-bottom: 5px;">your character</h3>
					<span><?= $user->name ?></span>
					<img src="/thumbs/player?id=<?= $user->id ?>&sxy=250" width="250px">
					<div style="margin-top:-20px; margin-bottom: 15px; font-weight: bold;">
						<a href="/users/<?= $user->id ?>/profile">profile</a><span class="spacer">&nbsp;</span>
						<a href="/my/profile">settings</a><span class="spacer">&nbsp;</span>
						<a href="/my/stuff">stuff</a>
						<br>
						<a href="/my/friends">friends</a><span class="spacer">&nbsp;</span>
						<a href="/users/<?= $user->id ?>/following">following</a><span class="spacer">&nbsp;</span>
						<a href="/users/<?= $user->id ?>/followers">followers</a>
						<br style="margin: 10px 0px;">
						<a href="/api/logout?redirect=/" class="button">logout</a>
						<br style="margin: 10px 0px;">
					</div>
					<?php endif ?>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php $page->loadFooter2() ?>