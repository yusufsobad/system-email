<?php

date_default_timezone_set('Asia/Jakarta');
session_start();
require '../function.php';

if(!isset($_GET['send'])){
	$err = new _error();
	$err = $err->_alert_db("Sending not load");
	die($err);
}else{
    if(!isset($_SESSION['kmi_language'])){
    	$_SESSION['kmi_language'] = 'id_ID';
    }
    
    define('kmi_language',$_SESSION['kmi_language']);
    
	$key = str_replace("sobad_","",$_GET['send']);

	sobad_pages("../pages/");

	$data = isset($_GET['data']) ? $_GET['data'] : "";
	
	$ajax['func'] = $key;
	$ajax['data'] = $data;
	sobad_email($ajax);
}

function sobad_email($args=array()){
	$ajax_func = $args['func'];
	$data = $args['data'];
	$err = new _error();
	
	if(!is_callable($ajax_func)){
		$msg = $err->_alert_db("request not found!!!");
		die($msg);
	}

	$data = explode('-',$data);
	$data[1] = isset($data[1])?$data[1]:1;
	$data[2] = isset($data[2])?$data[2]:0;
	$msg = $ajax_func($data[0],$data[1],$data[2]); // id,type,meta_id

	if(empty($msg)){
		$msg = $err->_alert_db("ada kesalahan pada pemrosesan data!!!");
		die($msg);
	}
	
	foreach($msg as $key => $val){
		$req = sobad_send_mail($val['data']);
		
		if($req===0){
			$ajax_func($val['index'],2,$val['meta_id']);	
		}else{
			$ajax_func($val['index'],3,$val['meta_id']);
		}
	}
}