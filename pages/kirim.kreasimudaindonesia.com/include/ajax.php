<?php
// -------------- show error reporting
//	ini_set('display_errors', 1);
//	ini_set('display_startup_errors', 1);
//	error_reporting(E_ALL);
// -------------- end error

date_default_timezone_set('Asia/Jakarta');
session_start();

if(!isset($_POST['ajax'])){
    require 'err.php';
    
	$err = new _error();
	$err = $err->_alert_db("ajax not load");
	die($err);
}else{
	define('kmi_language','id_ID');
	$key = str_replace("sobad_","",$_POST['ajax']);
    
    require '../function.php';
	sobad_pages("../pages/");

	$data = isset($_POST['data']) ? $_POST['data'] : "";
	
	$ajax['func'] = $key;
	$ajax['data'] = $data;
	sobad_ajax($ajax);
}

function sobad_ajax($args){

	$ajax_func = $args['func'];
	$data = $args['data'];

	if(!is_callable($ajax_func)){
		$ajax = array(
			'status' => "gagal",
			'msg'	 => "request not found!!!",
			'func'	 => 'sobad_'.$ajax_func
		);
		$ajax = json_encode($ajax);
		
		return print_r($ajax);
	}
	
	$msg = $ajax_func($data);

	if(empty($msg)){
		$ajax = array(
			'status' => "error",
			'msg'	 => "ada kesalahan pada pemrosesan data!!!",
			'func'	 => 'sobad_'.$ajax_func
		);
		$ajax = json_encode($ajax);
		
		return print_r($ajax);
	}
	
	$ajax = array(
		'status' => "success",
		'msg'	 => $msg,
		'func'	 => 'sobad_'.$ajax_func
	);
	
	$ajax = json_encode($ajax);		
	return print_r($ajax);
}