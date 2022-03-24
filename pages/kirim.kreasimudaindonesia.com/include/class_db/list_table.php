<?php

class kmi_table{
	public function _get_table($func){
		$func = str_replace('-','_',$func);
		
		if(is_callable(array($this,$func))){
			$list = $this->{$func}();
			return $list;
		}
		
		return false;
	}
	
	private function _list_table(){
		$table = array(
			'email-group-meta'	=> email_group_meta(),
			'email-list'		=> email_list(),
			'email-log'			=> email_log(),
			'email-log-meta'	=> email_log_meta(),
			'email-option'		=> email_option(),
			'email-type'		=> email_type(),
			'email-temp'		=> email_temp(),
			'email-template'	=> email_template(),
			'email-user'		=> email_user()
		);
		
		return $table;
	}
	
	private function email_group_meta(){
		$list = array(
			'meta_id'		=> 0,
			'meta_key'		=> '',
			'meta_value'	=> '',
			'user'			=> $_SESSION['kmi_ID']
		);
		
		return $list;
	}
	
	private function email_list(){
		$list = array(
			'name'			=> '',
			'email'			=> '',
			'type'			=> 2,
			'user'			=> $_SESSION['kmi_ID'],
			'note'          => ''
		);
		
		return $list;
	}
	
	private function email_log(){
		$list = array(
			'name'			=> '',
			'mail_from'		=> 0,
			'mail_to'		=> 0,
			'type'			=> 0,
			'mail_subject'	=> '',
			'attachment'	=> '',
			'template'		=> 0,
			'footer'		=> 0,
			'date'			=> date('Y-m-d H:i:s'),
			'status'		=> 0,
			'trash'			=> 0,
			'user'			=> $_SESSION['kmi_ID']
		);
		
		return $list;
	}
	
	private function email_log_meta(){
		$list = array(
			'meta_id'			=> 0,
			'meta_mail'			=> 0,
			'meta_date'			=> date('Y-m-d H:i:s'),
			'meta_date'			=> '',
			'meta_date'			=> '',
			'status'			=> 0,
			'log'				=> 0,
			'user'		    	=> $_SESSION['kmi_ID']
		);
		
		return $list;
	}
	
	private function email_option(){
		$list = array(
			'meta_id'		=> 0,
			'meta_key'		=> '',
			'meta_value'	=> ''
		);
		
		return $list;
	}
	
	private function email_temp(){
		$list = array(
			'meta_table'	=> '',
			'meta_key'		=> '',
			'meta_value'	=> ''
		);
		
		return $list;
	}
	
	private function email_template(){
		$list = array(
			'name'			=> '',
			'lokasi'		=> '',
			'date'			=> date('Y-m-d'),
			'reff'			=> 0,
			'type'			=> 0,
			'locked'		=> 0,
			'user'			=> $_SESSION['kmi_ID']
		);
		
		return $list;
	}
	
	private function email_type(){
		$list = array(
			'name'			=> ''
		);
		
		return $list;
	}
	
	private function email_user(){
		$list = array(
			'user'			=> '',
			'pass'			=> md5('1234'),
			'name'			=> ''
		);
		
		return $list;
	}
}