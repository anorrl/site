<?php
	use anorrl\Script;

	set_content_type(ARLTYPEPLAIN);

	die(Script::load("gameserver")->sign());
?>
