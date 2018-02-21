<?php
// Class that provides methods for working with the images.  The constructor is empty, because
// initialisation isn't needed; in fact it probably never really needs to be instanced and all
// could be done with static methods.
class ImageServer {
	private $filedata;
	private $uploadResult = "Upload failed! (unknown reason) <a href=''>Return</a>";
	private $pictable = "recommendation";
	private $thumbsize = 200;			// max width/height of thumbnail images
// 	private $acceptedTypes = ["image/jpeg", "image/png", "image/gif", "image/tiff", "image/svg+xml"];
	private $acceptedTypes = ["image/jpeg", "image/png", "image/gif"];	// tiff and svg removed: image processing code can't handle them

	public function __construct() {}
	
	// abandoned experiment with a ::instance() method similar to the ones used elsewhere in F3
	// -- it works, but seems to have no advantages
	// 	public static function instance() {
	// 		return new self;
	// 	}

	// Puts the file data into the DB
	public function store() {
		global $f3;			// because we need f3->get()
		$mediatype = new DB\SQL\Mapper($f3->get('DB'),"mediatype");
		$moodtype = new DB\SQL\Mapper($f3->get('DB'),"moodtype");
		$mediatypeid = $mediatype->load(array('description=?',$this->filedata["type"]));
		$pic = new DB\SQL\Mapper($f3->get('DB'),$this->pictable);	// create DB query mapper object
		$pic->recomid = strtoupper(md5(uniqid(rand(),true)));
		$pic->title = $this->filedata["title"];
		$pic->url = $this->filedata["name"];
		$pic->mediatype = $this->filedata["type"];
		$pic->userid = $_SESSION['userinfo']['userid'];
		$pic->uploaddate = date("Ymd");
		$pic->uploadtime = date("H:i:s");
		
		
		$collections = new DB\SQL\Mapper($f3->get('DB'),"mediacollections");
		$collections->mediaid = strtoupper(md5(uniqid(rand(),true)));
		$collections->mediatype = $mediatypeid["typeid"];
		$collections->url = $this->filedata["name"];
		$collections->recommended = true;
		$collections->recomid = $pic->recomid;
		$collections->uploaddate =$pic->uploaddate;
		$collections->uploadtime =$pic->uploadtime; 
		$collections->title = $pic->title;
		
		$pic->save();
		$collections->save();
		
		$tags = new DB\SQL\Mapper($f3->get('DB'),"originalmoods");
		foreach ($this->filedata["moodtags"] as $record) {
			$tags->mediaid = $collections->mediaid;
			$moodtypeid = $moodtype->load(array('description=?',$record));
			$tags->moodtype = $moodtypeid['moodtypeid'];
			$tags->save();
			$tags->reset();
		}
	}

	// Upload file, using callback to get data, then copy data into local array.  
	// Call store() to store data, call createThumbnail(), add thumb name to the
	// array then return the array
	public function upload() {
		global $f3;		// so that we can call functions like $f3->set() from inside here

		$overwrite = false; // set to true, to overwrite an existing file; Default: false
		$slug = true; // rename file to filesystem-friendly version

		Web::instance()->receive(
			function($file,$anything){
				/* looks like:
				  array(5) {
					  ["name"] =>     string(19) "csshat_quittung.png"
					  ["type"] =>     string(9) "image/png"
					  ["tmp_name"] => string(14) "/tmp/php2YS85Q"
					  ["error"] =>    int(0)
					  ["size"] =>     int(172245)
					}
				*/
				// $file['name'] already contains the slugged name now

				$this->filedata = $file;		// export file data to outside this function

				// maybe you want to check the file size
				if($this->filedata['size'] > (2 * 1024 * 1024)) {		// if bigger than 2 MB
					$this->uploadResult = "Upload failed! (File > 2MB)  <a href=''>Return</a>";
					return false; // this file is not valid, return false will skip moving it
				}
				if(!in_array($this->filedata['type'], $this->acceptedTypes)) {		// if not an approved type 
					$this->uploadResult = "Upload failed! (File type not accepted)  <a href=''>Return</a>";
					return false; // this file is not valid, return false will skip moving it
				}
				// everything went fine, hurray!
				$this->uploadResult = "success";
				return true; // allows the file to be moved from php tmp dir to your defined upload dir
			},
			$overwrite,
			$slug
		);
	// 	var_dump($this->filedata);
 
 		if ($this->uploadResult != "success") {
 			echo $this->uploadResult;				// ideally this might be output from index.php
 			return null;
 		}
		
		$mood = $f3->get('POST.mood');
		if (empty($mood)){
			echo "Please select at least one mood tag <a href=''>Return</a>";
			return null;
		}

		$picname = $f3->get('POST.picname');
		$this->filedata["title"] = $picname;		// add the title to filedata for later use
		$this->filedata["moodtags"] = $mood;
		$this->store();
		$this->createThumbnail($this->filedata["name"], $f3->get("UPLOADS") . "/thumb/" .$this->thumbFile($this->filedata["name"]), basename($this->filedata["type"]));
		$this->filedata["thumbNail"] = $this->thumbFile($this->filedata["name"]);		// add the thumbnail to filedata for later use

		return $this->filedata;
	}


