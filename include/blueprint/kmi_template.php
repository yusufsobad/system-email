<?php

class kmi_template extends _class{

	public static $table = 'email-template';

	public static function blueprint($key='email'){
		$args = array(
			'type'		=> $key,
			'table'		=> self::$table,
		);

		return $args;
	}

// ---------------------------------	
// GET Template LIST ---------------
	public static function get_template($id=0,$args=array()){
		return self::get_id($id,$args);
	}
	
	public static function _get_templates($args=array(),$limit=''){
		$id_user = get_id_user();
		
		$where = "AND user IN ('$id_user','0') $limit";
		return self::get_all($args,$where);
	}
	
	public static function get_contents($args=array(),$limit=''){
		$id_user = get_id_user();

		$where = "AND type=0 $limit"; // AND user='$id_user'
		return self::get_all($args,$where);
	}
	
	public static function get_signatures($args=array(),$limit=''){
		$id_user = get_id_user();

		$where = "AND type=1 $limit"; // AND user='$id_user'
		return self::get_all($args,$where);
	}

}