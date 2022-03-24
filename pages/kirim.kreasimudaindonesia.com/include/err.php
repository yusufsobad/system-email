<?php

class _error{
	public function _page404(){
		header('Location: include/404.php');
	}
	
	public function _page500(){
		header('Location: include/500.php');
	}
	
	public function _connect(){
		$err = $this->_alert_db("server: koneksi gagal");
		die($err);
	}
	
	public function _database(){
		$err = $this->_alert_db("server: database tidak ditemukan");
		die($err);
	}
	
	public function _user_login(){
		$err = $this->_alert_db("Username atau password anda salah");
		die($err);
	}
	
	public function _alert_db($msg,$func=""){
		$ajax = array(
			'status' => "error",
			'msg'	 => $msg,
			'func'	 => $func
		);
		$ajax = json_encode($ajax);
		
		return $ajax;
	}
}