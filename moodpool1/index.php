<?php

  /////////////////////////////////////
 // index.php for moodpool website //
/////////////////////////////////////

// Create f3 object then set various global properties of it
// These are available to the routing code below, but also to any 
// classes defined in autoloaded definitions
// the following code is modified from SimpleExample app

$f3 = require('lib/base.php');

// autoload Controller class(es) and anything hidden above web root, e.g. DB stuff
$f3->set('AUTOLOAD','controllers/');		

$db = DatabaseConnection::connect();		// defined as autoloaded class in AboveWebRoot/autoload/
$f3->set('DB', $db);

$f3->set('DEBUG',3);		// set maximum debug level
$f3->set('UI','ui/');		// folder for View templates
$f3->set('UPLOADS','data/image/'); //folder for uploaded images
session_start();

  /////////////////////////////////////////////
 // moodpool website routings //
/////////////////////////////////////////////

//home page (index.html) -- actually just shows form entry page with a different title
$f3->redirect('GET /','/welcome');
$f3->route('GET /welcome',
	function($f3){
		$f3->set('html_title','welcome');
		$f3->set("content","welcome.html");
		$md = new Mooddb;
		$moodtags = $md->getMood();
		$f3->set('moodtags', $moodtags);
		if(empty($_SESSION['userinfo']['username'])){
			$f3->set('loginorout','login');
		}else{
			$f3->set('username',$_SESSION['userinfo']['username']);
			$f3->set('loginorout','logout');
		}
		echo template::instance()->render('layout.html');
	}
);
$f3->route('GET /signup',
	function($f3) {
		$f3->set('html_title','signup');
		$f3->set('content','form.html');
		$f3->set('action','signup');
		$f3->set('loginorout','login');
		echo template::instance()->render('layout.html');
	}
);
$f3->route('POST /signup',
	function($f3){
		$formdata = array();			// array to pass on the entered data in
		$formdata["username"] = $f3->get('POST.username');		// whatever was called "username" on the form
		$formdata["password"] = $f3->get('POST.password');		// whatever was called "password" on the form
			
		$controller = new Userdb;
		$result = $controller->putIntoDatabase($formdata);
		$userid = $controller->getUserId($formdata["username"]);

		$f3->set('html_title','response');
		$f3->set('content','response.html');
		if($result){
			$f3->set('result','this username exists, please enter a new username');
			$f3->set('loginorout','login');
		}else{
			$f3->set('result','success');
			$f3->set('loginorout','logout');
			$_SESSION['userinfo'] = array(
				'username' => $formdata["username"],
				'userid' => $userid
			);
		}

		echo template::instance()->render('layout.html');
	}
);
$f3->route('GET /login',
	function($f3) {
		$f3->set('html_title','login');
		$f3->set('content','form.html');
		$f3->set('action','login');
		$f3->set('loginorout','login');
		echo template::instance()->render('layout.html');
	}
);

$f3->route('POST /login',
	function($f3){
		$formdata = array();			// array to pass on the entered data in
		$formdata["username"] = $f3->get('POST.username');		// whatever was called "username" on the form
		$formdata["password"] = $f3->get('POST.password');		// whatever was called "password" on the form
			
		$controller = new Userdb;
		$result = $controller->checkUserInfo($formdata);
		$userid = $controller->getUserId($formdata["username"]);

		$f3->set('html_title','response');
		$f3->set('content','response.html');
		switch ($result) {
			case 0:
				$f3->set('result','Success');
				$f3->set('loginorout','logout');
				$_SESSION['userinfo'] = array(
					'username' => $formdata["username"],
					'userid' => $userid
				);
				break;
			case 1:
				$f3->set('result','The username does not exist. Please check your username');
				$f3->set('loginorout','login');
				break;
			case 2:
				$f3->set('result','The password is wrong.');
				$f3->set('loginorout','login');
				break;
		}
		echo template::instance()->render('layout.html');
	}
);

$f3->route('GET /logout',
	function($f3) {
		session_destroy();
		$f3->reroute('/welcome');
	}
);

$f3->route('GET /@username/home',
	function($f3) {
		$f3->set('html_title','MyHome');
		$f3->set('content','profile.html');
		$f3->set('loginorout','logout');
		$f3->set('username',$_SESSION['userinfo']['username']);
		$is = new ImageServer;
		$info = $is->getUpload(0);
		$f3->set('datalist', $info);
		$f3->set('recommendation', 'viewimages.html');
		$likedimages = $is->getLiked(0);
		$f3->set('likedimages',$likedimages);
		//var_dump($likedimages);
		echo template::instance()->render('layout.html');
	}
);

$f3->route('POST|GET /upload',
	function($f3) {
		$f3->set('html_title','upload');
		$f3->set('content','upload.html');
		$f3->set('loginorout','logout');
		$f3->set('username',$_SESSION['userinfo']['username']);
		$md = new Mooddb;
		$datalist = $md->getMood();
		$f3->set('datalist', $datalist);
		echo template::instance()->render('layout.html');
	}
);

$f3->route('POST /uploadImage',
	function($f3) {
		$is = new ImageServer;
		if ($filedata = $is->upload()) {						// if this is null, upload failed	
			$f3->set('filedata', $filedata);
		
			$f3->set('html_title','Image Server Home');
			$f3->set('content','uploaded.html');
			$f3->set('loginorout','logout');
			$f3->set('username',$_SESSION['userinfo']['username']);
			echo template::instance()->render('layout.html');
		}
	}
);

