<?php

date_default_timezone_set('Asia/Jakarta');
session_start();

if(!isset($_GET['send'])){
	$err = _error::_alert_db("Sending not load");
	die($err);
}else{

	$key = $_GET['object'];
	$key = str_replace("sobad_","",$key);
	$func = str_replace("sobad_","",$_GET['send']);
	
	define('AUTHPATH',$_SERVER['SERVER_NAME']);
	require 'config/hostname.php';

	// Get Define
	new hostname();

	// get file component
	new _component();

	// load route
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
		$ajax = array(
			'status' => "failed",
			'msg'	 => "object not found!!!",
			'func'	 => 'sobad_'.$key
		);
		$ajax = json_encode($ajax);
			
		return print_r($ajax);
	}

	define('_object',$key);
	sobad_cronjob::_get($data);
}

class sobad_cronjob{
	public static function _get($args=array()){
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

		$data = explode('-',$data);
		$data[1] = isset($data[1])?$data[1]:1;
		$data[2] = isset($data[2])?$data[2]:0;
		
		try{
			$msg = $_class::{$_func}($data[0],$data[1],$data[2]);
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

		foreach($msg as $key => $val){
			$req = send_mail::sobad_send_mail($val['data']);
			
			if($req===0){
				$ajax_func($val['index'],2,$val['meta_id']);	
			}else{
				$ajax_func($val['index'],3,$val['meta_id']);
			}
		}
	}
}