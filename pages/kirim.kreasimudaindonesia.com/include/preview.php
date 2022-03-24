<?php
date_default_timezone_set('Asia/Jakarta');
session_start();

if(!isset($_GET['page'])){
	$err = new _error();
	$err->_page500();
}else{
	define('kmi_language','id_ID');
	$key = str_replace("sobad_","",$_GET['page']);

	require '../function.php';
	sobad_pages("../pages/");

	$data = isset($_GET['data']) ? $_GET['data'] : "";
	
	$preview['func'] = $key;
	$preview['data'] = $data;
	sobad_preview($preview);
}

function sobad_preview($args){
	$preview_func = $args['func'];
	$data = $args['data'];

	if(!is_callable($preview_func)){
		include '404.php';
	}
	
	$msg = $preview_func($data);

	if(empty($msg)){
		include '500.php';
	}
	
	echo $msg;
}