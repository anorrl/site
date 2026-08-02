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
			if(isset($_GET['redirect']))
				redirect($_GET['redirect']);
			else
				redirect("/my/home");
		} else {
			$_SESSION['login_errors'] = $result["errors"];
			redirect($_SERVER['REQUEST_URI']);
		}
	}

	$random = rand(0, 100000);
	$deceptacon = $random >= 50000 && $random <= 55000;
	$music = $deceptacon ? "deceptacon" : "sonic2013menu";

	$page->loadHeader2();
?>
<style>
	#newfrontpage {
		text-align: center;
	}

	.form-asterisk {
		color: red;
		display: inline-block !important;
	}

	#newfrontpage .fieldset * {
		display: block;
		text-align: left;
	}

	#newfrontpage form {
		text-align: center;
	}

	.fieldset input, .fieldset textarea {
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

	.fieldset {
		width: fit-content;
		margin: 0 auto;
	}

</style>
<script>
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

	$(function() {
		if(navigator.getAutoplayPolicy("mediaelement") !== "allowed")
			$("#autoplay-warning").css("display", "block");
	})

	console.log("chance: <?= $random ?>, <?= $deceptacon ? "true": "false" ?>");

</script>
<?php if(isset($_SESSION['signup_errors'])): ?>
	<div style="color: red; font-weight: bold; font-size: 20px;">there's errors with the registration</div>
	<pre>
		<?= print_r($_SESSION['signup_errors']) ?>
	</pre>
<?php endif ?>

<?php if(isset($_SESSION['login_errors'])): ?>
	<div style="color: red; font-weight: bold; font-size: 20px;">there's errors with the login</div>
	<pre>
		<?= print_r($_SESSION['login_errors']) ?>
	</pre>
<?php endif ?>
<audio src="/public/<?= $music ?>.mp3" autoplay volume="<?= $deceptacon ? "0.1" : "0.2" ?>" loop></audio>
<div style="position: fixed; background: black; padding: 10px;display:none" id="autoplay-warning">
	Hey did you know there's music playing right now?
	<br>
	No? Well seems like you have AUTOPLAY off!
</div>
<div id="newfrontpage">
	<img src="/public/images/header/logo.png" height="200">
	<br>
	<img src="/public/images/slogan.gif" width="440" style="margin-top: -55px">
	<div style="display: flex; width: 915px; margin: 0 auto; gap: 10px;">
		<div class="box" style="position: relative; padding: 15px; flex: 1;">
			<img src="/public/images/splash_16.png">
			<h3 style="margin-bottom: 0px;">there's creativity to be had here!</h3>
			<h4 style="margin-bottom: 0px;">what will you make?</h4>
		</div>
		<div class="box" id="auth" style="flex: 0.5;<?= !ARLAUTH ? "max-width: 207px;" : "" ?>">
			<?php if(!ARLAUTH): ?>
			<h3>.login</h3>
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
				<hr>
				<h3>looking to join in on the fun?</h3>
				<br>
				<a href="" class="button" style="font-family:'arial-rounded bold'; text-decoration: none">.register</a>
			</div>
			<?php else: 
				$user = SESSION->user; ?>
			<h3 style="font-family: 'arial-rounded bold'; margin-bottom: 5px;">.welcome!</h3>
			<span><?= $user->name ?></span>
			<img src="<?= $user->getThumbsUrlAvatar(250) ?>" width="250px">
			<div style="margin-top:-20px; margin-bottom: 15px; font-weight: bold;">
				<a href="/users/<?= $user->id ?>/profile">.profile</a><span class="spacer">&nbsp;</span>
				<a href="/my/profile">.settings</a><span class="spacer">&nbsp;</span>
				<a href="/my/stuff">.stuff</a>
				<br>
				<a href="/my/friends">.friends</a><span class="spacer">&nbsp;</span>
				<a href="/users/<?= $user->id ?>/following">.following</a><span class="spacer">&nbsp;</span>
				<a href="/users/<?= $user->id ?>/followers">.followers</a>
				<br style="margin: 10px 0px;">
				<a href="/api/logout?redirect=/" class="button">logout</a>
				<br style="margin: 10px 0px;">
			</div>
			<?php endif ?>
		</div>
	</div>

</div>
<?php $page->loadFooter2() ?>
<?php
	unset($_SESSION['signup_errors']);
	unset($_SESSION['login_errors']);
?>