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
					'column'	=> array('name','email','type')
				),
				'to_mail'	=> array(
					'key'		=> 'ID',
					'table'		=> 'email-list',
					'column'	=> array('name','email','type')
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
					'column'	=> array('name','email','type')
				)
			);
		}

		return $args;
	}
	
	public static function get_log($id=0,$args=array()){
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

	public static function get_log_metas($args=array(),$limit=''){
		self::$table = 'email-log-meta';
		$id_user = get_id_user();

		$where = "`email-log-meta`.user='$id_user' $limit";
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
		$id_user = get_id_user();
		
		$where = "AND status IN ($status) AND `email-log-meta`.user='$id_user' $limit";
		return self::get_log_send($where);
	}
}