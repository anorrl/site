<?php

	// base code lifted from pixie by parakeet

	$dir = __DIR__."/private";
	$router = new AltoRouter();

	function route($method, $path, $file, $auth_reroute = true) {
		global $router;
		$router->map($method, $path, function(...$params) use ($path, $file, $auth_reroute) {

			if($auth_reroute && str_starts_with($file, "/private/views/") && !SESSION) {
				redirect("/");
			}

			foreach ($params as $key => $value) {
				$$key = $value;
			}

			    if(str_ends_with($file, ".json")) { set_content_type(ARLTYPEJSON);  }
			elseif(str_ends_with($file,  ".txt")) { set_content_type(ARLTYPEPLAIN); }
			
			if(str_ends_with($file, ".json") || str_ends_with($file, ".js")) {
				$file = file_get_contents(__DIR__.$file);
				$file = str_replace("{domain}", CONFIG->domain, $file);

				exit($file);
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
				exit_http(401);
			}
		});
	}

	function load(string $name) {
		if(strlen(trim($name)) == 0)
			return;

		$path = __DIR__."/router.{$name}.php";

		if(!file_exists($path))
			return;

		require $path;
	}

	//route('GET',      '/test', '/private/views/test.php');

	load("main");
	load("api");
	load("gameapi");
	
	route('GET|POST', '/[*:name]-place', '/private/views/place.php');
	route('GET',      '/asset', '/private/gameapis/assetdeliverer.php');

	$match = $router->match();

	if (is_array($match) && is_callable($match['target'])) {
		call_user_func_array($match['target'], $match['params']);
	} else {
		header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
		require __DIR__.'/private/views/errors/404.php';
		exit();
	}
?>
