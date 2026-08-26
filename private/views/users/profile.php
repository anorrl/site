<?php
	use anorrl\User;
	use anorrl\Page;
	use anorrl\UserSettings;

	use Michelf\MarkdownExtra;

	// No id parameter? GET OUT!
	if(!isset($id)) {
		redirect("/my/home");
	}

	$user = User::FromID(intval($id));

	if(!$user) {
		redirect("/my/home");
	}

	$bgm = null;
	$owner = false;
	$online = $user->isOnline();
	$items = $user->getWearing(null, true);
	$friends = $user->getFriends();
	$friends_count = count($friends);
	$followers_count = $user->getFollowersCount();
	$following_count = $user->getFollowingCount();


	if(ARLAUTH) {
		$settings = SESSION->settings;
		$owner = $user->id == SESSION->user->id;
	} else {
		$settings = UserSettings::Get();
	}

	//str_replace("\n", "<br>", UtilUtils::RecurseRemove(UtilUtils::TurnUrlIntoHyperlink($user->blurb), "\r\n\r\n\r\n", "\n\n"))

	$bio = MarkdownExtra::defaultTransform($user->blurb);

	$page = new Page($owner ? "Your Profile" : "{$user->name}'s Profile", $owner ? "user_profile" : null);
	if($user->has_pfp_set)
		$page->setIcon($user->getThumbsUrlProfile());
	$page->loadHeader2();

	if($settings->profile_music) {
		$bgm = $user->getSettings()->background_music;
		if($bgm && !$bgm->isUsable()) {
			$bgm = null;
		}

		//if($bgm)
			//$page->loadWimpy("/asset/?id={$bgm->id}", $bgm->name, "", $bgm->getURL());
	}

	$owner_look = "it's you.";

	if($owner) {
		if(!isset($_SESSION['ANORRL$Owner$StopLooking']))
			$_SESSION['ANORRL$Owner$StopLooking'] = 0;

		$_SESSION['ANORRL$Owner$StopLooking']++;

		if($_SESSION['ANORRL$Owner$StopLooking'] > 10)
			$owner_look = "still you...";
		
		if($_SESSION['ANORRL$Owner$StopLooking'] > 20)
			$owner_look = "you must really like yourself...";
	}
?>
<script src="/public/wimpy/wimpy.js"></script>
<link rel="stylesheet" href="/public/css/cropper.min.css">
<script src="/public/js/core/cropper.min.js"></script>
<script src="/public/js/core/jquery-cropper.min.js"></script>
<script src="/public/js/core/jquery-modal.js"></script>
<script src="/public/js/3D/ThreeDeeThumbnails.js?v=3"></script>
<script src="/public/js/3D/three.min.js"></script>
<script src="/public/js/3D/MTLLoader.js?v=1"></script>
<script src="/public/js/3D/OBJMTLLoader.js?v=1"></script>
<script src="/public/js/3D/tween.js"></script>
<script src="/public/js/3D/PolygonOrbitControls.js"></script>
<script src="/public/js/thumbnails.js"></script>

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
		inset: -3px;
		left: 3px;
		top: 3px;
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
		*font-size: 14px;
		display:inline-block;
		margin-left: -1px;
		margin-right: -1px;
	}

	#profile-stats #profile-name {
		font-size: 18px;
		font-style: italic;
		font-weight: bold;
	}

	#profile-stats #profile-name img {
		margin-right: 7px;
		image-rendering:pixelated;
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

	#crop-modal {
		margin-top: 27px !important;
		margin: 0px !important;
		transform: translate(-50%, -50%);
		z-index: 10000 !important;
		padding: 10px;
		text-align: center;
		display: none;
	}

	#crop-modal a[rel] {
		text-transform: none;
		text-decoration: none;
	}

	.jquery-modal.blocker {
		z-index: 9999 !important;
	}

	#profile-bio {
		padding: 15px 30px;
		font-size: 13px; 
		font-family: 'Fira Mono';
		max-height: 300px;
		overflow: auto;
		max-width:596px;
		overflow-wrap: break-word;
	}
