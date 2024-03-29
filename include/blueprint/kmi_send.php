<?php

class kmi_send extends _class{

	public static $table = 'email-log';

	public static function blueprint($key='email'){
		$args = array(
			'type'		=> $key,
			'table'		=> self::$table,
			'detail'	=> array(
				'from_mail'	=> array(
					'key'		=> 'ID',
					'table'		=> 'email-list',
					'column'	=> array('name','email','place','type')
				),
				'to_mail'	=> array(
					'key'		=> 'ID',
					'table'		=> 'email-list',
					'column'	=> array('name','email','place','type')
				),
				'template'	=> array(
					'key'		=> 'ID',
					'table'		=> 'email-template',
					'column'	=> array('name','lokasi')
				),
				'footer'	=> array(
					'key'		=> 'ID',
					'table'		=> 'email-template',
					'column'	=> array('name','lokasi')
				)
			),
		);

		if($key=='logmeta'){
			$args['detail'] = array(
				'meta_mail'	=> array(
					'key'		=> 'ID',
					'table'		=> 'email-list',
					'column'	=> array('name','email','place','type')
				)
			);
		}

		return $args;
	}
	
	public static function get_log($id=0){
		$args = self::_list();
		return self::get_id($id,$args);
	}
	
	public static function get_sends($args=array(),$limit){
		$id_user = get_id_user();

		$where = "AND `email-log`.user='$id_user' $limit";
		return self::get_all($args,$where);
	}

	public static function get_log_meta($id=0,$limit=''){
		$where = "AND `email-log-meta`.meta_id='$id' $limit";
		return self::get_log_send($where);
	}	

	public static function count_log_metas($limit=''){
		$data = self::get_log_metas(array('ID'),"AND ". $limit);
		return count($data);
	}

	public static function get_log_metas($args=array(),$limit=''){
		self::$table = 'email-log-meta';
		$id_user = isset($_GET['user']) ? $_GET['user'] : get_id_user();

		$user = !empty($id_user)?"`email-log-meta`.user='$id_user' AND":"";

		$where = $user . " `email-log-meta`.log='0' $limit";
		$data = self::get_all($args,$where,'logmeta');

		self::$table = 'email-log';

		return $data;
	}
	
	public static function get_log_send($limit=''){
		$args = array(
			'ID',
			'meta_id',
			'meta_mail',
			'meta_date',
			'read_date',
			'link_date',
			'status',
			'user'
		);
		
		return self::get_log_metas($args,$limit);
	}
	
	public static function get_status_mail($status='0',$limit=''){
		$id_user = isset($_GET['user']) ? $_GET['user'] : get_id_user();
		
		$where = "AND status IN ($status) AND `email-log-meta`.user='$id_user' $limit";
		return self::get_log_send($where);
	}
}