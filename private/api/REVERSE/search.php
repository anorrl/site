<?php
	set_content_type(ARLTYPEJSON);

	echo json_encode([
	"previousPageCursor" => null,
	"nextPageCursor" => "50_1_".md5(random_bytes(16)),
	"data" => [
		"id" => 20,
		"name" => "BULLLSHIIITT",
		"description" => "description",
		"isArchived" => false,
		"rootPlaceId" => 29,
		"isActive" => true,
		"privacyType" => true,
		"creatorType" => "User",
		"creatorTargetId" => "1",
		"creatorName" => "kuro",
		"created" => date('Y-m-d\TH:i:s.u\Z', time()),
		"updated" => date('Y-m-d\TH:i:s.u\Z', time()),
	],
]);
?>