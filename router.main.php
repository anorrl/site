<?php
	route('GET|POST', '/', '/private/views/index.php', false);
	route('GET',      '/catalog', '/private/views/catalog.php');
	route('GET',      '/games', '/private/views/games.php');
	route('GET',      '/vandals', '/private/views/vandals.php');
	route('GET',      '/badges', '/private/views/badges.php');

	route('GET|POST', '/catalog/[i:id]/[*:name]', '/private/views/item.php');
	route('GET|POST', '/games/[i:id]/[*:name]', '/private/views/place.php');

	route('GET',      '/info/credits', '/private/views/info/credits.php');
	route('GET',      '/info/about', '/private/views/info/about.php', false);
	route('GET',      '/info/privacy', '/private/views/info/privacy.php', false);
	route('GET',      '/info/terms', '/private/views/info/terms.php', false);
	
	route('GET',      '/develop', '/private/views/develop/index.php');
	route('GET',      '/develop/', '/private/views/develop/index.php');

	// replace with creations
	route('GET|POST', '/develop/create/[*:type]', '/private/views/develop/create.php');
	route('GET',      '/develop/create/', '/private/views/develop/create.php');
	route('GET',      '/develop/create',  '/private/views/develop/create.php');
	
	route('GET',      '/develop/creations/[*:type]',  '/private/views/develop/creations.php');
	route('GET',      '/develop/creations/',  '/private/views/develop/creations.php');
	route('GET',      '/develop/creations',  '/private/views/develop/creations.php');

	route('GET|POST', '/develop/[i:id]/configure', '/private/views/develop/configure.php');

	// studio
	route('GET|POST', '/develop/place/[i:placeId]/[*:type]', '/private/views/develop/place/create.php');
	route('GET|POST', '/develop/projects', '/private/views/develop/projects.php');
	route('GET|POST', '/ide/publish', '/private/views/develop/projects.php');

	route('GET|POST', '/users/[i:id]/profile', '/private/views/users/profile.php');
	route('GET',      '/users/[i:id]/followers', '/private/views/users/followers.php');
	route('GET',      '/users/[i:id]/following', '/private/views/users/following.php');
	route('GET',      '/users/[i:id]/friends', '/private/views/users/friends.php');

	route('GET|POST', '/my/home', '/private/views/my/home.php');
	route('GET|POST', '/my/profile', '/private/views/my/profile.php');
	route('GET|POST', '/my/character', '/private/views/my/character.php');
	route('GET|POST', '/my/stuff', '/private/views/my/stuff.php');
	route('GET|POST', '/my/friends', '/private/views/my/friends.php');

	route('GET',      '/thumbs/', '/private/thumbs/index.php');

	route('GET',      '/thumbnail/avatar/[*:hash]/mtl', '/private/api/thumbnail/avatar/getters/mtl.php');
	route('GET',      '/thumbnail/avatar/[*:hash]/obj', '/private/api/thumbnail/avatar/getters/obj.php');
	route('GET',      '/thumbnail/avatar/[*:hash]/img/[*:image]', '/private/api/thumbnail/avatar/getters/img.php');
	route('GET',      '/thumbnail/avatar/generate', '/private/api/thumbnail/avatar/generate.php');
	route('GET',      '/thumbnail/asset/[*:hash]/mtl', '/private/api/thumbnail/asset/getters/mtl.php');
	route('GET',      '/thumbnail/asset/[*:hash]/obj', '/private/api/thumbnail/asset/getters/obj.php');
	route('GET',      '/thumbnail/asset/[*:hash]/img/[*:image]', '/private/api/thumbnail/asset/getters/img.php');
	route('GET',      '/thumbnail/asset/generate', '/private/api/thumbnail/asset/generate.php');
	route('GET',      '/thumbnail/get', '/private/api/thumbnail/get.php');

	route('GET',      '/mobile/inbox', '/private/views/mobile/inbox.php');
	route('GET',      '/mobile/home', '/private/views/mobile/home.php');
	route('GET',      '/mobile-app-upgrades/native-ios/bc', '/private/views/mobile/nocurrencylol.php');
	route('GET',      '/mobile-app-upgrades/native-ios/robux', '/private/views/mobile/nocurrencylol.php');
	route('GET',      '/mobile/games', '/private/views/mobile/games.php');
	route('GET',      '/mobile/games/', '/private/views/mobile/games.php');

	route('GET',      '/download/thankyou', '/private/views/download/thankyou.php');

	route_redirect('GET', '/users/', '/my/home');
	route_redirect('GET', '/my/',    '/my/home');
?>