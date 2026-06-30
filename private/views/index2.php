<?php
	use anorrl\Page;

	$page = new Page("Welcome to ANORRL");

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
</style>
<script>
	function openLoginPanel() {
		if(!$("#login").is(":visible")) {
			$("#login").show();
			$("#register").hide();
		}
	}

	function openRegisterPanel() {
		if(!$("#register").is(":visible")) {
			$("#register").show();
			$("#login").hide();
		}
	}
</script>
<div id="newfrontpage">
	<img src="/public/images/header/logo.png" height="225">
	<table>
		<tr>
			<td>
				<div class="box">
					<h1>Awesome box</h1>
					<p>(add characters!!!)</p>
				</div>
			</td>
			<td width="50%">
				<div class="box">
					<h3><a href="javascript:openRegisterPanel()">Register</a> | <a href="javascript:openLoginPanel()">Login</a></h3>
					<div id="login" class="panel">
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
					<div id="register" class="panel" style="display: none">
						<form method="POST">
							<div class="fieldset">
								<label>Username</label>
								<input type="text" placeholder="thats your name right?">
							</div>
							<div class="fieldset">
								<label>Password</label>
								<input type="password" placeholder="thats your password right?">
							</div>
							<div class="fieldset">
								<label>Confirm Password</label>
								<input type="password" placeholder="just in case... do it again...">
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