<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<!-- Bootstrap -->
<link href="<?= ($UI) ?>css/bootstrap.min.css" rel="stylesheet">	
<!-- Stylesheet CSS-->	
<link href="<?= ($UI) ?>css/filtertest_style.css" rel="stylesheet" type="text/css">	
<!-- JavaScripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>	
<script src="<?= ($UI) ?>js/prueba.js"></script>
<!-- 180221 add by lydia -->
<script src="<?= ($UI) ?>js/searchresult.js"></script>

	<!-- JavaScripts POPOUT -->

<body>
<div id="wrapper">	 
<!--LOGO--> 	
<div class="col-md-12">	
<div class="row"> 
<!--Mood Selector--> 	   
	  Select a mood:
      <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle moodselector-style" data-toggle="dropdown">
            <?= ($mood) ?> <span class="caret"></span>
        </button>
          <ul class="dropdown-menu" role="menu">
            <!--<li><a href="#">Relaxed</a></li>
            <li><a href="#">Joy</a></li>
            <li><a href="#">Melancholy</a></li>
            <li><a href="#">Gloomy</a></li> -->
			<?php foreach (($moodlist?:[]) as $mood): ?>
				<li><a href="<?= ($BASE) ?>/search/<?= ($mood['description']) ?>"><?= ($mood['description']) ?></a></li>
			<?php endforeach; ?>
          </ul>
	  </div>
<!--User image Btn-->		
</div>   
</div>	

<div class="col-md-12">
<div class="row"> 	
<div class="category-continer">
	<p class="category_item item_style" id="all">All Media </p>
	<p class="category_item item_style" id="photo">Photos</p>
	<p class="category_item item_style" id="video">Videos</p>
	<p class="category_item item_style" id="music">Music</p>
	<p class="category_item item_style" id="illustration">Illustration</p>
	<p class="category_item item_style" id="article">Articles</p>
	<p class="category_item item_style" id="book">Books</p>
</div>
</div>
</div>
	
<div class="col-md-12">
<div class="row">
<div class="block_container">
<!-- Ejemplo popout -->
	<?php foreach (($searchresult?:[]) as $ikey=>$item): ?>
		<div id="imgdisplay" >
			<p><a href="<?= ($item['img']) ?>" id = "img<?= ($ikey) ?>"><img src="<?= ($item['img_m']) ?>" class="mood_item photo /></a></p>
			<p><a href="<?= ($item['url']) ?>" id = "url<?= ($ikey) ?>">link</a></p>
			<p><i id="title<?= ($ikey) ?>"><?= ($item['title']) ?></i>
			<button id="button<?= ($ikey) ?>" value="<?= ($item['mediaid']) ?>" onclick="passInf(<?= ($ikey) ?>)"><?= ($item['liked']) ?></button></p>
			<script type="text/javascript">
				liked(<?= ($ikey) ?>);
			</script>
		</div>
	<?php endforeach; ?>
	<!-- <img src="http://via.placeholder.com/200x175" class="mood_item photo"> -->
<!-- after class add the especific tag for the media -->
	<!-- <img src="http://via.placeholder.com/200x175" class="mood_item photo">
	<img src="http://via.placeholder.com/200x175" class="mood_item video">
	<img src="http://via.placeholder.com/200x175" class="mood_item photo">
	<img src="http://via.placeholder.com/200x175" class="mood_item video">
	<img src="http://via.placeholder.com/200x175" class="mood_item music">
	<img src="http://via.placeholder.com/200x175" class="mood_item book">
	<img src="http://via.placeholder.com/200x175" class="mood_item photo">
	<img src="http://via.placeholder.com/200x175" class="mood_item music">
	<img src="http://via.placeholder.com/200x175" class="mood_item photo">
	<img src="http://via.placeholder.com/200x175" class="mood_item book">
	<img src="http://via.placeholder.com/200x175" class="mood_item article">
	<img src="http://via.placeholder.com/200x175" class="mood_item music">
	<img src="http://via.placeholder.com/200x175" class="mood_item illustration">
	<img src="http://via.placeholder.com/200x175" class="mood_item video">
	<img src="http://via.placeholder.com/200x175" class="mood_item illustration">
	<img src="http://via.placeholder.com/200x175" class="mood_item music">
	<img src="http://via.placeholder.com/200x175" class="mood_item video">
	<img src="http://via.placeholder.com/200x175" class="mood_item photo">
	<img src="http://via.placeholder.com/200x175" class="mood_item music">
	<img src="http://via.placeholder.com/200x175" class="mood_item illustration">
	<img src="http://via.placeholder.com/200x175" class="mood_item book">
	<img src="http://via.placeholder.com/200x175" class="mood_item article"> -->
</div>
</div>
</div>
<!-- POPOUT -->		
	
<!-- POPOUT -->	
</div>	
</body>
</html>
