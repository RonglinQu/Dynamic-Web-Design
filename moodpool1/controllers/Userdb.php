<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.
// this php is modified from simpleController.php in SimpleExample app

class Userdb {
	private $mapper;
	
	public function __construct() {
		global $f3;						// needed for $f3->get() 
		$this->mapper = new DB\SQL\Mapper($f3->get('DB'),"users");	// create DB query mapper object
																			// for the "users" table
	}
	
	public function putIntoDatabase($data) {
		if ($this->mapper->count(array('username=?',$data["username"]))){
			return 1;
		}
		$this->mapper->username = $data["username"];					// set value for "name" field
		$this->mapper->password = md5($data["password"]);				// set value for "password" field
		$this->mapper->save();		// save new record with these fields
		return 0;
	}
	
	public function checkUserInfo($data) {
		if (!$this->mapper->count(array('username=?',$data["username"]))){
			return 1;
		}else if (!$this->mapper->count(array('username=? AND password=?',$data["username"],md5($data["password"])))){
			return 2;
		}
		
		return 0;
	}
	public function getData() {
		$list = $this->mapper->find();
		return $list;
	}
	
	public function deleteFromDatabase($idToDelete) {
		$this->mapper->load(['id=?', $idToDelete]);				// load DB record matching the given ID
		$this->mapper->erase();									// delete the DB record
	}
	
	public function getUserId($username){
	    $id = $this->mapper->load(['username=?',$username]);
		return $id['userid']; 
	}
}

?>
