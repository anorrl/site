<?php

	// base code lifted from pixie by parakeet

	$dir = __DIR__."/private";
	$router = new AltoRouter();

	function route($method, $path, $file) {
		global $router;
		$router->map($method, $path, function(...$params) use ($path, $file) {
			$secret_enabled = isset(CONFIG->secret);
			if(
				$secret_enabled &&
				str_starts_with($file, "/private/views/") &&
				(!isset($_COOKIE['ANORRL$Hidden$Cookie$yaya']) || 
				(isset($_COOKIE['ANORRL$Hidden$Cookie$yaya']) && $_COOKIE['ANORRL$Hidden$Cookie$yaya'] != CONFIG->secret->token))) {
					die(header("Location: https://arl.lambda.cam/goodbye"));
			} else {
				$secret_enabled = false;
			}
			
			// yeah i just dont feel like it
			if(!$secret_enabled) {
				if(
					$path != "/login" &&
					$path != "/register" &&
					$path != "/" &&
					$path != "/index" &&
					str_starts_with($file, "/private/views/") &&
					!SESSION
				) {
					die(header("Location: /login"));
				}
			}

			foreach ($params as $key => $value) {
				$$key = $value;
			}
			if(str_ends_with($file, ".json")) {
				header("Content-Type: application/json");

			}
			if(str_ends_with($file, ".txt")) {
				header("Content-Type: text/plain");
			}
			
			if(str_ends_with($file, ".json") || str_ends_with($file, ".js")) {
				$file = file_get_contents(__DIR__.$file);
				$file = str_replace("{domain}", CONFIG->domain, $file);

				echo $file;
			} else {
				require __DIR__.$file;
			}
		});
	}

	function route_api($method, $path) {
		global $router;

		$file = "/private/api/$path.php";

		$router->map($method, "/api/$path", function(...$params) use ($path, $file) {
			if(SESSION || (str_starts_with($path, "gameserver") && !str_ends_with($path,"/get"))) {
				foreach ($params as $key => $value) {
					$$key = $value;
				}
				require __DIR__.$file;
			} else {
				die(http_response_code(401));
			}
		});
	}

	//route('GET',      '/test', '/private/views/test.php');
 
	route('GET',      '/', '/private/views/index.php');

	route('GET',      '/index', '/private/views/index.php');
	route('GET|POST', '/login', '/private/views/login.php');
	route('GET|POST', '/register', '/private/views/register.php');
	
	route('GET|POST', '/catalog', '/private/views/catalog.php');
	route('GET|POST', '/games', '/private/views/games.php');
	route('GET|POST', '/vandals', '/private/views/vandals.php');
	route('GET|POST', '/edit', '/private/views/edit.php');

	route('GET|POST', '/create/[i:placeId]/badge', '/private/views/create_badge.php');
	route('GET|POST', '/create/[*:type]', '/private/views/create.php');
	route('GET|POST', '/create/', '/private/views/create.php');
	route('GET|POST', '/create', '/private/views/create.php');

	route('GET|POST', '/[*:name]-item', '/private/views/item.php');

	$router->map('GET', '/game/[i:id]', function($id) {
		$name = "a";
		require __DIR__.'/private/views/place.php';
	});

	route('GET|POST', '/users/[i:id]/profile', '/private/views/users/profile.php');
	route('GET',      '/users/[i:id]/css', '/private/views/users/css.php');
	route('GET',      '/users/[i:id]/followers', '/private/views/users/followers.php');
	route('GET',      '/users/[i:id]/following', '/private/views/users/following.php');
	route('GET',      '/users/[i:id]/friends', '/private/views/users/friends.php');

	route('GET',      '/thumbs/profile', '/private/thumbs/profile.php');
	route('GET',      '/thumbs/player', '/private/thumbs/player.php');
	route('GET',      '/thumbs/headshot', '/private/thumbs/headshot.php');
	route('GET',      '/thumbs/', '/private/thumbs/index.php');

	route('GET',      '/info/credits', '/private/views/info/credits.php');

	route('GET',      '/download', '/private/views/download/index.php');
	route('GET',      '/download/', '/private/views/download/index.php');
	route('GET',      '/download/thankyou', '/private/views/download/thankyou.php');

	route('GET|POST', '/my/home', '/private/views/my/home.php');
	route('GET|POST', '/my/profile', '/private/views/my/profile.php');
	route('GET|POST', '/my/character', '/private/views/my/character.php');
	route('GET|POST', '/my/places', '/private/views/my/places.php');

	route('GET|POST', '/ide/publish', '/private/views/my/places.php');

	route('GET|POST', '/my/stuff', '/private/views/my/stuff.php');
	route('GET|POST', '/my/friends', '/private/views/my/friends.php');
	route('GET|POST', '/my/', '/private/views/my/index.php');

	route('GET', '/badges', '/private/views/badges.php');

	route('GET', '/thumbnail/avatar/[*:hash]/mtl', '/private/api/thumbnail/avatar/getters/mtl.php');
	route('GET', '/thumbnail/avatar/[*:hash]/obj', '/private/api/thumbnail/avatar/getters/obj.php');
	route('GET', '/thumbnail/avatar/[*:hash]/img/[*:image]', '/private/api/thumbnail/avatar/getters/img.php');
	route('GET', '/thumbnail/avatar/generate', '/private/api/thumbnail/avatar/generate.php');

	route('GET', '/thumbnail/asset/[*:hash]/mtl', '/private/api/thumbnail/asset/getters/mtl.php');
	route('GET', '/thumbnail/asset/[*:hash]/obj', '/private/api/thumbnail/asset/getters/obj.php');
	route('GET', '/thumbnail/asset/[*:hash]/img/[*:image]', '/private/api/thumbnail/asset/getters/img.php');
	route('GET', '/thumbnail/asset/generate', '/private/api/thumbnail/asset/generate.php');

	route('GET', '/thumbnail/get', '/private/api/thumbnail/get.php');

	// Apis!
	route_api('GET|POST', 'catalog');
	route_api('GET|POST', 'character');
	route_api('GET|POST', 'comment');
	route_api('GET|POST', 'favourite');
	route_api('GET|POST', 'feeds');
	route_api('GET|POST', 'games');
	route_api('GET|POST', 'gameservers');
	route_api('GET|POST', 'logout');
	route_api('GET|POST', 'outfits');
	route_api('GET|POST', 'people');
	route_api('GET|POST', 'purchase');
	route_api('GET|POST', 'stuff');
	route_api('GET|POST', 'ticketer');
	route_api('GET|POST', 'user');

	route_api('GET|POST', 'gameservers/close');
	route_api('GET|POST', 'gameservers/removeplayer');
	route_api('GET|POST', 'gameservers/validateplayer');
	route_api('GET|POST', 'gameservers/renewlease');
	route_api('GET',      'gameservers/get');
	route_api('POST',     'gameservers/shutdown');

	route_api('GET|POST', 'asset/render');
	route_api('GET|POST', 'asset/delete');

	route('GET', '/users/emotes', '/private/api/users/emotes.php');

	// game apis
	route('GET',      '/asset/', '/private/gameapis/assetdeliverer.php');
	route('GET',      '/Asset/', '/private/gameapis/assetdeliverer.php');
	
	route('GET',      '/users/[i:userId]/canmanage/[i:placeId]', '/private/api/users/canmanage.php');
	route('GET',      '//users/[i:userId]/canmanage/[i:placeId]', '/private/api/users/canmanage.php');
	route('GET',      '/users/[i:userId]/canmanage/[i:placeId]/', '/private/api/users/canmanage.php');
	route('GET',      '/Users/[i:userId]', '/private/api/users/data.php');
	route('GET',      '/users/get-by-username', '/private/api/users/get-by-username.php');

	route('GET',      '/IDE/Upload.aspx', '/private/views/ide/publish.php');
	route('GET|POST', '/IDE/PublishNewPlace.aspx', '/private/views/ide/publishnewplace.php');
	route('GET',      '/IDE/ClientToolbox.aspx', '/private/views/ide/toolbox.php');
	route('GET',      '/ide/toolbox', '/private/views/ide/toolbox.php');
	route('GET|POST', '/Data/Upload.ashx', '/private/gameapis/ide/upload.php');
	route('GET|POST', '/Game/Upload.ashx', '/private/views/ide/goingupload.php');

	route('GET|POST', '/ide/toolbox/items', '/private/api/toolbox/items.php');
	route('GET|POST', '/IDE/Toolbox/Search', '/private/api/toolbox/search.php');
	route('GET|POST', '/IDE/Toolbox/GetTotalNumberOfResults', '/private/api/toolbox/gettotalnumber.php');

	route('GET|POST', '/Sets/SetHandler.ashx', '/private/gameapis/sets/setshandler.php');

	route('GET',      '/Game/Tools/ThumbnailAsset.ashx', '/public/images/unavailable-75.png');
	route('GET',      '/Thumbs/Avatar.ashx', '/private/thumbs/player.php');
	route('GET',      '/thumbs/avatar.ashx', '/private/thumbs/player.php');
	route('GET',      '/Thumbs/RawAsset.ashx', '/private/thumbs/rawasset.php');
	route('GET',      '/Thumbs/Asset.ashx', '/private/thumbs/rawasset.php');
	route('GET',      '/thumbnail/avatar-headshot', '/private/thumbs/fakeheadshot.php');

	route('GET',      '/Game/LuaWebService/HandleSocialRequest.ashx', '/private/gameapis/social/socialrequests.php');
	route('GET|POST', '/game/PlaceLauncher.ashx', '/private/gameapis/gamescripts/placelauncher.php');
	route('GET|POST', '/Game/PlaceLauncher.ashx', '/private/gameapis/gamescripts/placelauncher.php');

	// signed luas
	route('GET',      '/Game/PlaceSpecificScript.slua', '/private/gameapis/gamescripts/placespecificscript.php');
	route('GET',      '/Game/LoadPlaceInfo.slua', '/private/gameapis/gamescripts/loadplaceinfo.php');
	route('GET',      '/game/gameserver.slua', '/private/gameapis/gamescripts/gameserver.php');
	route('GET',      '/game/visit.slua', '/private/gameapis/gamescripts/visit.php');
	route('GET',      '/game/edit.slua', '/private/gameapis/gamescripts/edit.php');

	route('GET',      '/Game/PlaceSpecificScript.ashx', '/private/gameapis/gamescripts/placespecificscript.php');
	route('GET',      '/Game/LoadPlaceInfo.ashx', '/private/gameapis/gamescripts/loadplaceinfo.php');
	route('GET',      '/game/join.ashx', '/private/gameapis/gamescripts/join.php');

	route('GET',      '/game/load-place-info', '/private/gameapis/places/load-place-info.php');

	route('GET',      '/GetAllowedMD5Hashes/', '/private/gameapis/authentication/getallowedmd5hashes.json');
	route('GET',      '/GetAllowedSecurityKeys/', '/private/gameapis/authentication/getallowedsecuritykeys.json');
	route('GET',      '/GetAllowedSecurityVersions/', '/private/gameapis/authentication/getallowedsecurityversions.json');

	route('GET',      '/Setting/QuietGet/AndroidAppSettings/', '/private/gameapis/settings/ClientAppSettings.json');
	route('GET',      '/Setting/QuietGet/ClientAppSettings/', '/private/gameapis/settings/ClientAppSettings.json');
	route('GET',      '/Setting/QuietGet/ClientSharedSettings/', '/private/gameapis/settings/ClientSharedSettings.json');
	route('GET',      '/Setting/QuietGet/ACCService'.CONFIG->asset->key.'/', '/private/gameapis/settings/ACCService.json');
	route('GET',      '/Setting/QuietGet/WindowsBootstrapperSettings/', '/private/gameapis/settings/Bootstrapper.json');
	route('GET',      '/Setting/QuietGet/WindowsStudioBootstrapperSettings/', '/private/gameapis/settings/Bootstrapper.json');

	route('GET|POST', '/v1.0/MultiIncrement/', '/private/templates/responses/nothing.txt');
	route('GET|POST', '/Error/Dmp.ashx', '/private/templates/responses/nothing.txt');
	route('GET|POST', '/v1.1/Counters/Increment/', '/private/templates/responses/nothing.txt');
	route('GET|POST', '/v1.1/counters/increment/', '/private/templates/responses/nothing.txt');
	route('GET|POST', '/game/report-stats', '/private/templates/responses/nothing.txt');
	route('GET|POST', '/Game/report-stats', '/private/templates/responses/nothing.txt');
	route('GET|POST', '/game/validate-machine', '/private/templates/responses/success.json');
	route('GET|POST', '/mac-address/validate-machine', '/private/templates/responses/success.json');
	route('GET|POST', '/Game/validate-machine', '/private/templates/responses/success.json');

	route('GET',      '/Login/Negotiate.ashx', '/private/gameapis/authentication/negotiate.php');
	route('GET',      '/Login/RequestAuth.ashx', '/private/gameapis/authentication/requestauth.php');
	route('GET',      '/login/RequestAuth.ashx', '/private/gameapis/authentication/requestauth.php');
	route('GET',      '/game/GetCurrentUser.ashx', '/private/gameapis/authentication/getcurrentuser.php');
	route('GET',      '/Game/GetCurrentUser.ashx', '/private/gameapis/authentication/getcurrentuser.php');

	route('GET',      '/game/logout.aspx', '/private/api/logout.php');
	route('GET',      '/Game/logout.aspx', '/private/api/logout.php');

	route('GET',      '/game/players/[i:id]', '/private/api/users/players.php');
	route('GET',      '//game/players/[i:id]', '/private/api/users/players.php');
	route('GET',	  '//game/players/[i:id]/', '/private/api/users/players.php');
	route('GET',      '/game/players/[i:id]/', '/private/api/users/players.php');
	
	route('GET|POST', '/persistence/getV2.aspx', '/private/gameapis/persistence/getv2.php');
	route('GET|POST', '/persistence/getV2', '/private/gameapis/persistence/getv2.php');
	route('GET|POST', '/persistence/getSortedValues', '/private/gameapis/persistence/getv2.php');
	route('GET|POST', '/persistence/increment.aspx', '/private/gameapis/persistence/increment.php');
	route('GET|POST', '/persistence/set.aspx', '/private/gameapis/persistence/set.php');
	route('GET|POST', '/persistence/set', '/private/gameapis/persistence/set.php');
	route('GET|POST', '/Persistence/SetBlob.ashx', '/private/gameapis/datastores/setblob.php');
	route('GET|POST', '/Persistence/GetBlob.ashx', '/private/gameapis/datastores/getblob.php');

	route('GET',      '/userblock/getblockedusers', '/private/gameapis/social/getblockedusers.php');
	route('GET',      '/user/following-exists', '/private/gameapis/social/following-exists.php');
	route('GET',      '/user/get-friendship-count', '/private/gameapis/social/get-friendship-count.php');
	route('GET|POST', '/user/follow', '/private/gameapis/social/follow.php');
	route('GET|POST', '/user/unfollow', '/private/gameapis/social/unfollow.php');
	route('GET',      '/user/request-friendship', '/private/gameapis/social/request-friendship.php');
	route('GET',      '/user/decline-friend-request', '/private/gameapis/social/decline-friend-request.php');
	route('GET|POST', '/Game/AreFriends', '/private/gameapis/social/arefriends.php');

	route('GET',      '/universes/get-universe-containing-place', '/private/gameapis/universes/get-universe-containing-place.php');
	route('GET',      '/universes/[i:universeId]/cloudeditenabled', '/private/gameapis/universes/cloudeditenabled.php');
	route('GET',      '/universes/[i:universeId]/game-start-info', '/private/gameapis/universes/game-start-info.php');
	route('GET|POST', '/universes/[i:universeId]/enablecloudedit', '/private/gameapis/universes/enablecloudedit.php');
	route('GET|POST', '/universes/[i:universeId]/disablecloudedit', '/private/gameapis/universes/disablecloudedit.php');
	route('GET',      '/universes/[i:universeId]/listcloudeditors', '/private/gameapis/universes/listcloudeditors.php');
	route('GET|POST', '/universes/[i:universeId]/addcloudeditor', '/private/gameapis/universes/addcloudeditor.php');
	route('GET|POST', '/universes/[i:universeId]/removecloudeditor', '/private/gameapis/universes/removecloudeditor.php');
	route('GET',      '/places/[i:placeId]/settings', '/private/gameapis/places/settings.php');
	route('GET',      '/places/[i:placeId]/settings', '/private/gameapis/places/settings.php');
	route('GET',      '/universes/get-info', '/private/gameapis/universes/get-info.php');
	route('GET',      '/universes/validate-place-join', '/private/gameapis/universes/validate-place-join.php');
	route('GET',      '/universes/get-universe-places', '/private/gameapis/universes/get-universe-places.php');
	route('GET',      '/universes/get-aliases', '/private/gameapis/universes/get-aliases.php');
	route('GET',      '/developerproducts/list', '/private/gameapis/universes/developerproducts.php');
	route('GET',      '/badges/list-badges-for-place/json', '/private/gameapis/places/list-badges-for-place.php');

	route('GET',      '/Asset/BodyColors.ashx', '/private/gameapis/character/bodycolors.php');
	route('GET',      '/Asset/CharacterFetch.ashx', '/private/gameapis/character/characterfetch.php');

	route('GET|POST', '/game/MachineConfiguration.ashx', '/private/gameapis/authentication/machineconfiguration.txt');
	route('GET|POST', '/Game/MachineConfiguration.ashx', '/private/gameapis/authentication/machineconfiguration.txt');

	route('GET',      '/Game/Tools/InsertAsset.ashx', '/private/api/insertasset.php');

	route('GET',      '/UploadMedia/PostImage.aspx', '/private/gameapis/uploadmedia/postimage.php');
	route('GET',      '/UploadMedia/UploadVideo.aspx', '/private/gameapis/uploadmedia/uploadvideo.php');

	route('GET|POST', '/moderation/v2/filtertext', '/private/gameapis/moderation/filtertext.php');
	route('GET|POST', '//moderation/filtertext/', '/private/gameapis/moderation/filtertext.php');
	route('GET|POST', '/moderation/filtertext/', '/private/gameapis/moderation/filtertext.php');

	route('GET',      '/marketplace/productinfo', '/private/gameapis/marketplace/productinfo.php');
	route('GET',      '/Marketplace/ProductInfo', '/private/gameapis/marketplace/productinfo.php');
	route('GET',      '/marketplace/productDetails', '/private/gameapis/marketplace/productinfo.php');
	route('GET',      '/marketplace/purchase', '/private/gameapis/marketplace/purchase.php');
	route('GET',      '/ownership/hasasset', '/private/gameapis/marketplace/hasasset.php');
	route('GET',      '/ownership/hasAsset', '/private/gameapis/marketplace/hasasset.php');
	route('GET',      '/gametransactions/getpendingtransactions/', '/private/gameapis/marketplace/getpendingtransactions.php');
	route('GET',      '/gametransactions/getpendingtransactions', '/private/gameapis/marketplace/getpendingtransactions.php');
	route('GET',      '/currency/balance', '/private/gameapis/marketplace/balance.php');

	route('GET',      '/inbox', '/private/views/mobile/inbox.php');
	route('GET',      '/home', '/private/views/mobile/home.php');
	route('GET',      '/mobile-app-upgrades/native-ios/bc', '/private/views/mobile/nocurrencylol.php');
	route('GET',      '/mobile-app-upgrades/native-ios/robux', '/private/views/mobile/nocurrencylol.php');
	route('GET',      '/mobile/games', '/private/views/mobile/games.php');
	route('GET',      '/mobile/games/', '/private/views/mobile/games.php');
	route('GET',      '/signup/is-username-valid', '/private/api/mobile/is-username-valid.php');
	route('GET',      '/signup/is-password-valid', '/private/api/mobile/is-password-valid.php');
	route('GET',      '/UserCheck/getrecommendedusername', '/private/api/mobile/getrecommendedusername.php');

	route('GET|POST', '/mobileapi/login', '/private/api/mobile/login.php');
	route('GET|POST', '/mobileapi/securesignup', '/private/api/mobile/securesignup.php');

	route('GET',      '/UserCheck/getrecommendedusername', '/private/api/mobile/getrecommendedusername.php');
	
	route('GET|POST', '/[*:name]-place', '/private/views/place.php');
	route('GET',      '/asset', '/private/gameapis/assetdeliverer.php');

	$match = $router->match();

	if (is_array($match) && is_callable($match['target'])) {
		call_user_func_array($match['target'], $match['params']);
	} else {
		header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
		if(!SESSION && isset(CONFIG->secret)) {
			die(header("Location: https://arl.lambda.cam/goodbye"));
		}
		require __DIR__.'/private/views/errors/404.php';
		exit();
	}
?>
