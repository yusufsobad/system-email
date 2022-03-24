<?php
class conn extends _error{
	public function connect(){
		$server = "localhost";
		$user = "bungasid_sendmail";
		$pass = "UPq~N9}W[)+h";
		$database = "bungasid_mail700";

		$conn=new mysqli($server,$user,$pass,$database);
		mysqli_connect($server,$user,$pass) or $this->_connect();
		$conn->select_db($database) or $this->_database();
		
		return $conn;
	}
}
?>