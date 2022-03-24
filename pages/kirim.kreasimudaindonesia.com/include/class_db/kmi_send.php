<?php

class kmi_send extends kmi_db{
	private function list_log(){
		$list = array(
			'ID',
			'mail_from',
			'mail_to',
			'type',
			'mail_subject',
			'attachment',
			'template',
			'footer',
			'date',
			'name',
			'status'
		);
		
		return $list;
	}
	
	public function get_log($id=0,$args=array()){
		$where = "WHERE `email-log`.ID='$id'";
		return $this->_set_inner_log($where,$args);
	}
	
	public function get_sends($args=array(),$limit){
		$id_user = $_SESSION['kmi_ID'];
		$where = "WHERE `email-log`.user='$id_user' $limit";
		return $this->_set_inner_log($where,$args);
	}

	public function get_log_metas($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			return array();
		}

		$id_user = $_SESSION['kmi_ID'];
		$where = "WHERE user='$id_user' $limit";
		return $this->_get_log($where,$args,'email-log-meta');
	}
	
	public function get_log_send($limit=''){
		$args = array(
				'`email-log-meta`.ID',
				'`email-log-meta`.meta_id',
				'`email-log-meta`.meta_mail',
				'`email-log-meta`.meta_date',
				'`email-log-meta`.read_date',
				'`email-log-meta`.link_date',
				'`email-log-meta`.status',
				'`email-list`.name',
				'`email-list`.email'
			);
		
		$inner = 'INNER JOIN `email-list` ON `email-log-meta`.meta_mail = `email-list`.ID ';
		$where = $inner."WHERE $limit";
		return $this->_get_log($where,$args,'email-log-meta');
	}

	public function get_log_meta($id=0,$limit=''){
		$where = "meta_id='$id' $limit";
		return $this->get_log_send($where);
	}	
	
	public function get_status_mail($status='0',$limit=''){
		$id_user = $_SESSION['kmi_ID'];
		$where = "status IN ($status) AND `email-log-meta`.user='$id_user' $limit";
		return $this->get_log_send($where);
	}
	
	private function _set_inner_log($where,$args){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_log();
		}
		
		foreach($args as $key => $val){
			$args[$key] = "`email-log`.$val";
		}
		
		$inner = '';
		if(in_array("`email-log`.mail_from",$args)){
			array_push($args,"`email-list`.name AS my_name");
			array_push($args,"`email-list`.email AS my_mail");
			
			$inner .= "LEFT JOIN `email-list` ON `email-log`.mail_from = `email-list`.ID ";
		}
		
		if(in_array("`email-log`.mail_to",$args)){
			array_push($args,"mail_to.name AS cust_name");
			array_push($args,"mail_to.email AS cust_mail");
			array_push($args,"mail_to.type AS cust_type");
			
			$inner .= "LEFT JOIN `email-list` AS mail_to ON `email-log`.mail_to = mail_to.ID ";
		}
		
		if(in_array("`email-log`.type",$args)){
			array_push($args,"`email-type`.name AS nm_type");
			
			$inner .= "LEFT JOIN `email-type` ON `email-log`.type = `email-type`.ID ";
		}
		
		if(in_array("`email-log`.template",$args)){
		// get_template	
			array_push($args,"`email-template`.name AS nm_tmplate");
			array_push($args,"`email-template`.lokasi AS url_tmplate");
			
			$inner .= "LEFT JOIN `email-template` ON `email-log`.template = `email-template`.ID ";
		}

		if(in_array("`email-log`.footer",$args)){
		// get_footer	
			array_push($args,"tmpl_foot.name AS nm_footer");
			array_push($args,"tmpl_foot.lokasi AS url_footer");
			
			$inner .= "LEFT JOIN `email-template` AS tmpl_foot ON `email-log`.footer = tmpl_foot.ID ";
		}
	
		$where = $inner.$where;
		return $this->_get_log($where,$args,'email-log');
	}
	
	private function _get_log($where='',$args=array(),$table){
		$log = array();
		$q = $this->_select_table($where,$table,$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$log[] = $item;
			}
		}
		
		return $log;
	}
}