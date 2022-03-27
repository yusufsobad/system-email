<?php

class kmi_mail extends _class{

	public static $table = 'email-list';

	public static $tbl_join = 'email-group-meta';

	public static $tbl_meta = 'email-option';

	protected static $join = "joined.meta_id ";

	protected static $group = " GROUP BY `email-option`.meta_id";

	protected static $list_meta = '';

	public static function set_listmeta(){
		$type = parent::$_type;

		switch ($type) {
			case 'produsen':
				self::$list_meta = array(
					'mail_pass',
					'mail_host',
					'mail_secure'
				);
				break;
			
			default:
				self::$list_meta = array();
				break;
		}
	}

	public static function blueprint($key='customer'){
		self::set_listmeta();

		$args = array(
			'type'		=> $key,
			'table'		=> self::$table,
//			'detail'	=> array(
//				'type'	=> array(
//					'key'		=> 'ID',
//					'table'		=> 'email-type',
//					'column'	=> array('name')
//				),
//			),
		);

		if($key=='produsen'){
			$args['meta'] = array(
				'key'		=> 'meta_id',
				'table'		=> self::$tbl_meta,
			);
		}

		if($key=='group'){
			$args['joined'] = array(
				'key'	=> 'meta_id',
				'table'	=> self::$tbl_join 
			);
		}

		return $args;
	}

// ------------------------------	
// GET TABLE TYPE ---------------
	public static function get_types(){
		$args = array(
			1 => 'produsen',
			2 => 'customer',
			3 => 'supplier',
			4 => 'group'
		);

		return $args;
	}

// ------------------------------	
// GET TABLE OPTION	-------------
	public static function get_option($id){
		self::$table = 'email-option';

		$args = array(
			'ID',
			'meta_key',
			'meta_value'
		);
		
		$where = "WHERE meta_id='$id'";		
		$data = self::_get_data($where,$args);

		self::$table = 'email-list';
		return $data;
	}
	
	public static function get_options($where='',$args=array()){
		self::$table = 'email-option';

		$check = array_filter($args);
		if(empty($check)){
			$args = array(
				'ID',
				'meta_key',
				'meta_value'
			);
		}
		
		$data = self::_get_data($where,$args);

		self::$table = 'email-list';
		return $data;
	}

// ------------------------------	
// GET TABLE LIST ---------------
	public static function get_email($id=0,$args=array()){		
		return self::get_id($id,$args);
	}
	
	public static function get_email_custom($where='',$args=array()){
		return self::get_all($args,$where);
	}
	
	public static function get_group($id=0,$args=array(),$where=''){
		return self::get_id($id,$args,$where,'group');
	}

	public static function get_exgroup($id=0,$args=array()){
		$where = "`email-list`.ID NOT IN ('0','$id')";
		return self::get_id($id,$args,$where,'group');
	}
	
	public static function get_customers($args=array(),$limit=''){
		$id_user = get_id_user();

		$where = "`email-list`.type='2' AND `email-list`.user='$id_user' $limit";
		return self::get_all($args,$where);
	}
	
	public static function get_produsens($args=array(),$limit=''){
		$id_user = get_id_user();

		$where = "`email-list`.type='1' AND `email-list`.user='$id_user' $limit";
		return self::get_all($args,$where,'produsen');
	}

	public static function get_groups($args=array(),$limit=''){
		$id_user = get_id_user();

		$where = "`email-list`.type='4' AND `email-list`.user='$id_user' $limit";
		return self::get_all($args,$where,'group');
	}

	public static function get_exgroups($args=array(),$limit=''){
		$id_user = get_id_user();

		$where = "`email-list`.type='4' AND `email-list`.user NOT IN ('0','$id_user') $limit";
		return self::get_all($args,$where,'group');
	}	
}