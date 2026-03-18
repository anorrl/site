<?php 

	$name = $_GET['name'];
	$id = intval($_GET['id']);

	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/assetutils.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/classes/comment.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/core/utilities/userutils.php";

	$asset = Asset::FromID($id);
	$user = UserUtils::RetrieveUser();

	if($asset != null) {

		if($asset->notcatalogueable && $asset->type == AssetType::AUDIO) {
			$urlname = $asset->relatedasset->GetURLTitle();
			$id = $asset->relatedasset->id;
			die(header("Location: /$urlname-item?id=$id"));
		}

		$urlname = $asset->GetURLTitle();
		if($asset->GetURLTitle() != $name) {
			if($asset->type == AssetType::PLACE) {
				die(header("Location: /$urlname-place?id=$id"));
			}
			die(header("Location: /$urlname-item?id=$id"));
		} else {
			if($asset->type == AssetType::PLACE) {
				die(header("Location: /$urlname-place?id=$id"));
			}
		}

		if($asset->type == AssetType::AUDIO) {
			include $_SERVER['DOCUMENT_ROOT']."/core/connection.php";
			$stmt = $con->prepare('SELECT * FROM `assets` WHERE `asset_relatedid` = ? AND `asset_type` = ?;');
			$type = AssetType::AUDIO->ordinal();
			$stmt->bind_param("ii", $id, $type);
			$stmt->execute();
			$stmt_result = $stmt->get_result();
			if($stmt_result->num_rows == 0) {
				$audio_asset_id = $id;
			} else {
				$audio_asset_id = $stmt_result->fetch_assoc()['asset_id'];
			}
		}

		if($user != null) {
			$is_creator = $user->id == $asset->creator->id || $user->IsAdmin();
			$is_favourited = $asset->HasUserFavourited($user);
			$is_bought = $user->Owns($asset);
			
			if(
				isset($_POST['ANORRL$Comment$Post$Contents']) &&
				isset($_POST['ANORRL$Comment$Post$Submit']) &&
				$asset->comments_enabled
			) {
				$result = Comment::Post($asset, $_POST['ANORRL$Comment$Post$Contents']);
				
				if($result['error']) {
					$_SESSION['ANORRL$Comment$Post$Error'] = $result['reason'];
				}

				die(header("Location: /$urlname-item?id=$id"));
			}

			$comments = Comment::GetCommentsOn($asset);
			$comments_count = count($comments);
		}

		$favourites_count = $asset->favourites_count . " times";
		if($asset->favourites_count == 1) {
			$favourites_count = $asset->favourites_count . " time";
		}
		$asset_creator_name = $asset->creator->name;

		$asset_description = $asset->description;
		if(trim($asset_description) == "") {
			$asset_description = "<b>Seems like $asset_creator_name hasn't put anything here...</b>";
		} else {
			$asset_description = str_replace(PHP_EOL, "<br>", $asset_description);
		}
	} else {
		die(header("Location: /my/stuff"));
	}

	$rendering_types = [
		AssetType::PLACE,
		AssetType::SHIRT,
		AssetType::PANTS,
		AssetType::MODEL,
		AssetType::HAT,
		AssetType::MESH,
		AssetType::HEAD,
		AssetType::PACKAGE,
		AssetType::TORSO,
		AssetType::LEFTARM,
		AssetType::RIGHTARM,
		AssetType::LEFTLEG,
		AssetType::RIGHTLEG,
		AssetType::GEAR,
	];

	$get_related_id = $asset->GetAssetIDSafe();


	$header_data = $asset;

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= htmlspecialchars($asset->name, ENT_QUOTES) ?> - ANORRL</title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<link rel="stylesheet" href="/css/new/main.css">
		<link rel="stylesheet" href="/css/new/item/item.css">
		<link rel="stylesheet" href="/css/new/comments.css?v=1">
		<link rel="stylesheet" href="/css/new/my/home.css?v=2">

		<meta name="title" content="<?= htmlspecialchars($asset->name, ENT_QUOTES) ?>">
		<meta name="description" content="<?= htmlspecialchars(substr($asset->description, 0, 128), ENT_QUOTES) ?>"><!-- Max 128 chars -->

		<meta property="og:type" content="website">
		<meta property="og:title" content="<?= htmlspecialchars($asset->name, ENT_QUOTES) ?>">
		<meta property="og:description" content="<?= htmlspecialchars(substr($asset->description, 0, 128), ENT_QUOTES) ?>">
		<meta property="og:url" content="https://arl.lambda.cam/<?= $asset->GetURLTitle()?>-item?id=<?= $asset->id ?>">
		<meta property="og:site_name" content="ANORRL">
		<meta property="og:image" content="https://arl.lambda.cam/thumbs/?id=<?= $asset->id ?>">

		<?php
		if($user == null) {
			die();
			//die(header("Location: /login"));
		}
		?>
		
		<script src="/js/core/jquery.js"></script>
		<script src="/js/main.js?t=1771413807"></script>
		<script src="/js/item.js?t=1771413807"></script>
		<style>
			h2, h3, h4 {
				margin: 0;
			}
		</style>
		<?php if($user != null && $user->IsAdmin()  || $is_creator): ?>
		<script>
			var rendering = false;
			function Render() {
				if(rendering) {
					return;
				}

				rendering = true;
				window.alert("Committing render! (Press ok to continue)");
				$("#RenderButton").html("Rendering...");
				$.post( "/Admin/components/assetstuff", { id: <?= $asset->id ?>, type: "render" }).done(function( data ) {
					window.location.reload();
				});
			}
			
			function Delete() {
				if(window.confirm("Are you sure you want to delete this??")) {
					$.post( "/Admin/components/assetstuff", { id: <?= $asset->id ?>, type: "delete" }).done(function( data ) {
						window.location.reload();
					});
				}
			}
		</script>
		<?php endif ?>
		<script>
			function copyToClipboard(assetID) {
				// https://stackoverflow.com/a/65996386
				var textToCopy = assetID;

				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(textToCopy);
				} else {
					// Use the 'out of viewport hidden text area' trick
					const textArea = document.createElement("textarea");
					textArea.value = textToCopy;
						
					// Move textarea out of the viewport so it's not visible
					textArea.style.position = "absolute";
					textArea.style.left = "-999999px";
						
					document.body.prepend(textArea);
					textArea.select();

					try {
						document.execCommand('copy');
					} catch (error) {
						console.error(error);
					} finally {
						textArea.remove();
					}
				}
				window.alert("Link has been copied!");
			}
		</script>
		<style>
			#AssetName {
				width: 600px;
				display: inline-block;
				overflow: hidden;
				margin-right: 10px;
				margin-bottom: -4px;
				text-overflow: ellipsis;
			}
		</style>
	</head>
	<body>
		<?php if($asset->onsale): ?>
		<div id="PurchasePanel" style="display: none">
			<div id="ModalPopup" data-assetid="<?= $asset->id ?>">
				<h3>Purchase this <?= strtolower($asset->type->label()) ?>??</h3>

				<div id="PurchaseFreeItem">
					<p>
						The item "<?= $asset->name ?>" from <a target="__blank" href="/users/<?= $asset->creator->id ?>/profile"><?= $asset->creator->name ?></a> is available in the <b><i>Public Domain</i></b>. 
					</p>
					<p>
						Would you like to add it to your inventory for <b><i>free</i></b>?
					</p>
					<p>
						<input type="submit" value="Add it now!" onclick="ANORRL.Item.Purchasing.PurchaseItem(); return false;" class="MediumButton" style="width:100%;" />
					</p>
					<p>
						<input type="submit" value="Cancel" onclick="ANORRL.Item.Purchasing.ClosePurchasePanel(); return false;" class="MediumButton" style="width:100%;" />
					</p>
				</div>
				<div id="PurchaseProcessing" style="display: none;">
					<p style="text-align: center;margin: 25px;">
						<img src="/images/ProgressIndicator4White.gif" style="margin-bottom: -20px;margin-left: -25px;"> <span style="font-size: 15px;padding-left: 15px;">Processing...</span>
					</p>
				</div>
				<div id="PurchaseError" style="display: none">
					<p>
						Ok wait... There's been an issue... an error...
					</p>
					<p style="font-weight: bold; color: red" id="Error">
						
					</p>
					<p>
						<input type="submit" value="Cancel" onclick="ANORRL.Item.Purchasing.ClosePurchasePanel(); return false;" class="MediumButton" style="width:100%;" />
					</p>
				</div>
				<div id="PurchaseSuccess" style="display: none">
					<p>
						Awesome sauce! You just bought "<?= $asset->name ?>" from <a target="__blank" href="/users/<?= $asset->creator->id ?>/profile"><?= $asset->creator->name ?></a>!
					</p>
					<?php if($asset->type->wearable()): ?>
					<p>
						<input type="submit" value="Try it on your character!" onclick="window.location.href='/my/character#<?= strtolower($asset->type->label()) ?>'; return false;" class="MediumButton" style="width:100%;" />
					</p>
					<?php else: ?>
					<p>
						<input type="submit" value="Check it out in your inventory!" onclick="window.location.href='/my/stuff#<?= strtolower($asset->type->label()) ?>'; return false;" class="MediumButton" style="width:100%;" />
					</p>
					<?php endif ?>
					<p>
						<input type="submit" value="Continue Shopping" onclick="window.location.href='/catalog'; return false;" class="MediumButton" style="width:100%;" />
					</p>
					<p>
						<input type="submit" value="Close" onclick="window.location.reload(); return false;" class="MediumButton" style="width:100%;" />
					</p>
				</div>
			</div>
		</div>
		<?php endif ?>
		<div id="Container">
		<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
			<div id="Body">
				<div id="BodyContainer">
					<div id="ItemContainer">
						<h4>ANORRL <?= $asset->type->label(); ?></h4>
						<h2 style="padding: 5px 30px;">
							<a class="FavouriteButton" href="#" data-assetid="<?= $asset->id ?>" <?= $is_favourited ? 'favourited="true"' : "" ?>></a>
							<span id="AssetName" title="<?= $asset->name ?>"><?= $asset->name ?></span>
							<a href="javascript:copyToClipboard('<?= $get_related_id ?>')">(Copy Asset ID)</a>
						</h2>
						<div id="ItemDetails">
							<div id="Content">
								<?php if($asset->type == AssetType::AUDIO): ?>
								<img src="/thumbs/?id=<?= $asset->id ?>&sxy=190&nocompress">
								<audio src="/asset/?id=<?= $audio_asset_id ?>" controls>Your browser does not support HTML5 Audio</audio>
								<?php else: ?>
								<img src="/thumbs/?id=<?= $asset->id ?>&sxy=240&nocompress">
								<?php endif ?>
							</div>
							<div id="Information">
								<div id="UserCard">
									<a href="/users/<?= $asset->creator->id ?>/profile"><img src="/thumbs/player?id=<?= $asset->creator->id ?>" style="width: 100px;"></a>
									<div id="AssetInfoStuff">
										<span>Created by <a href="/users/<?= $asset->creator->id ?>/profile"><?= $asset_creator_name ?></a></span>
										<span><b>Created on</b>: <?= $asset->created_at->format('d/m/Y H:i'); ?></span>
										<span><b>Last updated</b>: <?= $asset->last_updatetime->format('d/m/Y H:i'); ?></span>
										<span><b>Favourited</b>: <?= $favourites_count ?></span>
									</div>
								</div>
								<div id="ItemDescription">
									<?= $asset_description ?>
								</div>
							</div>
							<div id="Purchasing">
								<span>Sales: </span><b><?= $asset->sales_count ?></b><br>
								<hr>
								<?php if($user == null): ?>
									<div id="NotOnSale">You need to be logged in to purchase this!</div>
								<?php else: ?>
									<?php if(!$asset->IsUsable()): ?>
										<div id="NotOnSale">This <?= strtolower($asset->type->label()) ?> is broken and needs to be republished.</div>
									<?php else: ?>
										<?php if($asset->onsale): ?>
											<?php if(!$is_bought): ?>
												<button class="PurchaseButton" onclick="ANORRL.Item.Purchasing.OpenPurchasePanel()"><span>Free for grabs!</span></button>
											<?php else: ?>
												<div id="NotOnSale">Hey! You already own this item??</div>
											<?php endif ?>
										<?php else: ?>
											<?php if($is_bought): ?>
												<div id="NotOnSale">Item not on sale and besides you own this.</div>
											<?php else: ?>
												<div id="NotOnSale">Item not on sale.</div>
											<?php endif ?>
										<?php endif ?>
									<?php endif ?>
								<?php endif ?>
								<hr>
								<?php if($user != null): ?>
								<div id="ManageOptions">
									<?php if($is_creator): ?>
									<a href="/edit?id=<?= $asset->id ?>">Configure</a>
									<?php if($asset->IsUsable()): ?>
									<?php if(in_array($asset->type, $rendering_types)): ?><a href="javascript:Render()" id="RenderButton">Render this asset</a><?php endif?>
									<?php endif ?>
									<a href="javascript:Delete()">Delete this asset</a>
									<?php endif ?>
								</div>
								<?php endif ?>
							</div>
						</div>
						<?php if($user != null): ?>
						<div id="CommentsContainer">
							<h3>Users who bought this!</h3>
							<div id="CommentSection">
								<?php if($asset->sales_count > 0): ?>
									<div id="FriendsContainer">
										<ul id="Friends" style="width: 848px;border: 0px;background: none;padding: 0px;">
											<?php 
												$users = $asset->GetSales();

												foreach($users as $u) {
													if($u instanceof User) {
														
														$fID = $u->id;
														$fName = $u->name;
														echo <<<EOT
														<li class="Friend">
															<a id="ProfileLink" href="/users/$fID/profile">
																<img id="Profile" src="/thumbs/headshot?id=$fID&sxy=100">
																<div id="Name">$fName</div>
															</a>
														</li>
														EOT;
													}
													
												}
											?>
										</ul>
									</div>
								<?php else: ?>
									<div id="CommentsDisabled">Aw man! No one bothered to take this <?= strtolower($asset->type->label()) ?> yet!</div>	
								<?php endif ?>
							</div>
						</div>
						<?php endif ?>
						<div id="CommentsContainer">
							<?php if($user == null || !$asset->comments_enabled): ?>
							<h3>Comments</h3>
							<div id="CommentSection">
								<?php if($user == null): ?>
								<div id="CommentsDisabled">You need to be logged in to comment on this item!</div>
								<?php else: ?>
								<div id="CommentsDisabled">Comments have been disabled for this item.</div>
								<?php endif ?>
							</div>
							<?php else: ?>
							<h3>Comments (<?= $comments_count ?>)</h3>
							<div id="CommentPostArea">
								<?php if(isset($_SESSION['ANORRL$Comment$Post$Error'])): ?>
								<div class="Error">Error: <?= $_SESSION['ANORRL$Comment$Post$Error'] ?></div>
								<?php endif ?>
								<form method="POST">
									<h4 style="margin: 0; letter-spacing: 5px;">Post a comment or something</h4>
									<textarea placeholder="Write a wonderful comment about this place!" name="ANORRL$Comment$Post$Contents" maxlength="256" minlength="4"></textarea>
									<input type="submit" value="Submit!" name="ANORRL$Comment$Post$Submit">
								</form>
							</div>
							<div id="CommentSection">
								<?php if($comments_count != 0):
									foreach($comments as $comment) {
										if($comment instanceof Comment) {
											$comment->PrintComment();
										}
									}
								else: ?>
								<div id="CommentsDisabled">It's pretty empty in here... :<</div>
								<?php endif ?>
							</div>
							<?php endif ?>
						</div>
					</div>
				</div>
				<?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
			</div>
		</div>
	</body>
</html>
<?php unset($_SESSION['ANORRL$Comment$Post$Error']); ?>
