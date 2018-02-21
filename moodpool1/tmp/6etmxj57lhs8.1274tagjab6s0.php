<h3>Uploaded</h3>
<?php foreach (($datalist?:[]) as $item): ?>
	<div id="imgdisplay">
		<p><a href="<?= ($BASE) ?>/<?= ($item['url']) ?>"><img src="<?= ($BASE) ?>/<?= ($item['thumbNail']) ?>" /></a></p>
		<p><?= ($item['title']) ?>  (<a href="<?= ($BASE) ?>/delete/<?= ($item['recomid']) ?>">Delete?</a>)</p>
		<?php foreach (($item['tags']?:[]) as $moodtag): ?>
			<label><?= ($moodtag) ?></label>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<hr/>
<h3>Likes</h3>
<?php foreach (($likedimages?:[]) as $imageinfo): ?>
	<div id="imgdisplay">
		<p><a href="<?= ($imageinfo['img']) ?>"><img src="<?= ($imageinfo['img_m']) ?>" /></a></p>
		<p><?= ($imageinfo['title']) ?>  (<a href="<?= ($BASE) ?>/unlike/<?= ($imageinfo['mediaid']) ?>">unlike</a>)</p>
		<?php foreach (($imageinfo['tags']?:[]) as $moodtag): ?>
			<label><?= ($moodtag) ?></label>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>

