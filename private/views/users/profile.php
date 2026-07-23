<?php
	use anorrl\User;
	use anorrl\Page;
	use anorrl\utilities\UtilUtils;

	if(!UtilUtils::HasBeenRewritten()) {
		redirect("/my/home");
	}
	
	// No id parameter? GET OUT!
	
	if(!isset($id)) {
		redirect("/my/home");
	}

	$user = User::FromID(intval($id));

	if($user == null) {
		redirect("/my/home");
	}

	$settings = SESSION->settings;
	$bgm = null;

	$owner = $user->id == SESSION->user->id;

	$page = new Page($owner ? "Your Profile" : "{$user->name}'s Profile", $owner ? "user_profile" : null);
	
	$page->loadHeader2();

	if($settings->profile_music) {
		$bgm = $user->getSettings()->background_music;
		if($bgm && !$bgm->isUsable()) {
			$bgm = null;
		}

		if($bgm)
			$page->loadWimpy("/asset/?id={$bgm->id}", $bgm->name, "", $bgm->getURL());
	}
?>
<script src="/public/wimpy/wimpy.js"></script>
<link rel="stylesheet" href="/public/css/cropper.min.css">
<script src="/public/js/core/cropper.min.js"></script>
<script src="/public/js/core/jquery-cropper.min.js"></script>
<script src="/public/js/core/jquery-modal.js"></script>
<style>
	#profile-container {
		position:relative;
		width: 970px;
		height: 220px;
		background-color:black;
		background-image: url('<?= $user->getThumbsUrlBanner() ?>');
		border: 2px solid var(--border-color);
		background-size: 100%;
	}

	#profile-picture {
		width: 161px;
		height: 161px;
		position: relative;
	}

	#profile-picture #controls {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		gap: 5px;

		position: absolute;
		background-color: rgba(0,0,0, 0.3);
		transition: opacity 0.25s;
		opacity: 0;
		inset: 0px;
		right: -6px;
		bottom: -6px;
	}

	#profile-picture:hover #controls {
		opacity: 1;
		cursor: pointer;
		pointer-events: none;
	}
	
	#profile-picture:hover #controls * {
		pointer-events: all;
	}

	#profile-picture img {
		border-image: repeating-linear-gradient(
			-55deg,
			#000,
			#000 10px,
			#ffb101 10px,
			#ffb101 20px
		) 10;
		border-style: solid;
		width: 100%;
	}

	#profile-stats {
		user-select: none;
		display: flex;
		justify-content: center;
		flex-direction: column;
		gap: 3px;
		margin-left: 15px;
	}

	#profile-stats div {
		background: rgba(0,0,0, 0.6);
		font-family: "Fira Mono";
		letter-spacing: 2px;
		padding: 5px 10px;
		width:fit-content;
	}

	#profile-stats a {
		color: white;
		transition: 0.5s font-size;
	}

	#profile-stats a:hover {
		font-weight: bold;
		*text-decoration: none;
		font-size: 14px;
	}

	#profile-stats #profile-name {
		font-size: 18px;
		font-style: italic;
		font-weight: bold;
	}

	.quote {
		font-weight: bold;
		font-size: 15px;
		margin-top: -5px;
		display: inline-block;
	}

	#banner-controls {
		position: absolute;display: flex; flex-direction: column;top:15px;right:15px; gap: 5px;
		opacity: 0;
		transition: opacity 0.25s;
	}

	#profile-container:hover #banner-controls {
		opacity: 1;
	}

	#report-banner {
		font-size: 11px; text-align: center; padding: 8px 16px; 
		width: fit-content;
		margin: 0 auto;
		letter-spacing: 2px;
		font-weight: bolder;
		font-style: italic;
		color: #ddd;
		margin-top:5px;
	}

	.page-title {
		margin-top: 5px; font-size: 13px
	}

	#character-container {
		padding: 5px; display: flex;
	}

	#character-container > div {
		flex: 1;
		padding: 10px;
	}
