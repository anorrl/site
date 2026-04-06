<!DOCTYPE html>
<html>
	<head>
		<title><?= $this->title ?></title>
		<link rel="icon" type="image/x-icon" href="/favicon.ico">
		<?php foreach($this->scripts as $script): ?>
		<script src="<?= $script ?>"></script>
		<?php endforeach ?>
		<?php foreach($this->stylesheets as $stylesheet): ?>
		<link rel="stylesheet" href="<?= $stylesheet ?>">
		<?php endforeach ?>

		<?php if(count($this->metas) != 0): ?>
			<?php foreach($this->metas as $meta): ?>
			<meta property="<?= $meta['type'] ?>" content="<?= $meta['contents'] ?>">
			<?php endforeach ?>
		<?php endif ?>
	</head>
	<body <?= $this->settings->nightbg_enabled ? "night" : "" ?>>