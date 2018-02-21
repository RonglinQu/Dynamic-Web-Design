<?php

class FlickrAPI {
	
	private $key = "d497fdb7e29545410d6da8acf8dafee0";
	private $number_per_page = 5;
	
	public function __construct() {}
	
	public function makingRequest($tag) {

		$request =  "https://api.flickr.com/services/rest?&method=flickr.photos.search".
					//"&api_key=".$this->key."&tags=".$tag."&extras=url_sq,url_m&per_page=".$this->number_per_page."&format=json&nojsoncallback=1";
					"&api_key=".$this->key."&tags=".$tag."&extras=url_sq,url_m&per_page=".$this->number_per_page;
		return $request;
	}
	
	public function load($request) {
		// for more on cURL see http://php.net/manual/en/book.curl.php and http://blog.unitedheroes.net/curl/
		$crl = curl_init(); // creating a curl object
		$timeout = 10;
		curl_setopt ($crl, CURLOPT_URL,$request);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		$xml_to_parse = curl_exec($crl);
		
		curl_close($crl); // closing the crl object
		
		$parsed_xml = simplexml_load_string($xml_to_parse);
		$items = $parsed_xml->photos->photo; // traversing the xml nodes to count how many photos were retrieved

		//echo htmlspecialchars($xml_to_parse);//show XML

		return $items;
	}
	
	public function getingPictures($items){
		
		$numOfItems = count($items);
		$returnrecord = array();
		if($numOfItems>0){ // yes, some items were retrieved
			foreach($items as $current){
				$record = array();
				$record['img_m'] = "http://farm".$current['farm'].".static.flickr.com/".$current['server']."/".$current['id']."_".$current['secret']."_m.jpg";
				$record['img'] = "http://farm".$current['farm'].".static.flickr.com/".$current['server']."/".$current['id']."_".$current['secret'].".jpg";
				$record['url'] = "http://www.flickr.com/photos/".$current['owner']."/".$current['id'];
				$record['title'] = $current['title'];
				array_push($returnrecord,$record);
			}
		}
		return $returnrecord;
	}
	
}

?>