	// This just returns all the data we have about images in the DB, just as an array.
	// If given no argument, it uses the default argument, 0, and in this case it returns data about all images.
	// If given an image ID as argument (there can be no image with ID 0), it returns data only about that image.
	public function getUpload($picID=0) {
		global $f3;
		$returnData = array();
		$pic=new DB\SQL\Mapper($f3->get('DB'),$this->pictable);	// create DB query mapper object
		$recommendinfo = new DB\SQL\Mapper($f3->get('DB'),"recommendinfo");
		$list = $pic->find(array('userid=? AND valid=?',$_SESSION['userinfo']['userid'],true));
		if ($picID == 0) {
			foreach ($list as $record) {
				$recordData = array();
				$tags_list = array();
				$tags = array();
				$recordData["title"] = $record["title"];
				$recordData["mediatype"] = $record["mediatype"];
				$recordData["url"] = $record["url"];
				$recordData["recomid"] = $record["recomid"];
				$recordData["thumbNail"] = $f3->get('UPLOADS'). "/thumb/" .$this->thumbFile($record["url"]);
				$tags_list = $recommendinfo->find(array("recomid=?",$record["recomid"]));
				foreach($tags_list as $moodtag){
					array_push($tags,$moodtag["description"]);
				}
				$recordData["tags"] = $tags;
				array_push(	$returnData, $recordData);
			}
			return $returnData;
		}
		$pic->load(array('recomid=? AND userid=? AND valid=?',$picID,$_SESSION['userinfo']['userid'],true));
		$recordData = array();
		$tags_list = array();
		$tags = array();
		$recordData["title"] = $pic["title"];
		$recordData["mediatype"] = $pic["mediatype"];
		$recordData["url"] = $pic["url"];
		$recordData["recomid"] = $pic["recomid"];
		$tags_list = $recommendinfo->find(array("recomid=?",$recordData["recomid"]));
		foreach($tags_list as $moodtag){
			array_push($tags,$moodtag["description"]);
		}
		$recordData["tags"] = $tags;
		return $recordData;
	}
	
	public function getLiked($picID=0) {
		global $f3;
		$returnData = array();
		$likedpic=new DB\SQL\Mapper($f3->get('DB'),"userandmedia");	// create DB query mapper object
		$moodtags = new DB\SQL\Mapper($f3->get('DB'),"personalmood");
		$moodtype = new DB\SQL\Mapper($f3->get('DB'),"moodtype");
		if ($picID == 0) {
			$data_list = $likedpic->find(array('userid=?',$_SESSION['userinfo']['userid']));
			foreach ($data_list as $record) {
				$recordData = array();
				$tags_list = array();
				$tags=array();
				$recordData["title"] = $record["title"];
				$recordData["mediatype"] = $record["mediatype"];
				$recordData["img"] = $record["url"];
				$recordData["url"] = $record["mediasource"];
				$recordData["mediaid"] = $record["mediaid"];
				$suffix = substr(strrchr($recordData["img"], '.'), 1);
				$path = pathinfo($recordData["img"]);
				$recordData["img_m"] = $path['dirname']."/".basename($recordData["img"],".".$suffix)."_m.".$suffix;
				$tags_list = $moodtags->find(array("userid=? AND mediaid=?",$_SESSION['userinfo']['userid'],$record['mediaid']));
				foreach($tags_list as $moodtag){
					$moodtype->load(array('moodtypeid=?',$moodtag["mood"]));
					array_push($tags,$moodtype["description"]);
				}
				$recordData["tags"] = $tags;
				array_push(	$returnData, $recordData);
			}
			return $returnData;
		}
		$likedpic->load(array('mediaid=? AND userid=?',$picID,$_SESSION['userinfo']['userid']));
		$recordData = array();
		$tags_list = array();
		$tags = array();
		$recordData["title"] = $likedpic["title"];
		$recordData["mediatype"] = $likedpic["mediatype"];
		$recordData["img"] = $record["url"];
		$recordData["url"] = $record["mediasource"];
		$recordData["mediaid"] = $record["mediaid"];
		$suffix = substr(strrchr($recordData["img"], '.'), 1);
		$recordData["img_m"] = basename($recordData["img"],".".$suffix)."_m.".$suffix;
		$tags_list = $moodtags->find(array("userid=? AND mediaid=?",$_SESSION['userinfo']['userid'],$record['mediaid']));
		foreach($tags_list as $moodtag){
			$moodtype->load(array('moodtypeid=?',$moodtag["mood"]));
			array_push($tags,$moodtype["description"]);
		}
		$recordData["tags"] = $tags;
		return $recordData;
	}

