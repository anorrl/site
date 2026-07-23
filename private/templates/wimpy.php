<!-- remove this -->
<div style="position: absolute;bottom: 33px;left: 10px;width: 300px;height: 70px;z-index: 9999; white-space: nowrap;">
	<div 
		data-wimpyplayer=""
		data-skin="/public/wimpy/skins/Slick_modified.tsv"
		data-loop="2"
		data-disablecontrols="next,playlist,rewind,getid3"
		style="text-align: center;"
		data-media="<?= $this->url ?>.mp3"
		data-volume="0.4"
	></div>
	<div style="border: 2px solid black; background: #222; color: white; padding: 5px; text-align: center; margin-left:-2px">
		<?php if(strlen($this->link) == 0): ?>
			<?= $this->name ?>
		<?php else: ?>
			<a href="<?= $this->link ?>"><?= $this->name ?></a>
		<?php endif ?>
	</div>
	<?php if($this->cover_art != ""): ?>
	<div style="border: 2px solid black;margin-left:-2px; height: 298px; border-top: none">
		<img src="<?= $this->cover_art ?>" style="width: 298px; height: 298px; background: #111;">
	</div>
	<?php endif ?>
</div>
