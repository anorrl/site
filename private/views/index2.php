<?php
	use anorrl\Page;

	$page = new Page("Welcome to ANORRL!");

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

	.panel {
		padding: 5px;
		
	}

	.panel input[type="submit"] {
		border: 2px solid rgb(141, 29, 216);
		font-size: 14px;
		padding: 5px 15px;
		font-weight: bold;
		background: linear-gradient(180deg,rgb(156, 55, 223) 0%, rgb(81, 34, 112) 100%);
		color: white;
		cursor: pointer;
	}

	.panel input[type="submit"]:hover {
		filter: brightness(1.15);
	}

	#auth[data-selected="register"] a[href="javascript:openRegisterPanel()"],
	#auth[data-selected="login"] a[href="javascript:openLoginPanel()"] {
		font-size: 16px;
		color: #fff;
		text-decoration: underline;
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
2
	$(function() {
		$("audio").each(function() {
			if($(this).attr("volume")) {
				$(this)[0].volume = Number($(this).attr("volume"));
			}
		})
	})
</script>
<audio src="/public/sonic2013menu.mp3" autoplay volume="0.2" loop></audio>
<div id="newfrontpage">
	<img src="/public/images/header/logo.png" height="200">
	<br>
	<img src="/public/images/slogan.gif" width="440">
	<table>
		<tr>
			<td>
				<div class="box" style="position: relative">
					<img src="/public/images/splash_16.png" height="300">
					<img src="/public/images/anorrl-fellas1.png" style="width: 245px; position: absolute;left: -56px;bottom: -62px;">
				</div>
			</td>
			<td width="25%">
				<div class="box" id="auth">
					<h3><a href="javascript:openRegisterPanel()">Register</a> | <a href="javascript:openLoginPanel()">Login</a></h3>
					<div id="login" class="panel">
						<h3 style="margin-top: 0px;">welcome back!</h3>
						<form method="POST">
							<div class="fieldset">
								<input type="text" placeholder="Username">
							</div>
							<div class="fieldset">
								<input type="password" placeholder="Password">
							</div>
							<input type="submit" value="Login">
						</form>
					</div>
					<div id="register" class="panel">
						<form method="POST">
							<h3 style="margin-top: 0px;">are you new around here?</h3>
							<div class="fieldset">
								<label>Username</label>
								<input style="margin-bottom: 0px;" type="text" placeholder="thats your name right?" minlength="3" maxlength="20" required>
								<label class="helperfield"> 3-20 alphanumeric characters, no spaces </label>
							</div>
							<div class="fieldset">
								<label>Password</label>
								<input style="margin-bottom: 0px;" type="password" placeholder="thats your password right?" minlength="6" maxlength="20" required>
								<label class="helperfield"> 6-20 characters, min 4 letters & 2 numbers </label>
							</div>
							<div class="fieldset">
								<label>Confirm Password</label>
								<input type="password" placeholder="just in case... do it again..."  minlength="6" maxlength="20" required>
							</div>
							<div class="fieldset">
								<label>Invite Key</label>
								<input type="password" placeholder="sigh... you know the deal..." required>
							</div>
							<input type="submit" value="Register">
						</form>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php $page->loadFooter2() ?>