</style>
<script>
	$(function(){
		$("input[type='file'][hidden]").on("change", function() {
			var type = $(this).data("type");
			if(type != "pfp" && type != "banner") {
				alert("Something went wrong!");
				return;
			}

			var reader = new FileReader();

			reader.onload = function (e) {
				$("#crop-modal").attr("type", type);
				$("#crop-modal").modal({showClose: false});
				$('#cropper-img').attr('src', e.target.result).width(500);
				$('#cropper-img').cropper({
					aspectRatio: type == "banner" ? 970 / 220 : 1/1,
					viewMode: 1
				});
				$('#cropper-img').data("cropper").replace(e.target.result);	
			};

			reader.readAsDataURL(this.files[0]);
		})

		$("#crop-modal button[rel='save']").click(function() {
			var type = $('#cropper-img').parent().attr("type");
			if(type != "pfp" && type != "banner") {
				alert("Something went wrong!");
				return;
			}
			$('#cropper-img').data("cropper").getCroppedCanvas().toBlob((blob) => {
				const formData = new FormData();
				formData.append('croppedImage', blob);
				$.ajax('/users/update/' + type, {
					method: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success(data) {
						if(data['success']) {
							var image = $("#cropper-img").cropper("getCroppedCanvas").toDataURL(type == "banner" ?  'image/png' :  'image/jpeg');
							
							if(type == "banner") {
								$("#profile-container").css("background-image", "url("+image+")");
							} else {
								$("#profile-picture img").attr("src", image);
								$(".header-pfp-image").attr("src", image);
							}
						}
						else {
							alert("Something went wrong: " + data['reason']);
						}
					},
					error() {
						alert('Upload error');
					},
				});
				
				
			}, type == "banner" ?  'image/png' :  'image/jpeg');
		})

		$("button[data-method]").click(function() {
			var method = $(this).data("method");

			if(method.startsWith("upload-")) {
				var type = method.replaceAll("upload-", "");
				var file = $("input[type='file'][hidden]");

				file.attr("data-type", type);
				file.trigger("click");
			}
			else if(method.startsWith("remove-")) {
				var type = method.replaceAll("remove-", "");
				$.post("/users/remove/"+type, function() {window.location.reload();})
			}
		})
	});
</script>
<div id="crop-modal" class="box">
	<h2>crop yo shit!</h2>
	<img id="cropper-img">
	<div style="margin-top: 5px;">
		<!-- evil -->
		<a href="#" rel="modal:close" style="color:white">
			<button class="button">cancel</button>
			<button class="button" rel="save">save</button>
		</a>
	</div>
</div>
<input type="file" hidden accept="image/*"/>
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
			<div id="profile-name"><img <?php if(!$owner): ?>title="this means they're <?= $online ? "online" : "offline" ?>!"<?php endif ?> src="/public/images/OnlineStatusIndicator_Is<?= $online ? "Online" : "Offline" ?>.png" width="12"><?= $user->name ?></div>
			<?php if($user->getLatestStatus()): ?>
			<div style="padding-top: 5px; font-style: italic">
				<span class="quote">"</span><?= $user->getLatestStatus()->content ?><span class="quote">"</span>
			</div>
			<?php endif ?>
			<div style="padding-top: 5px;">
				<a href="/users/<?= $user->id ?>/friends"><b><?= $friends_count ?></b> Friend<?= $friends_count == 1 ? "" : "s"  ?></a> |
				<a href="/users/<?= $user->id ?>/followers"><b><?= $followers_count ?></b> Follower<?= $followers_count == 1 ? "" : "s"  ?></a> |
				<a href="/users/<?= $user->id ?>/following"><b><?= $following_count ?></b> Following</a>
			</div>
			
			<div style="margin-top: 5px;">
				<?php if($owner): ?>
					<button class="button"><?= $owner_look ?></button>
				<?php else: ?>
					<button class="button">follow</button>
					<button class="button">friend</button>
					<button class="button">block</button>
				<?php endif ?>
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

