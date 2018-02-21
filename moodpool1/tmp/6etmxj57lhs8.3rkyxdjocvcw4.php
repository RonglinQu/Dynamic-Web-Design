<h1>Upload</h1>
<form name="upload" method="POST" action="<?= ($BASE) ?>/uploadImage" enctype="multipart/form-data">
	<label for='picfile'>Select image file: </label>
	<input type="file" name="picfile" id="picfile" /><br />
	<label for='picname'>Picture title: </label>
	<input type="text" name="picname" id="picanme" placeholder="Title for image" size="80"/><br />
	</p><label>Please choose at least one mood tag from the list</label>
	<?php foreach (($datalist?:[]) as $moodtype): ?>
		<div id="tagselection">
			<input type="checkbox" name="mood[]" value="<?= ($moodtype['description']) ?>" /><label><?= ($moodtype['description']) ?></label>
		</div>
	<?php endforeach; ?>
	<input type="submit" name="submit" value="Submit"/>
</form>
