<?php

	// lifted from pixie - by parakeet

	$dir = __DIR__."/private";
	$router = new AltoRouter();

	function route($method, $path, $file) {
		global $router;
		$router->map($method, $path, function(...$params) use ($file) {
			foreach ($params as $key => $value) {
				$$key = $value;
			}
			require __DIR__.$file;
		});
	}

	function route_api($method, $path) {
		global $router;

		$file = "/private/api/$path.php";

		$router->map($method, "/api/$path", function(...$params) use ($file) {
			foreach ($params as $key => $value) {
				$$key = $value;
			}
			require __DIR__.$file;
		});
	}
 
	route('GET',      '/', '/private/views/index.php');
	route('GET',      '/index', '/private/views/index.php');
	route('GET|POST', '/login', '/private/views/login.php');
	route('GET|POST', '/register', '/private/views/register.php');
	
	route('GET|POST', '/catalog', '/private/views/catalog.php');
	route('GET|POST', '/games', '/private/views/games.php');
	route('GET|POST', '/vandals', '/private/views/vandals.php');
	route('GET|POST', '/edit', '/private/views/edit.php');

	route('GET|POST', '/create/[*:type]', '/private/views/create.php');
	route('GET|POST', '/create/', '/private/views/create.php');

	route('GET|POST', '/[*:name]-item', '/private/views/item.php');
	route('GET|POST', '/[*:name]-place', '/private/views/place.php');

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
	route('GET|POST', '/my/stuff', '/private/views/my/stuff.php');
	route('GET|POST', '/my/friends', '/private/views/my/friends.php');
	route('GET|POST', '/my/', '/private/views/my/index.php');

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

	// game apis

	route('GET',      '/asset/', '/private/api/assetdeliverer.php');
	route('GET',      '/Asset/', '/private/api/assetdeliverer.php');


	$match = $router->match();

	if (is_array($match) && is_callable($match['target'])) {
		call_user_func_array($match['target'], $match['params']);
	} else {
		header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
		require __DIR__.'/private/views/errors/404.php';
		exit();
	}
?>