<div style="display: flex; gap: 10px;">
	<?php if($user->blurb != ""): ?>
	<div style="flex: 1;">
		<h4 class="page-title">.about</h4>
		<div class="box" id="profile-bio" <?php if(!$bgm): ?>style="max-width:910px"<?php endif ?>><?= $bio ?></div>
	</div>
	<?php endif ?>
	<?php if($bgm): ?>
	<div style="margin: 0 auto;">
		<h4 class="page-title">.music</h4>
		<div style="margin-bottom: 5px;">
			<div style="width: 300px; white-space: nowrap;">
				<div class="box" style="margin-left:-2px; text-align: center;">
					<a href="<?= $bgm->getURL() ?>">
						<div style="border-bottom: 1px solid var(--lighter-border-color); padding: 5px 0px;">
							<img src="<?= $bgm->getThumbsUrl(); ?>" style="width: 206px;">
						</div>
						<div >
							<h4 style="margin: 5px 0px; margin-bottom: 3px;"><?= $bgm->name ?></h4>
						</div>
					</a>
				</div>
				<div 
					data-wimpyplayer
					data-skin="/public/wimpy/skins/Slick_modified.tsv"
					data-loop="2"
					data-disablecontrols="next,playlist,rewind,getid3"
					style="text-align: center; margin-top: 5px;"
					data-media="/asset/?id=<?=$bgm->id?>.mp3"
					data-volume="0.4"
				></div>
			</div>
		</div>
	</div>
	<?php endif ?>
</div>

<style>
	.thumbnail-holder button {
		background: none;
		background-image: url("/public/images/thumbnails/viewer/3D.png");
		background-position: -10px -10px;
		width: 48px;
		height: 48px;
		border: none;
		position: absolute;
		right: 0px;
		cursor: pointer;
	}

	.thumbnail-holder button:hover {
		background-image: url("/public/images/thumbnails/viewer/3DHover.png");
	}

	.thumbnail-holder button[data-3d="true"] {
		background-image: url("/public/images/thumbnails/viewer/2D.png");
	}

	.thumbnail-holder button[data-3d="true"]:hover {
		background-image: url("/public/images/thumbnails/viewer/2DHover.png");
	}

	.thumbnail-holder {
		margin: 0 auto;
		position: relative
	}

	.thumbnail-span {
		width: 100%;
		height: 100%;
		display: none;
		margin: 0 auto;
	}

	.thumbnail-spinner {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 300px;
	}
</style>
<style>
	.accoutrement-item {
		text-align: center;
		height: 100px;
		width: 100px;
		margin: 0 auto;
	}

	.accoutrement-item:hover {
		border: 2px solid var(--border-color);
		margin: -2px auto;
		background: linear-gradient(0deg,rgb(156, 55, 223) 0%, rgb(81, 34, 112) 100%);
	}

	.accoutrement-item img {
		object-fit: cover;
	}

	#character-items {
		display: grid; grid-template-columns: repeat(4, 1fr);
		gap: 5px;
		height: 300px;
		overflow: auto;
		border-left: 1px solid var(--lighter-border-color);
	}

	#no-character-items {
		display: flex;
		align-items: center;
		justify-content: center;
		border-left: 1px solid var(--lighter-border-color);
		font-size: 16px;
		font-family: 'Fira Mono'
	}
</style>
<div class="multi-titles">
	<div>
		<h4 class="page-title">.character</h4>
	</div>
	<div>
		<h4 class="page-title">.items</h4>
	</div>
</div>
<div class="box" id="character-container" style="z-index: 2">
	<div style="text-align: center;">
		<div class="thumbnail-holder" width="300" height="300">
			<button id="thumbnail-switcher" data-3d></button>
			<span class="thumbnail-span" data-3d-url="/thumbnail/get?user=<?= $user->id ?>" style="width:300px;height:300px"></span>
			<img src="<?= $user->getThumbsUrlAvatar() ?>" width="300">
		</div>
	</div>
	<?php if(count($items) > 0): ?>
	<div style="" id="character-items">
		<?php foreach($items as $item): ?>
			<div class="accoutrement-item">
				<a href="<?= $item->getURL() ?>" title="<?= $item->name ?>">
					<img src="<?= $item->getThumbsUrl() ?>" width="100" height="100">
				</a>
			</div>
		<?php endforeach ?>
	</div>
	<?php else :?>
	<div id="no-character-items">
		<b><?= $user->name ?> has no items!</b>
	</div>
	<?php endif ?>
</div>

<div id="report-banner" class="box">
	got something to report about this user? <a href="/report?userid=<?= $user->id ?>">click here!</a>
</div>

<?php
	$page->loadFooter2();
?>
