<?php

class kmi_user{
	private static $url = 'https://s.soloabadi.com/system-absen/include/curl.php';

	private static function send_curl($data=array()){
		$url = self::$url;

		$data = sobad_curl::get_data(self::$url,$data);
		$data = json_decode($data,true);

		if($data['status']=='error'){
			die(_error::_alert_db($data['msg']));
		}

		return $data['msg'];
	}

	public static function get_id($id,$args=array(),$limit='',$type=''){
		$data = array(
			'object'	=> 'sobad_user',
			'func'		=> 'get_id',
			'data'		=> array($id,$args,$limit,$type)
		);

		return self::send_curl($data);
	}

	public static function get_all($args=array(),$limit='',$type=''){
		$data = array(
			'object'	=> 'sobad_user',
			'func'		=> 'get_all',
			'data'		=> array($args,$limit,$type)
		);

		return self::send_curl($data);
	}

	public static function get_count($limit='1=1 ',$args=array(),$type=''){
		$data = array(
			'object'	=> 'sobad_user',
			'func'		=> 'count',
			'data'		=> array($limit,$args,$type)
		);

		return self::send_curl($data);
	}

	public static function check_login($user='',$pass=''){
		$data = array(
			'object'	=> 'sobad_user',
			'func'		=> 'check_login',
			'data'		=> array($user,$pass)
		);

		$data = self::send_curl($data);
		$check = array_filter($data);
		if(empty($check)){
			return $data;
		}

		$user = $data[0]['ID'];

		//Check module -> departement
		$return = array();
		$module = kmi_module::get_all(array('status','admin'),"AND user_id='$user'");
		$check = array_filter($module);
		if(!empty($check) && $module[0]['status']==1){
			$data[0]['dept'] = 'mail';
			$data[0]['admin'] = $module[0]['admin'];
			return $data;
		}

		die(_error::_alert_db('Anda tidak punya Akses !!!'));
	}

	public static function get_sales($args=array(),$limit=''){
		$where = "AND divisi='8' $limit";
		return self::get_all($args,$where);
	}
}