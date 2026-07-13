<?php
	route('GET|POST', '/', '/private/views/index.php', false);
	route('GET',      '/index', '/private/views/index.php', false);
	route('GET|POST', '/login', '/private/views/login.php', false);
	route('GET|POST', '/register', '/private/views/register.php', false);
	
	route('GET|POST', '/catalog', '/private/views/catalog.php');
	route('GET|POST', '/games', '/private/views/games.php');
	route('GET|POST', '/vandals', '/private/views/vandals.php');
	route('GET|POST', '/edit', '/private/views/edit.php');
	
	route('GET|POST', '/create/[i:placeId]/[*:type]', '/private/views/create_place.php');
	route('GET|POST', '/create/[*:type]', '/private/views/create.php');
	route('GET|POST', '/create/', '/private/views/create.php');
	route('GET|POST', '/create', '/private/views/create.php');

	route('GET|POST', '/[*:name]-item', '/private/views/item.php');

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

	route('GET|POST', '/my/stuff', '/private/views/my/stuff.php');
	route('GET|POST', '/my/friends', '/private/views/my/friends.php');
	route('GET|POST', '/my/', '/private/views/my/index.php');

	route('GET|POST', '/develop/projects', '/private/views/develop/projects.php');
	route('GET|POST', '/ide/publish', '/private/views/develop/projects.php');

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
?>