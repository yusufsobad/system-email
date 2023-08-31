<?php
//(!defined('AUTHPATH'))?exit:'';

class conn extends _error{
	public static function connect(){
		global $DB_NAME;
		$database = $DB_NAME;
		$database .= development == 3 ? '_demo' : '';

		$server = SERVER;
		$user = USERNAME;
		$pass = PASSWORD;

		$conn=new mysqli($server,$user,$pass,$database);
		mysqli_connect($server,$user,$pass) or parent::_connect();
		$conn->select_db($database) or parent::_database();

		return $conn;
	}
}
?>