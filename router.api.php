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
	route_api('GET|POST', 'people');
	route_api('GET|POST', 'purchase');
	route_api('GET|POST', 'stuff');
	route_api('GET|POST', 'ticketer');
	route_api('GET|POST', 'user');
	route_api('GET|POST', 'placestuff');

	route_api('GET|POST', 'gameservers/close');
	route_api('GET|POST', 'gameservers/removeplayer');
	route_api('GET|POST', 'gameservers/validateplayer');
	route_api('GET|POST', 'gameservers/renewlease');
	route_api('GET',      'gameservers/get');
	route_api('POST',     'gameservers/shutdown');

	route_api('POST', 'asset/render');
	route_api('POST', 'asset/delete');
	route_api('POST', 'asset/refund');
?>