	// Delete data record about the image, and remove its file and thumbnail file
	public function deleteService($recomID) {
		global $f3;
		$pic=new DB\SQL\Mapper($f3->get('DB'),$this->pictable);	// create DB query mapper object
		$collections = new DB\SQL\Mapper($f3->get('DB'),"mediacollections");
		$tags = new DB\SQL\Mapper($f3->get('DB'),"originalmoods");
		$pic->load(['recomid=?',$recomID]);							// load DB record matching the given ID
		unlink($pic["url"]);										// remove the image file
		unlink($f3->get('UPLOADS')."/thumb/".$this->thumbFile($pic["url"]));	// remove the thumbnail file
		$collections->load(['recomid=?',$recomID]);
		if($collections->count>1){
			$pic->url = $f3->get("UPLOADS")."404.jpg";
			$pic->valid = false;
			$pic->save();
			$collections->url = $f3->get("UPLOADS")."404.jpg";
			$collections->save();
		}else{
			$tags->load(array('mediaid=?',$collections['mediaid']));
			while(!$tags->dry()){
				$tags->erase();
				$tags->next();
			}
			$collections->erase();
			$pic->url = $f3->get("UPLOADS")."404.jpg";
			$pic->valid = false;
			$pic->save();
		}
	}

	public function unlike($mediaid) {
		global $f3;
		$mediacollections = new DB\SQL\Mapper($f3->get('DB'),"mediacollections");
		$collections = new DB\SQL\Mapper($f3->get('DB'),"collections");
		$personaltags = new DB\SQL\Mapper($f3->get('DB'),"personalmood");
		$mediacollections->load(['mediaid=?',$mediaid]);							// load DB record matching the given ID	
		$collections->load(array('mediaid=? AND userid =?',$mediacollections->mediaid,$_SESSION['userinfo']['userid']));
		$personaltags->load(array('mediaid=? AND userid=?',$mediacollections->mediaid,$_SESSION['userinfo']['userid']));
		while(!$personaltags->dry()){
			$personaltags->erase();
			$personaltags->next();
		}
		$collections->erase();
		if($mediacollections->count>1){
			$mediacollections->count--;
			$mediacollections->save();
		}else{
			$originalmoods = new DB\SQL\Mapper($f3->get('DB'),"originalmoods");
			$originalmoods->load(array('mediaid=?',$mediacollections->mediaid));
			while(!$originalmoods->dry()){
				$originalmoods->erase();
				$originalmoods->next();
			}
			$mediacollections->erase();
		}
	}

	// A method that finds the file for a given image ID, and ouputs the raw content of it with a 
	// suitable header, e.g. so that <img src="/image/ID" /> will work.
	// This is necessary because image files are stored above the web root, so have no direct URL.
	public function showImage($picID, $thumb) {
		global $f3;
		$pic=new DB\SQL\Mapper($f3->get('DB'),$this->pictable);	// create DB query mapper object
		$pic->load(['id=?',$picID]);							// load DB record matching the given ID
		$fileToShow = ($thumb?$f3->get('UPLOADS').$this->thumbFile($pic["picfile"]):$pic["picfile"]);
		$fileType = ($thumb?"image/jpeg":$pic["pictype"]);		// thumb is always jpeg
		header("Content-type: " . $fileType);		// write out the image file http header
		readfile($fileToShow);						// write out raw file contents (image data)
	}
	
	
	// Create the name of the thumbnail file for the given image file
	// -- just by adding "thumb-" to the start, but bearing in mind that it
	// will always be a .jpg file.
	private function thumbFile($picfile) {
			return "thumb-".pathinfo($picfile,PATHINFO_FILENAME).".jpg";
	}
	
