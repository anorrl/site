<?php
	if(!isset($_POST['ANORRL$MessageBox$Type']) || !isset($_POST['ANORRL$MessageBox$Contents']))
		die();

	$type = intval($_POST['ANORRL$MessageBox$Type']);

	if(!in_array($type, [0,1,2]))
		die();

	$contents = trim($_POST['ANORRL$MessageBox$Contents']);

	if(strlen($contents) == 0)
		die();

	switch($type) {
		case 0: $title = "INFO"; break;
		case 1: $title = "WARNING"; break;
		case 2: $title = "ERROR"; break;
	}
?>
<div class="win7" style="user-select: none">
	<div class="window active">
		<div class="title-bar">
			<div class="title-bar-text"><?= $title ?></div>
			<div class="title-bar-controls" rel="modal:close">
				<button aria-label="Minimize"></button>
				<!--<button aria-label="Maximize"></button>-->
				<button aria-label="Close"></button>
			</div>
		</div>
		<div class="window-body has-space" style="color: black; min-width: 240px;width: 300px;">
			<img src="/public/images/windows/<?= strtolower($title) ?>.png" style="float: left; margin-right: 8px;" width="48">
			<p><?= $contents ?></p>
			<div style="clear: both; text-align: center" rel="modal:close">
				<button>Close</button>
			</div>
		</div>
	</div>
</div>