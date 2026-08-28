<?php
	// Apis!
	route_api("GET",      "background");
	route_api("GET",      "catalog");
	route_api("GET",      "feeds");
	route_api("GET",      "games");
	route_api("GET",      "logout");
	route_api("POST",     "purchase");
	route_api("GET",      "stuff");
	route_api("POST",     "ticketer");
	route_api("GET|POST", "user");
	route_api("GET",      "vandals");
	route_api("GET|POST", "character");
	route_api("GET",      "gameservers/get");
	route_api("POST",     "home");

	route("GET", "/api/creations",  "/private/api/creations.php");

	route("GET", "/thumbnail/avatar/[*:hash]/mtl",           "/private/api/thumbnail/avatar/getters/mtl.php", false);
	route("GET", "/thumbnail/avatar/[*:hash]/obj",           "/private/api/thumbnail/avatar/getters/obj.php", false);
	route("GET", "/thumbnail/avatar/[*:hash]/img/[*:image]", "/private/api/thumbnail/avatar/getters/img.php", false);
	route("GET", "/thumbnail/avatar/generate",               "/private/api/thumbnail/avatar/generate.php",    false);
	route("GET", "/thumbnail/asset/[*:hash]/mtl",            "/private/api/thumbnail/asset/getters/mtl.php",  false);
	route("GET", "/thumbnail/asset/[*:hash]/obj",            "/private/api/thumbnail/asset/getters/obj.php",  false);
	route("GET", "/thumbnail/asset/[*:hash]/img/[*:image]",  "/private/api/thumbnail/asset/getters/img.php",  false);
	route("GET", "/thumbnail/asset/generate",                "/private/api/thumbnail/asset/generate.php",     false);
	route("GET", "/thumbnail/get",                           "/private/api/thumbnail/get.php",                false);

	route("POST", "/asset/[i:id]/setversion/[i:vid]",   "/private/api/asset/setversion.php");
	route("POST", "/asset/[i:id]/render",    "/private/api/asset/render.php");
	route("POST", "/asset/[i:id]/delete",    "/private/api/asset/delete.php");
	route("POST", "/asset/[i:id]/refund",    "/private/api/asset/refund.php");
	route("POST", "/asset/[i:id]/comment",   "/private/api/asset/comment.php");
	route("POST", "/asset/[i:id]/favourite", "/private/api/asset/favourite.php");
	route("GET",  "/asset/[i:id]/versions",  "/private/api/asset/versions.php");

	route("GET", "/asset/[i:id]/comments",  "/private/api/asset/getcomments.php", false);
	route("GET", "/asset/[i:id]/ratings",   "/private/api/asset/getratings.php",  false);
	route("GET", "/users/[i:id]/comments",  "/private/api/users/getcomments.php", false);
	route("GET", "/users/[i:id]/css",       "/private/api/users/css.php",         false);

	route("GET|POST", "/users/[i:id]/friend",       "/private/api/users/friend.php");
	route("GET|POST", "/users/[i:id]/follow",       "/private/api/users/follow.php");

	route("POST", "/asset/[i:id]/rate",   "/private/api/asset/rate.php");

	route("POST", "/users/[i:id]/comment",   "/private/api/users/comment.php");
	route("POST", "/users/update/pfp",       "/private/api/users/update/pfp.php");
	route("POST", "/users/update/banner",    "/private/api/users/update/banner.php");
	route("POST", "/users/remove/pfp",       "/private/api/users/remove/pfp.php");
	route("POST", "/users/remove/banner",    "/private/api/users/remove/banner.php");
	
	route("POST", "/universes/[i:id]/setactive", "/private/api/universes/setactive.php");
	route("POST", "/universes/[i:id]/shutdown", "/private/api/universes/shutdown.php");
	route("POST", "/place/[i:id]/shutdown",     "/private/api/gameservers/shutdown.php");

	route("POST", "/server/[*:jobID]/renewlease",           "/private/api/gameservers/renewlease.php");
	route("POST", "/server/[*:jobID]/close",                "/private/api/gameservers/close.php");
	route("POST", "/server/[*:jobID]/remove/[i:player]",    "/private/api/gameservers/player/remove.php");
	route("POST", "/server/[*:jobID]/validate/[i:player]",  "/private/api/gameservers/player/validate.php");
?>