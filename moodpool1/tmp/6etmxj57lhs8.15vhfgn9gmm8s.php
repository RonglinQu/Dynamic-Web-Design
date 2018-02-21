<!DOCTYPE html>
<html>
	<head>
	  <title><?= ($html_title) ?></title>
	  <meta charset='utf8' />
	  <link href="<?= ($BASE) ?>/<?= ($UI) ?>css/filtertest_style.css" rel="stylesheet" type="text/css">	
	  <link href="<?= ($BASE) ?>/<?= ($UI) ?>css/bootstrap.min.css" rel="stylesheet">
	  <script type="text/javascript" src="<?= ($BASE) ?>/<?= ($UI) ?>js/layout.js"></script>
	</head>
	<div id="wrapper">	 
		<a href="<?= ($BASE) ?>/welcome"><img src="<?= ($BASE) ?>/<?= ($UI) ?>images/MP_logo_v2.png" class="img-logo"/></a>
	<div class="col-md-12">	
	<div class="row">		
		<form name="form1" action="<?= ($BASE) ?>/<?= ($loginorout) ?>" method="get"> 
			<button type="submit" class="btn btn-default btn-sm login-style" value = "<?= ($loginorout) ?>">
				<span class="glyphicon glyphicon-log-out"></span><b id = "loginout"><?= ($loginorout) ?></b>
			</button>
		</form>
		<button type="button" class="btn user-style">
			<a href="<?= ($BASE) ?>/<?= ($username) ?>/home"><?= ($username) ?></a>
		</button>
		<form name="form2" action="<?= ($BASE) ?>/signup" method="get" id = "signup"> 
			<button type="button" class="btn btn-default btn-sm login-style" value = "signup">
				<span class="glyphicon glyphicon-log-out"></span>signup
			</button>
			<script type="text/javascript">
				signuphidden();
			</script>
		</form>
		<body>
		  <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
		</body>
	</div>
	</div>
</html>
