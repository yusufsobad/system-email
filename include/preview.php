<?php

session_start();

if(!isset($_GET['page'])){
	include 'err.php';

	$err = new _error();
	$err = $err->_alert_db("ajax not load");
	die($err);
}else{
	$key = isset($_GET['object'])?$_GET['object']:$_COOKIE['sidemenu'];
	$key = str_replace("sobad_","",$key);
	$func = str_replace("sobad_","",$_GET['page']);

	define('AUTHPATH',$_SERVER['SERVER_NAME']);
	require 'config/hostname.php';

	// Get Define
	new hostname();

	// get file component
	new _component();

	// include pages
	$asset = sobad_asset::_pages("../coding/_pages/");

	// include pages
	load_first_page($key);

	// get Themes
	sobad_themes();

	if(!class_exists($key)){
		$key = get_home_func($key);
	}

	$value = isset($_GET['data']) ? $_GET['data'] : "";

	$data['class'] = $key;
	$data['func'] = $func;
	$data['data'] = $value;

	if(!class_exists($key)){
		die("Object Not Found!!!");
	}

	sobad_preview::_get($data);
}

class sobad_preview{
	public static function _get($args=array()){
		// new _libs_(array(convToPDF));

		$func = $args['func'];
		$data = $args['data'];
		$_class = $args['class'];

		if(!is_callable(array($_class,$func))){
			include '404.php';
		}
		
		$obj = new $_class();
		$msg = $obj->{$func}($data);

		if(empty($msg)){
			include '500.php';
		}
		
		echo $msg;
	}
}