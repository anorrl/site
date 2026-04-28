<?php
	use anorrl\utilities\ClientDetector;

	echo "has access: ". (ClientDetector::HasAccess() ? "yes" : "no");
?>
