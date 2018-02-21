<!-- 
this file is modified from simpleform.html in SimpleExample app
 -->
<h1>Welcome!</h1>
<form name="form" action="<?= ($BASE) ?>/search" method="post"> 
	<input name="tag" type="text" placeholder="Enter mood" id="tag" size="50" />
	<input type="submit"  value="submit">
</form>
<?php foreach (($moodtags?:[]) as $mood): ?>
	<label><?= ($mood['description']) ?></label>
<?php endforeach; ?>
</p>

