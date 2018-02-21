<?php

class Mooddb {
	private $mapper;
	
	public function __construct() {
		global $f3;						// needed for $f3->get() 
		$this->mapper = new DB\SQL\Mapper($f3->get('DB'),"moodtype");	// create DB query mapper object
																			// for the "moodtype" table
	}
	
	public function getMood() {
		$list = $this->mapper->find();
		return $list;
	}
	
	public function checkMood($mood) {
		$temp = trim(strtolower($mood));
		if($this->mapper->count(array('description=?',$temp))==0){
			return 1;
		}else{
			return 0;
		}
	}
}

?>
