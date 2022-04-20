<?php
(!defined('AUTHPATH'))?exit:'';

class sobad_table{

	public static function _get_table($func){
		$func = str_replace('-','_',$func);
				
		$obj = new self();
		if(is_callable(array($obj,$func))){
			$list = $obj::{$func}();
				return $list;
			}
		
		return false;
	}
		
	public static function _get_list($func=''){
		$list = array();
		$lists = self::_get_table($func);
		if($lists){
			foreach ($lists as $key => $val) {
				$list[] = $key;
			}
		}
		
		return $list;
	}
		

	private static function _list_table(){
		// Information data table
		
		$table = array(
				'email-group-meta'		=> self::email_group_meta(),
				'email-link'		=> self::email_link(),
				'email-list'		=> self::email_list(),
				'email-log'		=> self::email_log(),
				'email-log-meta'		=> self::email_log_meta(),
				'email-module'		=> self::email_module(),
				'email-option'		=> self::email_option(),
				'email-temp'		=> self::email_temp(),
				'email-template'		=> self::email_template(),
				'email-type'		=> self::email_type(),
				'email-user'		=> self::email_user(),
		);
		
		return $table;
	}
		

		private static function email_group_meta(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',
				'user'	=> 0,	
			);
			
			return $list;
		}

		private static function email_link(){
			$list = array(
				'link_meta'	=> 0,
				'href'	=> '',
				'status'	=> 0,
				'link_date'	=> date('Y-m-d H:i:s'),	
			);
			
			return $list;
		}

		private static function email_list(){
			$list = array(
				'name'	=> '',
				'email'	=> '',
				'place'	=> '',
				'type'	=> 0,
				'user'	=> 0,
				'note'	=> '',	
			);
			
			return $list;
		}

		private static function email_log(){
			$list = array(
				'name'	=> '',
				'from_mail'	=> 0,
				'to_mail'	=> 0,
				'type'	=> 0,
				'subject_mail'	=> '',
				'attachment'	=> '',
				'template'	=> 0,
				'footer'	=> 0,
				'date'	=> date('Y-m-d H:i:s'),
				'status'	=> 0,
				'trash'	=> 0,
				'user'	=> 0,	
			);
			
			return $list;
		}

		private static function email_log_meta(){
			$list = array(
				'meta_id'	=> 0,
				'meta_mail'	=> 0,
				'meta_date'	=> date('Y-m-d H:i:s'),
				'read_date'	=> date('Y-m-d H:i:s'),
				'link_date'	=> date('Y-m-d H:i:s'),
				'status'	=> 0,
				'log'	=> 0,
				'user'	=> 0,	
			);
			
			return $list;
		}

		private static function email_module(){
			$list = array(
				'user_id'	=> 0,
				'status'	=> '',	
			);
			
			return $list;
		}

		private static function email_option(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',	
			);
			
			return $list;
		}

		private static function email_temp(){
			$list = array(
				'meta_table'	=> '',
				'meta_key'	=> '',
				'meta_value'	=> '',	
			);
			
			return $list;
		}

		private static function email_template(){
			$list = array(
				'name'	=> '',
				'lokasi'	=> '',
				'date'	=> date('Y-m-d'),
				'reff'	=> 0,
				'type'	=> 0,
				'locked'	=> 0,
				'user'	=> 0,	
			);
			
			return $list;
		}

		private static function email_type(){
			$list = array(
				'name'	=> '',	
			);
			
			return $list;
		}

		private static function email_user(){
			$list = array(
				'user'	=> '',
				'pass'	=> '',
				'name'	=> '',	
			);
			
			return $list;
		}

}