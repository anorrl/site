<style>
	.game {
		text-align: center;
		padding: 10px;
		user-select: none;
	}

	.game #name {
		font-size: 13px;
		font-weight: bold;
		width: 181px;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
		padding-top: 5px;
		text-align: center;
		margin: 0 auto;
	}

	.game #thumbnail {
		width: 170px;
	}

	.game:hover {
		border: 2px solid var(--border-color);
		margin: -2px;
		background: linear-gradient(180deg,rgb(26, 12, 35) 0%, rgb(73, 34, 101) 100%);
		margin-bottom: -31px;
		z-index: 9999;
	}

	.game #info {
		display: none;
	}

	.game:hover #info {
		display: block;
	}

	
</style>
<div class="game" template>
	<a id="url" href>
		<img id="thumbnail" src>
		<div id="name"></div>
	</a>
</div>
<div id="panel">
	<div class="box" style="padding: 15px;">
		<h2>.games</h2>
		<hr>
		<input type="submit" class="button" value="create a place"> <span style="font-family: road; font-size: 14px;">(5 slots left)</span>
	</div>
	<div class="box" style="padding: 15px; margin-top: 10px;">
		<h2>.games <input class="box input" id="search-box" style="padding: 0px 5px;" placeholder="search whatever dude..."></h2>
		<hr>
		<?php generate_statuses() ?>
		<div data-loadtype="9"></div>
	</div>
</div>