	// This creates the actual thumbnail by resampling the image file to the size given by the thumbsize variable.
	// We can easily change this.  PHP has very rich image processing functionality; this is a simple example.
    // Based on code from PHP manual for imagecopyresampled()
    // NB this is old code: most of these functions also have F3 wrappers, which might be neater here ...
	private function createThumbnail($filename, $thumbfile, $type) {
	  // Set a maximum height and width
	  $width = $this->thumbsize;
	  $height = $this->thumbsize;
	  
	  // Get new dimensions
	  list($width_orig, $height_orig) = getimagesize($filename);
	  
	  $ratio_orig = $width_orig/$height_orig;
	  
	  if ($width/$height > $ratio_orig) {
		 $width = $height*$ratio_orig;
	  } else {
		 $height = $width/$ratio_orig;
	  }
	  
	  // Resample
	  $image_p = imagecreatetruecolor($width, $height);
	  switch ($type) {
		  case "jpeg":
		  	$image = imagecreatefromjpeg($filename);
		  	break;
		  case "png":
		  	$image = imagecreatefrompng($filename);
		  	break;
		  case "gif":
		  	$image = imagecreatefromgif($filename);
		  	break;
		  default:
		  	$data = file_get_contents($filename);
		  	$image = imagecreatefromstring($data);
	  }
	  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	  
	  // Output
	  // Notice this is always a jpeg image.  We could also have made others, but this seems OK.
	  imagejpeg($image_p, $thumbfile);	
	}
	
	public function addFavorite($image){
		global $f3;
		$mediacollections=new DB\SQL\Mapper($f3->get('DB'),"mediacollections");
		$collections = new DB\SQL\Mapper($f3->get('DB'),"collections");
		$predefined = new DB\SQL\Mapper($f3->get('DB'),"originalmoods");
		$personaldefined = new DB\SQL\Mapper($f3->get('DB'),"personalmood");
		$mediatype = new DB\SQL\Mapper($f3->get('DB'),"mediatype");
		$moodtype = new DB\SQL\Mapper($f3->get('DB'),"moodtype");
		$moodid = $moodtype->load(array('description=?',$image['moodtag']));
		$mediaid = NULL;
		if($this->checkURL($image['img'])){
			$mediacollections->load(array('url=?',$image['img']));
			$mediaid = $mediacollections->mediaid;
			if($collections->count(array('mediaid=? AND userid=?',$mediacollections->mediaid,$_SESSION['userinfo']['userid']))>0){
				if($personaldefined->count(array('userid=? AND mediaid=? AND mood=?',$_SESSION['userinfo']['userid'],$mediacollections->mediaid,$moodid['moodtypeid']))==0){
					$predefined->mediaid = $mediacollections->mediaid;
					$predefined->moodtype = $moodid['moodtypeid'];
					$personaldefined->save();
				}
			}else{
				$collections->userid = $_SESSION['userinfo']['userid'];
				$collections->mediaid = $mediacollections->mediaid;
				$collections->uploaddate = date('Ymd');
				$collections->uploadtime = date("H:i:s");
				$collections->save();
				$mediacollections->count++;
				$mediacollections->save();
			}
			if($predefined->count(array('mediaid=? AND moodtype=?',$mediacollections->mediaid,$moodid['moodtypeid']))==0){
				$predefined->mediaid = $mediacollections->mediaid;
				$predefined->moodtype = $moodid['moodtypeid'];
				$predefined->save();
			}

		}else{
			$mediacollections->mediaid = strtoupper(md5(uniqid(rand(),true)));
			$mediaid = $mediacollections->mediaid;
			$mediatypeid = $mediatype->load(array('description=?',"image/jpeg"));
			$mediacollections->mediatype = $mediatypeid['typeid'];
			$mediacollections->url = $image['img'];
			$mediacollections->recommended = false;
			$mediacollections->uploaddate = date('Ymd');
			$mediacollections->uploadtime = date("H:i:s");
			$mediacollections->mediasource = $image['url'];
			$mediacollections->title = $image['title'];
			
			$collections->userid = $_SESSION['userinfo']['userid'];
			$collections->mediaid = $mediacollections->mediaid;
			$collections->uploaddate = $mediacollections->uploaddate;
			$collections->uploadtime = $mediacollections->uploadtime;
			
			$predefined->mediaid = $mediacollections->mediaid;
			$predefined->moodtype = $moodid['moodtypeid'];
			
			$personaldefined->userid = $collections->userid;
			$personaldefined->mediaid = $collections->mediaid;
			$personaldefined->mood = $predefined->moodtype;
			
			$mediacollections->save();
			$collections->save();
			$predefined->save();
			$personaldefined->save();
		}
		return $mediaid;
	}
	
	public function checkURL($url){
		global $f3;
		$mediacollections=new DB\SQL\Mapper($f3->get('DB'),"mediacollections");
		if($mediacollections->count(array('url=?',$url))==0){
			return 0;
		}else{
			return 1;
		}
	}
	
	
	public function checkLiked($img){
		global $f3;
		$userandmedia=new DB\SQL\Mapper($f3->get('DB'),"userandmedia");
		$userandmedia->load(array('url=? AND userid=?',$img,$_SESSION['userinfo']['userid']));
		if($userandmedia->dry()){
			return ;
		}else{
			return $userandmedia->mediaid;
		}
	}
}
?>
