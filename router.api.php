<?php
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
	route_api('GET|POST', 'vandals');
	route_api('GET|POST', 'purchase');
	route_api('GET|POST', 'stuff');
	route_api('GET|POST', 'ticketer');
	route_api('GET|POST', 'user');
	route_api('GET|POST', 'placestuff');

	route('GET',      '/thumbnail/avatar/[*:hash]/mtl', '/private/api/thumbnail/avatar/getters/mtl.php', false);
	route('GET',      '/thumbnail/avatar/[*:hash]/obj', '/private/api/thumbnail/avatar/getters/obj.php', false);
	route('GET',      '/thumbnail/avatar/[*:hash]/img/[*:image]', '/private/api/thumbnail/avatar/getters/img.php', false);
	route('GET',      '/thumbnail/avatar/generate', '/private/api/thumbnail/avatar/generate.php', false);
	route('GET',      '/thumbnail/asset/[*:hash]/mtl', '/private/api/thumbnail/asset/getters/mtl.php', false);
	route('GET',      '/thumbnail/asset/[*:hash]/obj', '/private/api/thumbnail/asset/getters/obj.php', false);
	route('GET',      '/thumbnail/asset/[*:hash]/img/[*:image]', '/private/api/thumbnail/asset/getters/img.php', false);
	route('GET',      '/thumbnail/asset/generate', '/private/api/thumbnail/asset/generate.php', false);
	route('GET',      '/thumbnail/get', '/private/api/thumbnail/get.php', false);
	
	route('POST', "/universes/[i:id]/shutdown", "/private/api/universes/shutdown.php");

	route('GET',  "/asset/[i:id]/versions", "/private/api/asset/versions.php");
	route('POST', "/asset/[i:id]/render",   "/private/api/asset/render.php");
	route('POST', "/asset/[i:id]/delete",   "/private/api/asset/delete.php");
	route('POST', "/asset/[i:id]/refund",   "/private/api/asset/refund.php");
	route('POST', "/asset/[i:id]/setversion/[i:vid]",   "/private/api/asset/setversion.php");

	route('GET',  '/users/[i:id]/css',       '/private/api/users/css.php', false);
	route('POST', '/users/update/pfp',       '/private/api/users/updatepfp.php');
	route('POST', '/users/remove/pfp',       '/private/api/users/removepfp.php');
	route('POST', '/users/update/banner',    '/private/api/users/updatebanner.php');
	route('POST', '/users/remove/banner',    '/private/api/users/removebanner.php');
	route('GET', '/api/background', '/private/api/background.php', false);

	route_api('GET|POST', 'gameservers/close');
	route_api('GET|POST', 'gameservers/removeplayer');
	route_api('GET|POST', 'gameservers/validateplayer');
	route_api('GET|POST', 'gameservers/renewlease');
	route_api('GET',      'gameservers/get');
	route_api('POST',     'gameservers/shutdown');

?>