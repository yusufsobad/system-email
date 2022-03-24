<?php

class kmi_mail extends kmi_db{
	private function list_email(){
		$list = array(
			'ID',
			'name',
			'email',
			'type',
			'note'
		);
		
		return $list;
	}
	
	private function list_group(){
		$list = array(
			'`email-list`.ID',
			'`email-list`.name',
			'`email-list`.email',
			'`email-list`.note',
			'`email-group-meta`.ID AS id_meta',
			'`email-group-meta`.meta_value AS id_mail',
		);
		
		return $list;
	}

// ------------------------------	
// GET TABLE TYPE ---------------
	public function get_types(){
		$where = "WHERE 1=1";
		return $this->_get_type($where);
	}

// ------------------------------	
// GET TABLE OPTION	-------------
	public function get_option($id){
		$args = array(
			'ID',
			'meta_key',
			'meta_value'
		);
		
		$where = "WHERE meta_id='$id'";
		
		return $this->_get_option($where,$args);
	}
	
	public function get_options($where='',$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$args = array(
				'ID',
				'meta_key',
				'meta_value'
			);
		}
		
		return $this->_get_option($where,$args);
	}

// ------------------------------	
// GET TABLE LIST ---------------
	public function get_email($id=0,$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_email();
		}
		
		foreach($args as $key => $val){
			$args[$key] = "`email-list`.$val";
		}
		
		$where = "WHERE ID='$id'";
		return $this->_get_email($where,$args);
	}
	
	public function get_email_custom($where='',$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_email();
		}
		
		foreach($args as $key => $val){
			$args[$key] = "`email-list`.$val";
		}
		
		$where = "WHERE ".$where;
		return $this->_get_email($where,$args);
	}
	
	public function get_group($id=0,$args=array()){
		$args = $this->list_group();
		
		$inner = "INNER JOIN `email-type` ON `email-list`.type = `email-type`.ID ";
		$inner .= "INNER JOIN `email-group-meta` ON `email-list`.ID = `email-group-meta`.meta_id ";

		$where = $inner." WHERE `email-list`.ID='$id' AND `email-group-meta`.meta_key='mail_group'";
		return $this->_get_email($where,$args);
	}
	
	public function get_customers($args=array(),$limit=''){
		return $this->_set_list_mail($args,$limit);
	}
	
	public function get_produsens($args=array(),$limit=''){
		return $this->_set_list_mail($args,$limit,'produsen');
	}
	
	private function _set_list_mail($args=array(),$limit='',$type='customer'){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_email();
		}
		
		foreach($args as $key => $val){
			$args[$key] = "`email-list`.$val";
		}
		
		$id_user = $_SESSION['kmi_ID'];
		$inner = "INNER JOIN `email-type` ON `email-list`.type = `email-type`.ID";
		$where = $inner." WHERE `email-type`.name='$type' AND `email-list`.user='$id_user' $limit";
		return $this->_get_email($where,$args);
	}
	
	public function get_groups($args=array(),$limit=''){
		$args = $this->list_group();
		
		$inner = "LEFT JOIN `email-type` ON `email-list`.type = `email-type`.ID ";
		$inner .= "LEFT JOIN `email-group-meta` ON `email-list`.ID = `email-group-meta`.meta_id ";
		
		$id_user = $_SESSION['kmi_ID'];
		$where = $inner." WHERE `email-type`.name='group' AND `email-group-meta`.meta_key='mail_group' AND `email-list`.user='$id_user' $limit";
		return $this->_get_email($where,$args);
	}
	
	private function _get_email($where='',$args=array()){
		$email = array();
		$q = $this->_select_table($where,'email-list',$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$email[] = $item;
			}
		}
		
		return $email;
	}
	
	private function _get_type($where){
		$type = array();
		$q = $this->_select_table($where,'email-type',array('ID','name'));
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$type[] = $item;
			}
		}
		
		return $type;
	}
	
	private function _get_option($where,$args=array()){
		$option = array();
		$q = $this->_select_table($where,'email-option',$args);
		
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$option[] = $item;
			}
		}
		
		return $option;
	}
}