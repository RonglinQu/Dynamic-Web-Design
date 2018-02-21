<!-- 
this file is modified from simpleform.html in SimpleExample app
 -->
<h1>Welcome home, <?= ($username) ?></h1>
<form name="form" action="<?= ($BASE) ?>/upload" method="post"> 
	<input type="submit"  value="upload">
</form>
<?php echo $this->render($recommendation,NULL,get_defined_vars(),0); ?>
</p>

