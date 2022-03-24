<?php
require dirname(__FILE__)."/list_table.php";

class kmi_db extends conn{
	
	public function _table_db($table){
		$class = new kmi_table();
		$list = $class->_get_table($table);
		
		if($list == 0){
			$alert = $this->_alert_db("Table Tidak ditemukan!!!");
			die($alert);
		}
		
		return $list;
	}
	
	private function _def_table($table){
		$table = $this->_table_db($table);
		$this->_check_array($table);
		
		$data = array();
		foreach($table as $key => $val){
			$data[] = $key;
		}
		
		return implode(",",$data);
	}
	
	private function _check_array($args = array()){
		$alert = $this->_alert_db("Permintaan kosong!!!");
		$check = array_filter($args);
		
		if(empty($check)){
			die($alert);
		}
	}
	
	public function _select_table($where,$table,$args = array()){
		$conn = $this->connect();
		$alert = $this->_alert_db("pengambilan data gagal!!!");
		
		$this->_check_array($args);
		if(empty($table)){die("");}
		
		$args = implode(",",$args);
		$query = sprintf("SELECT %s FROM `%s` %s",$args,$table,$where);

		$q = $conn->query($query)or die($alert);
		if($q->num_rows<1){
			return 0;
		}
		
		return $q;
	}
	
	public function _insert_table($table,$args = array()){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal membuat data baru!!!");
		
		$this->_check_array($args);
		if(empty($table)){die("");}
		
		$def = $this->_table_db($table);
		$args = array_replace($def,$args);
		foreach($args as $key => $val){
			$tbl[] = $key;
			$val = $conn->real_escape_string($val);
			$data[] = "'$val'";
		}
		
		$tbl = implode(",",$tbl);
		$data = implode(",",$data);
	
		$query = sprintf("INSERT INTO `%s`(%s) VALUES(%s)",$table,$tbl,$data);
		$conn->query($query)or die($alert);
		
		return self::_max_table($table);
	}
	
	public function _max_table($table){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal menghitung table!!!");
		if(empty($table)){die("");}

		$query = sprintf("SELECT MAX(ID) AS ID from `%s`",$table);
		$q=$conn->query($query) or die($alert);
		if($q->num_rows>0){
			$r=$q->fetch_assoc();
			return $r['ID'];
		}
	}
	
	public function _update_table($id,$table,$args = array()){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal memperbarui data");
		
		$this->_check_array($args);
		if(empty($table)){die("");}
		if(empty($id)){die("");}
		
		foreach($args as $key => $val){
			$val = $conn->real_escape_string($val);
			$value = "$key='$val'";
			$data[] = $value;
		}
		
		$data = implode(",",$data);
		$query = sprintf("UPDATE `%s` SET %s WHERE ID='%s'",$table,$data,$id);
		$conn->query($query)or die($alert);
		
		return 1;
	}
	
	public function _update_multiple($where='',$table,$args = array()){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal memperbarui data");
		
		$this->_check_array($args);
		if(empty($table)){die("");}
		if(empty($where)){die("");}
		
		foreach($args as $key => $val){
			$val = $conn->real_escape_string($val);
			$value = "$key='$val'";
			$data[] = $value;
		}
		
		$data = implode(",",$data);
		$query = sprintf("UPDATE `%s` SET %s WHERE %s",$table,$data,$where);		
		$conn->query($query)or die($alert);
		
		return 1;
	}
	
	public function _delete_single($id,$table){
		$alert = $this->_alert_db("index table undefined!!!");
		if(empty($id)){die($alert);}
		
		$query = "WHERE ID='$id'";
		$q = $this->_delete_table($query,$table);

		return $q;
	}
	
	public function _delete_multiple($where,$table){
		$alert = $this->_alert_db("index table undefined!!!");
		if(empty($where)){die($alert);}
		
		$query = "WHERE $where";
		$q = $this->_delete_table($query,$table);

		return $q;
	}
	
	public function _drop_table_tmp(){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal mengkosongkan data!!!");
		
		$table = 'sdn-tmp';
		
		$query = sprintf("TRUNCATE `%s`",$table);
		$conn->query($query)or die($alert);
		
		return 1;
	}
	
	public function _copy_data_table($kepada=array(),$dari=array()){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal meng-copy data baru!!!");
		
		$this->_check_array($kepada);
		$this->_check_array($dari);
		
		$to = isset($kepada['table'])?$kepada['table']:die('');
		if(isset($kepada['colom'])){
			$args1 = implode(',',$kepada['colom']);
		}else{
			die('');
		}
		
		$from = isset($dari['table'])?$dari['table']:die('');
		if(isset($dari['colom'])){
			$args2 = implode(',',$dari['colom']);
		}else{
			die('');
		}
		
		$where = isset($dari['where'])?$dari['where']:die('');
		
		$query = sprintf("INSERT INTO `%s`(%s) SELECT %s FROM `%s` %s",$to,$args1,$args2,$from,$where);
	
		$conn->query($query)or die($alert);
		
		return self::_max_table($to);
	}
	
	private function _delete_table($query,$table){
		$conn = $this->connect();
		$alert = $this->_alert_db("Gagal menghapus data!!!");
		
		if(empty($table)){die("");}
		if(empty($query)){die("");}
		
		$query = sprintf("DELETE FROM `%s` %s",$table,$query);
		$conn->query($query)or die($alert);
		
		return 1;
	}
	
}