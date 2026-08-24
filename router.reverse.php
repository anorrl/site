<?php
	route('GET|POST', '/v2/login', '/private/api/REVERSE/login.php');
	route('GET|POST', '/v1/gametemplates', '/private/api/REVERSE/gametemplates.json');
	route('GET|POST', '/v1/user/groups/canmanage', '/private/api/REVERSE/canmanage.json'); 
	route('GET|POST', '/v1/search/universes', '/private/api/REVERSE/search.php'); 
	route('GET|POST', '/asset-gameicon/multiget', '/private/api/REVERSE/multiget.php');
?>