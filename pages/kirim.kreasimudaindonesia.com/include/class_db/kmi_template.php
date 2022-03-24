<?php

class kmi_template extends kmi_db{
	private function list_template(){
		$list = array(
			'ID',
			'name',
			'lokasi',
			'date',
			'reff'
		);
		
		return $list;
	}

// ---------------------------------	
// GET Template LIST ---------------
	public function get_template($id=0,$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_template();
		}
		
		$where = "WHERE ID='$id'";
		return $this->_get_template($where,$args);
	}
	
	public function _get_templates($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_template();
		}
		
		$id_user = $_SESSION['kmi_ID'];
		$where = "WHERE user IN ('$id_user','0') $limit";
		return $this->_get_template($where,$args);
	}
	
	public function get_contents($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_template();
		}
		
		$id_user = $_SESSION['kmi_ID'];
		$where = "WHERE type=0 AND user='$id_user' $limit";
		return $this->_get_template($where,$args);
	}
	
	public function get_signatures($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_template();
		}
		
		$id_user = $_SESSION['kmi_ID'];
		$where = "WHERE type=1 AND user='$id_user' $limit";
		return $this->_get_template($where,$args);
	}
	
	private function _get_template($where='',$args=array()){
		$email = array();
		$q = $this->_select_table($where,'email-template',$args);
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
}