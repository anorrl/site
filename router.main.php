<?php
	route('GET|POST', '/', '/private/views/index.php', false);
	route('GET',      '/catalog', '/private/views/catalog.php', false);
	route('GET',      '/games', '/private/views/games.php', false);
	route('GET',      '/vandals', '/private/views/vandals.php', false);
	route('GET',      '/badges', '/private/views/badges.php', false);

	
	route('GET|POST', '/catalog/[i:id]/[*:name]/sales', '/private/views/sales.php', false);
	route('GET|POST', '/catalog/[i:id]/[*:name]', '/private/views/item.php', false);
	route('GET|POST', '/games/[i:id]/[*:name]', '/private/views/place.php', false);

	route('GET',      '/info/credits', '/private/views/info/credits.php');
	route('GET',      '/info/about', '/private/views/info/about.php', false);
	route('GET',      '/info/privacy', '/private/views/info/privacy.php', false);
	route('GET',      '/info/terms', '/private/views/info/terms.php', false);
	
	route('GET',      '/develop', '/private/views/develop/index.php', false);
	route('GET',      '/develop/', '/private/views/develop/index.php', false);

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

	route('GET|POST', '/users/[i:id]/profile', '/private/views/users/profile.php', false);
	route('GET',      '/users/[i:id]/followers', '/private/views/users/followers.php', false);
	route('GET',      '/users/[i:id]/following', '/private/views/users/following.php', false);
	route('GET',      '/users/[i:id]/friends', '/private/views/users/friends.php', false);

	route('GET|POST', '/my/home', '/private/views/my/home.php');
	route('GET|POST', '/my/profile', '/private/views/my/profile.php');
	route('GET|POST', '/my/character', '/private/views/my/character.php');
	route('GET|POST', '/my/stuff', '/private/views/my/stuff.php');
	route('GET|POST', '/my/friends', '/private/views/my/friends.php');

	route('GET',      '/thumbs/', '/private/thumbs/index.php');

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