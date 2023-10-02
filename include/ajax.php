<?php 
//	ini_set('display_errors', 1);
//	ini_set('display_startup_errors', 1);
//	error_reporting(E_ALL);

session_start();

if(!isset($_POST['ajax'])){
	include 'err.php';

	$err = new _error();
	$err = $err->_alert_db("ajax not load");
	die($err);
}else{
	$key = $_POST['object'];
	$key = str_replace("sobad_","",$key);
	$func = str_replace("sobad_","",$_POST['ajax']);

	define('AUTHPATH',$_SERVER['SERVER_NAME']);
	require 'config/hostname.php';

	// Get Define
	new hostname();

	// get file component
	new _component();

	// get url
	get_uri();

	// load route
	$asset = sobad_asset::_pages("../coding/_pages/");

	// include pages
	load_first_page($key);

	// get Themes
	sobad_themes();

	if(!class_exists($key)){
		$key = get_home_func($key);
	}

	$value = isset($_POST['data']) ? $_POST['data'] : "";

	$data['class'] = $key;
	$data['func'] = $func;
	$data['data'] = $value;

	if(!class_exists($key)){
		$ajax = array(
			'status' => "failed",
			'msg'	 => "object not found!!!",
			'func'	 => 'sobad_'.$key
		);
		$ajax = json_encode($ajax);
			
		return print_r($ajax);
	}

	define('_object',$key);
	sobad_ajax::_get($data);
}

class sobad_ajax{
	public static function _get($args=array()){
		$develop = true;
		$start = self::_get_microtime();

		$check = array_filter($args);
		if(empty($check)){
			$ajax = array(
				'status' => "error",
				'msg'	 => "data not found!!!",
				'func'	 => ''
			);
			$ajax = json_encode($ajax);
			
			return print_r($ajax);
		}

		$_class = $args['class'];
		$_func = $args['func'];
		$data = $args['data'];

		if(!is_callable(array($_class,$_func))){
			$ajax = array(
				'status' => "failed",
				'msg'	 => "request not found!!!",
				'func'	 => 'sobad_'.$_func
			);
			$ajax = json_encode($ajax);
			
			return print_r($ajax);
		}
		
		try{
			$msg = $_class::{$_func}($data);
		}catch(Exception $e){
			return _error::_alert_db($e->getMessage());
		}

		if(empty($msg)){
			$ajax = array(
				'status' => "error",
				'msg'	 => "ada kesalahan pada pemrosesan data!!!",
				'func'	 => 'sobad_'.$_func
			);
			$ajax = json_encode($ajax);
			
			return print_r($ajax);
		}
		
		$finish = self::_set_microtime($start);

		$ajax = array(
			'status' => "success",
			'msg'    => "success",
			'data'	 => $msg,
			'func'	 => 'sobad_'.$_func
		);

		if($develop) $ajax['time'] = $finish;
		
		$ajax = json_encode($ajax);		
		return print_r($ajax);
	}

	public static function _get_microtime(){
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];

		return $time;
	}

	public static function _set_microtime($start=0){
		$finish = self::_get_microtime();
		$time = round(($finish - $start), 4);

		return $time;
	}
}