$f3->route('GET /viewimages',
  function($f3) {
  	$is = new ImageServer;
    $uploadimages = $is->getUpload(0);
    $f3->set('datalist', $uploadimages);
	$likedimages = $is->getLiked(0);
	$f3->set('likedimages',$likedimages);
	$f3->set('content', 'viewimages.html');
	$f3->set('loginorout','logout');
	$f3->set('username',$_SESSION['userinfo']['username']);
	echo template::instance()->render('layout.html');    
  }
);

// For GET delete requests, we show the viewimages page again, now without the deleted image
$f3->route('GET /delete/@id',
  function($f3) {
	$is = new ImageServer;
	$is->deleteService($f3->get('PARAMS.id'));
	$f3->set('username',$_SESSION['userinfo']['username']);
	$f3->reroute('/'.$_SESSION['userinfo']['username'].'/home');
  }
);

// For POST delete requests (presumably AJAX), we do not output any page content
$f3->route('POST /delete/@id',
  function($f3) {
	$is = new ImageServer;
	$is->deleteService($f3->get('PARAMS.id'));
  }
);

$f3->route('GET /search/@tag',
  function($f3){

	$md = new Mooddb();
	$moodtag = $f3->get('PARAMS.tag');
	if($md->checkMood($moodtag)){
		echo "The mood entered does not exist, please choose a new one";
	}else{
		$fapi = new FlickrAPI;
		$request = $fapi->makingRequest($moodtag);
		$items = $fapi->load($request);
		$link_list = $fapi->getingPictures($items);
		$is = new ImageServer();
		for($i=0;$i<count($link_list);$i++){
			$mediaid = $is->checkLiked($link_list[$i]['img']);
			if(!empty($mediaid)){
				$link_list[$i]['liked']=true;
				$link_list[$i]['mediaid'] = $mediaid;
			}else{
				$link_list[$i]['liked']=false;
				$link_list[$i]['mediaid'] = "";
			}
		}
	}
	$moodlist = $md->getMood();
	$f3->set('moodlist',$moodlist);
	$f3->set('content', 'searchresult.html');
	$f3->set('searchresult', $link_list);
	$f3->set('mood',$moodtag);
	if(empty($_SESSION['userinfo']['username'])){
		$f3->set('loginorout','login');
	}else{
		$f3->set('loginorout','logout');
		$f3->set('username',$_SESSION['userinfo']['username']);
	}
	echo template::instance()->render('layout.html'); 
  }
);

$f3->route('POST /search',
  function($f3){
	$moodtag = $f3->get('POST.tag');
	$md = new Mooddb();
	if($md->checkMood($moodtag)){
		echo "The mood entered does not exist, please choose a new one";
	}else{
		$fapi = new FlickrAPI;
		$request = $fapi->makingRequest($moodtag);
		$items = $fapi->load($request);
		$link_list = $fapi->getingPictures($items);
		$is = new ImageServer();
		for($i=0;$i<count($link_list);$i++){
			$mediaid = $is->checkLiked($link_list[$i]['img']);
			if(!empty($mediaid)){
				$link_list[$i]['liked']=true;
				$link_list[$i]['mediaid'] = $mediaid;
			}else{
				$link_list[$i]['liked']=false;
				$link_list[$i]['mediaid'] = "";
			}
		}
	}
	$moodlist = $md->getMood();
	$f3->set('moodlist',$moodlist);
	$f3->set('content', 'searchresult.html');
	$f3->set('searchresult', $link_list);
	$f3->set('mood',$moodtag);
	if(empty($_SESSION['userinfo']['username'])){
		$f3->set('loginorout','login');
	}else{
		$f3->set('loginorout','logout');
		$f3->set('username',$_SESSION['userinfo']['username']);
	}
	echo template::instance()->render('layout.html');
  }
);

$f3->route('POST /like',
	function($f3){
		$response=array();
		if(empty($_SESSION['userinfo']['userid'])){
			$response['result'] = 0;
			$response['message'] = "please login or signup \n<form name=\"form2\" action=\"".
				 $f3->get('BASE')."/login\" method=\"get\">".
				 "<input type=\"submit\" value=\"login\"/></form><form name=\"form2\" action=\"".
				 $f3->get('BASE')."/signup\" method=\"get\">".
				 "<input type=\"submit\" value=\"signup\"/></form>";
		}else{
			$image = json_decode($f3->get('POST.imageinfo'),true);
			$is = new ImageServer();
			$mediaid = $is->addFavorite($image);
			$response['result'] = 1;
			$response['message'] = $mediaid;
		}
		echo json_encode($response);
	}
);

$f3->route('GET /unlike/@mediaid',
	function($f3){
		$is = new ImageServer;
		$is->unlike($f3->get('PARAMS.mediaid'));
		$f3->reroute('/'.$_SESSION['userinfo']['username'].'/home');
	}
);

$f3->route('POST /unlike',
	function($f3){
		$mediaid = json_decode($f3->get('POST.mediaid'),true);
		$is = new ImageServer();
		$is->unlike($mediaid['mediaid']);
		echo 1;
	}
);
  ////////////////////////
 // Run the FFF engine //
////////////////////////

$f3->run();

?>