</style>
<script>
	$(function(){
		$("#pfpfile").on("change", function() {
			var reader = new FileReader();

			reader.onload = function (e) {
				$("#pfp-modal").modal({showClose: false});
				
				$('#pfp-crop-img').attr('src', e.target.result).width(500);
				
				$('#pfp-crop-img').cropper({
					aspectRatio: 970 / 220,
					aspectRatio: 1/1,
					viewMode: 1
				});
				$('#pfp-crop-img').data("cropper").replace(e.target.result);
				
			};

			reader.readAsDataURL(this.files[0]);
		})

		$("#bannerfile").on("change", function() {
			var reader = new FileReader();

			reader.onload = function (e) {
				$("#banner-modal").modal({showClose: false});
				
				$('#banner-crop-img').attr('src', e.target.result).width(500);
				
				$('#banner-crop-img').cropper({
					aspectRatio: 970 / 220,
					viewMode: 1
				});
				$('#banner-crop-img').data("cropper").replace(e.target.result);
				
			};

			reader.readAsDataURL(this.files[0]);
		})

		$("#pfp-modal button[rel='save']").click(function() {
			$('#pfp-crop-img').data("cropper").getCroppedCanvas().toBlob((blob) => {
			const formData = new FormData();
			formData.append('croppedImage', blob);
			$.ajax('/users/update/pfp', {
				method: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success() {
					var image = $("#pfp-crop-img").cropper("getCroppedCanvas").toDataURL("image/jpeg");
					$("#profile-picture img").attr("src", image);
					$(".header-pfp-image").attr("src", image);
				},
				error() {
					alert('Upload error');
				},
			});
			}, 'image/jpeg');
		})

		$("#banner-modal button[rel='save']").click(function() {
			$('#banner-crop-img').data("cropper").getCroppedCanvas().toBlob((blob) => {
			const formData = new FormData();
			formData.append('croppedImage', blob);
			$.ajax('/users/update/banner', {
				method: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success() {
					var image = $("#banner-crop-img").cropper("getCroppedCanvas").toDataURL("image/jpeg");
					$("#profile-container").css("background-image", "url("+image+")");
				},
				error() {
					alert('Upload error');
				},
			});
			}, 'image/jpeg');
		})

		$("button[data-method]").click(function() {
			var method = $(this).attr("data-method");

			if(method == "upload-pfp")
				$("#pfpfile").trigger("click");
			else if(method == "remove-pfp")
				$.post("/users/remove/pfp", function() {window.location.reload();})
			else if(method == "upload-banner")
				$("#bannerfile").trigger("click");
			else if(method == "remove-banner")
				$.post("/users/remove/banner", function() {window.location.reload();})
		})
	});
</script>
<style>
	#pfp-modal, #banner-modal {
		margin-top: 27px !important;
		margin: 0px !important;
		transform: translate(-50%, -50%);
		z-index: 10000 !important;
		padding: 10px;
		text-align: center;
	}

	.jquery-modal.blocker {
		z-index: 9999 !important;
	}
</style>
<div id="pfp-modal" class="box" style="display: none;">
	<h2>crop yo shit!</h2>
	<img id="pfp-crop-img">
	<div style="margin-top: 5px;">
		<!-- evil -->
		<a href="#"  rel="modal:close" style="color:white">
			<button class="button">cancel</button>
			<button class="button" rel="save">save</button>
		</a>
	</div>
</div>
<div id="banner-modal" class="box" style="display: none;">
	<h2>crop yo shit!</h2>
	<img id="banner-crop-img">
	<div style="margin-top: 5px;">
		<!-- evil -->
		<a href="#"  rel="modal:close" style="color:white">
			<button class="button">cancel</button>
			<button class="button" rel="save">save</button>
		</a>
	</div>
</div>
<input id="pfpfile" type="file" hidden/>
<input id="bannerfile" type="file" hidden/>
<div id="profile-container">
	<div style="padding: 30px; display: flex;">
		<div id="profile-picture">
			<?php if($owner): ?>
			<div id="controls">
				<button class="button" data-method="upload-pfp">change</button>
				<button class="button" data-method="remove-pfp">delete</button>
			</div>
			<?php endif ?>
			<a title="<?= $owner ? "your" : "{$user->name}'s" ?> profile pic!" href="<?= $user->getThumbsUrl(161)?>" target="__blank"><img src="<?= $user->getThumbsUrl(161)?>"></a>
		</div>

		<div id="profile-stats"> 
			<div id="profile-name"><?= $user->name ?></div>
			<?php if($user->getLatestStatus()): ?>
			<div style="padding-top: 5px; font-style: italic">
				<span class="quote">"</span><?= $user->getLatestStatus()->content ?><span class="quote">"</span>
			</div>
			<?php endif ?>
			<div style="padding-top: 5px;">
				<a href="/users/<?= $user->id ?>/friends"><b><?= $user->getFriendsCount() ?></b> Friends</a> |
				<a href="/users/<?= $user->id ?>/followers"><b><?= $user->getFollowersCount() ?></b> Followers</a> |
				<a href="/users/<?= $user->id ?>/following"><b><?= $user->getFollowingCount() ?></b> Following</a>
			</div>
			
			<div style="*background: none; *padding: 0px; margin-top: 5px;">
				<button class="button">follow</button>
				<button class="button">friend</button>
				<button class="button">block</button>
			</div>
			<?php if($owner): ?>
			<div id="banner-controls">
				<button class="button" data-method="upload-banner">change</button>
				<button class="button" data-method="remove-banner">delete</button>
			</div>
			<?php endif ?>
		</div>	
		
	</div>
</div>

<?php if($user->blurb != ""): ?>
<h4 class="page-title">.about</h4>
<div class="box" style="padding: 15px 30px; font-size: 13px;  font-family: 'Fira Mono';" >
	<?= UtilUtils::TurnUrlIntoHyperlink($user->blurb) ?>
</div>
<?php endif ?>

<h4 class="page-title">.character</h4>
<div class="box" id="character-container">
	<div>
		hi
	</div>
	<div style="border-left: 1px solid var(--lighter-border-color)">
		hi
	</div>
</div>

<div id="report-banner" class="box">
	got something to report about this user? <a href="/report?userid=<?= $user->id ?>">click here!</a>
</div>

<?php
	$page->loadFooter2();
?>
