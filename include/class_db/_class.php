<?php

abstract class _class{

	private static $_database = '';

	private static $_join = array();
	
	private static $_inner = '';

	private static $_where = '';

	private static $_meta = false;

	private static $_data_meta = array();

	protected static $_type = '';

	protected static $_temp = false;

	protected static $_temp_table = '';

	protected static $_base = '';

	private static function schema($key=''){
		$args = static::blueprint(self::$_type);

		if(!empty($key)){
			return $args = isset($args[$key])?$args[$key]:array();
		}

		return $args;
	}

	public static function _list(){
		$list = sobad_table::_get_list(static::$table);
		$list[] = 'ID';

		return $list;
	}

	private static function list_join(){
		$list = array();
		if(!empty(static::$tbl_join)){
			$list = sobad_table::_get_list(static::$tbl_join);
			$list[] = 'id_join';
		}

		return $list;
	}

	public static function list_meta($type=''){
		self::$_type = $type;
		$list = array();
		if(property_exists(new static,'list_meta')){
			static::set_listmeta();
			$list = static::$list_meta;
		}
		return $list;
	}

	protected static function _check_array($args=array(),$func='_list'){
		$check = array_filter($args);
		if(empty($check)){
			$args = self::{$func}();
		}

		return $args;
	}

	public static function sum($column='',$limit='1=1 ',$args=array(),$type=''){
		return self::condition_sql('sum',$column,$limit,$args,$type);
	}

	public static function avg($column='',$limit='1=1 ',$args=array(),$type=''){
		return self::condition_sql('avg',$column,$limit,$args,$type);
	}

	public static function condition_sql($condition,$column='',$limit='1=1 ',$args=array(),$type=''){
		self::$_meta = false;
		self::$_temp = false;
		self::$_type = $type;

		$inner = '';$meta = false;
		$limit = empty($limit)?"1=1 ":$limit;

		$blueprint = self::schema();		
		$table = $blueprint['table'];

		// Check Detail
		if(isset($blueprint['detail'])){
			$check = array_filter($blueprint['detail']);
			if(!empty($check)){
				self::_detail($args,$table,$blueprint['detail']);
				$inner .= self::$_inner;
				self::$_inner = '';
			}
		}

		// Check Join
		if(isset($blueprint['joined'])){
			$check = array_filter($blueprint['joined']);
			if(!empty($check)){
				$list_join = self::list_join();

				$check = false;
				foreach ($args as $key => $val) {
					if(in_array($val,$list_join)){
						$check = true;
						break;
					}
				}

				if($check){
					self::_joined($args,$table,$blueprint['joined']);
					$inner .= self::$_inner;
					self::$_inner = '';
				}
			}
		}

		$c_sql = strtoupper($condition);$c_alias = strtolower($condition);
		$_args = array("$c_sql(`$table`.$column) AS $c_alias");
		$count = self::_get_data($inner." WHERE ".$limit,$_args);

		self::$_meta = false;
		return $count[0][$c_alias];
	}	

	public static function count($limit='1=1 ',$args=array(),$type=''){
		self::$_meta = false;
		self::$_temp = false;
		self::$_type = $type;

		$inner = '';$meta = false;
		$limit = empty($limit)?"1=1 ":$limit;

		$blueprint = self::schema();		
		$table = $blueprint['table'];

		// Check Temporary
		if(isset($blueprint['temporary']) && self::$_temp){
			$temp = $blueprint['temporary'];
			$temp_table = "temp-" . $temp[$type]['temp'];
			self::$_temp_table = $temp_table;

			$inner .= "LEFT JOIN `" . $table . "` ON `" . $temp_table . "`.reff_temp = `" . $table . "`.ID ";
		}

		// Check Detail
		if(isset($blueprint['detail'])){
			$check = array_filter($blueprint['detail']);
			if(!empty($check)){
				self::_detail($args,$table,$blueprint['detail']);
				$inner .= self::$_inner;
				self::$_inner = '';
			}
		}

		// Check Join
		if(isset($blueprint['joined'])){
			$check = array_filter($blueprint['joined']);
			if(!empty($check)){
				$list_join = self::list_join();

				$check = false;
				foreach ($args as $key => $val) {
					if(in_array($val,$list_join)){
						$check = true;
						break;
					}
				}

				if($check){
					self::_joined($args,$table,$blueprint['joined']);
					$inner .= self::$_inner;
					self::$_inner = '';
				}
			}
		}

		$_args = array("COUNT('`$table`.ID') AS count");
		$check = array_filter(self::list_meta($type));
		if(!empty($check)){
			$_args = array("`$table`.ID");

			$inner .= "LEFT JOIN `".static::$tbl_meta."` ON `".static::$table."`.ID = `".static::$tbl_meta."`.meta_id ";
			$limit .= static::$group;
			self::$_meta = true;
		}

		$count = self::_get_data($inner." WHERE ".$limit,$_args);
		
		if(self::$_meta){
			return count($count);
		}

		self::$_meta = false;
		return $count[0]['count'];
	}
	
	public static function get_id($id,$args=array(),$limit='',$type=''){
		self::$_meta = false;
		self::$_temp = false;

		$where = "WHERE `".static::$table."`.ID='$id' $limit";
		return self::_check_join($where,$args,$type);
	}

	public static function get_all($args=array(),$limit='',$type=''){
		self::$_meta = false;
		self::$_temp = true;

		$check = substr($limit,0,4);
		$check = trim($check);

		$limit = strtoupper($check)=="AND"?substr($limit, 4):$limit;

		$limit = empty($limit)?'1=1':$limit;
		$where = "WHERE $limit";

		return self::_check_join($where,$args,$type);
	}

	public static function check_meta($id=0,$key='',$limit=''){
		$inner = "LEFT JOIN `".static::$tbl_meta."` ON `".static::$table."`.ID = `".static::$tbl_meta."`.meta_id ";;
		$where = $inner."WHERE meta_id='$id' AND meta_key='$key' $limit";

		return self::_get_data($where,array('`'.static::$tbl_meta.'`.ID'));
	}

	// -----------------------------------------------------------------
	// --- Function Check Join -----------------------------------------
	// -----------------------------------------------------------------	

	protected static function _filter_by_blueprint($where='',$args=array(),$type=''){
		$user = self::_list();
		
		self::$_type = $type;
		self::$_join = array();
		self::$_inner = '';
		self::$_where = $where;

		$blueprint = self::schema();
		$table = $blueprint['table'];

		$check = array_filter($args);
		if(empty($args)){
			$joins = self::list_join();
			$metas = self::list_meta($type);

			$args = array_merge($user,$joins,$metas);
		}

		// Check Temporary
		if(isset($blueprint['temporary']) && self::$_temp){
			$temp = $blueprint['temporary'];
			if(isset($temp[$type])){
				$temp_table = "temp-" . $temp[$type]['temp'];
				self::$_temp_table = $temp_table;

				$dbase = !empty(self::$_database) ? '`' . self::$_database . '`.' : '';
				self::$_inner .= "LEFT JOIN " . $dbase . "`" . $table . "` ON `" . $temp_table . "`.reff_temp = `" . $table . "`.ID ";
			}else{
				self::$_temp = false;
			}
		}
	
		if(isset($blueprint['detail'])){
			$check = array_filter($blueprint['detail']);
			if(!empty($check)){
				self::_detail($args,$table,$blueprint['detail']);
			}
		}

		if(isset($blueprint['other'])){
			$check = array_filter($blueprint['other']);
			if(!empty($check)){
				self::_other($args,$table,$blueprint['other']);
			}
		}

		if(isset($blueprint['joined'])){
			$check = array_filter($blueprint['joined']);
			if(!empty($check)){
				self::_joined($args,$table,$blueprint['joined']);
			}
		}

		if(isset($blueprint['meta'])){
			$check = array_filter($blueprint['meta']);
			if(!empty($check)){
				$args = self::_meta($args,$type);
			}
		}

		$j_logs='';
		foreach ($args as $key => $val) {
			if(in_array($val, $user)){
				self::$_join[] = "`".static::$table."`.$val";
			}
		}

		$check = array_filter(self::$_join);
		if(!empty($check)){
			$args = self::$_join;
		}

		$where = self::$_inner.self::$_where;
		self::$_inner = '';self::$_where = '';

		return [
			'where'		=> $where,
			'column'	=> $args
		];

		// $where = self::$_inner.self::$_where;
		// self::$_inner = '';self::$_where = '';
		// $data_join = self::_get_data($where,$args);

		// self::$_meta = false;
		// return $data_join;
	}

	protected static function _check_join($where='',$args=array(),$type=''){
		$filter = self::_filter_by_blueprint($where,$args,$type);
		$data_join = self::_get_data($filter['where'],$filter['column']);

		self::$_meta = false;
		return $data_join;
	}

	private static function _detail($args=array(),$table='',$detail=''){

		foreach($detail as $_key => $val){
			if($args==='*' || in_array($_key,$args)){
				$alias = isset($val['alias']) ? $val['alias'] : '';
				$key = !empty($alias) ? "_" . $alias : "_" . $_key;
				
				foreach($val['column'] as $ky => $vl){
					self::$_join[] = "$key.$vl AS ".$vl."_".substr($key,1,4);
				}
				
				$database = isset($val['database']) ? $val['database'] . '.' : '';
				$database = !empty(self::$_database) ? '`' . self::$_database . '`.' : $database;

				$tbl = $val['table'];
				$col = $val['key'];

				$tbl = !empty($database)?$database.'`'.$tbl.'`':'`'.$tbl.'`';
				self::$_inner .= "LEFT JOIN $tbl AS $key ON `$table`.$_key = $key.$col ";
				
				if(isset($val['detail'])){
					$_detail = $val['detail'];
					self::_detail($val['column'],$key,$_detail);
				}
				
				if(isset($val['joined'])){
					$_joined = $val['joined'];
					$_args = $_joined['column'];
					self::_joined($_args,$key,$_joined);
				}
			}
		}
	}

	private static function _other($args=array(),$table='',$other=''){

		foreach($other as $_key => $val){
			$alias = isset($val['alias']) ? $val['alias'] : '';
			$key = !empty($alias) ? "_" . $alias : "_" . $val['key'];
			
			foreach($val['column'] as $ky => $vl){
				self::$_join[] = "$key.$vl AS ".$vl."_".substr($key,1,4);
			}
			
			$database = isset($val['database'])?$val['database'] . '.' : '';
			$database = !empty(self::$_database) ? '`' . self::$_database . '`.' : $database;

			$tbl = $val['table'];
			$col = $val['key'];

			$tbl = !empty($database)?$database.'`'.$tbl.'`':'`'.$tbl.'`';
			self::$_inner .= "LEFT JOIN $tbl AS $key ON `$table`.ID = $key.$col ";
			
			if(isset($val['detail'])){
				$_detail = $val['detail'];
				self::_detail($val['column'],$key,$_detail);
			}
			
			if(isset($val['joined'])){
				$_joined = $val['joined'];
				$_args = $_joined['column'];
				self::_joined($_args,$key,$_joined);
			}
		}
	}
	
	private static function _joined($args=array(),$table='',$joined=''){

		$lst = isset($joined['column'])?$joined['column']:self::list_join();
		$tbl = $joined['table'];
		$col = $joined['key'];

		$dbase = !empty(self::$_database) ? '`' . self::$_database . '`.' : '';
	
		$inner = '';
		foreach($args as $key => $val){
			if(in_array($val,$lst)){
				if($val=='id_join'){
					self::$_join[] = "`$tbl`.ID AS id_join";
				}else{
					self::$_join[] = "`$tbl`.$val";
				}
				
				$inner = "LEFT JOIN $dbase`$tbl` ON $dbase`$table`.ID = $dbase`$tbl`.$col ";
			}
		}

		self::$_inner .= $inner;
		
		if(isset($joined['detail'])){
			$_detail = $joined['detail'];
			self::_detail($args,$tbl,$_detail);
		}

	}

	private static function _meta($args=array(),$type=''){
		$where = self::$_where;
		$inner = '';$group = $where;
		$meta = self::list_meta($type);
		//$select = "SUM(IF(`".static::$tbl_meta."`.meta_key = '{{key}}',`".static::$tbl_meta."`.meta_value,'')) AS {{key}}";
		$select = "max(case when `".static::$tbl_meta."`.meta_key = '{{key}}' then `".static::$tbl_meta."`.meta_value end) '{{key}}'";

		foreach ($args as $key => $val) {
			if(in_array($val, $meta)){
				$dbase = !empty(self::$_database) ? '`' . self::$_database . '`.' : '';

				self::$_join[] = str_replace('{{key}}', $val, $select);
				$inner = "LEFT JOIN " . $dbase . "`".static::$tbl_meta."` ON " . $dbase . "`".static::$table."`.ID = " . $dbase . "`".static::$tbl_meta."`.meta_id ";

				$group_by = static::$group;
				if(strpos($group, "ORDER BY") !== false){
					$group = str_replace("ORDER BY",$group_by." ORDER BY",$where);
				}else if(strpos($group, "LIMIT") !== false){
					$group = str_replace("LIMIT",$group_by." LIMIT",$where);
				}else{
					$group = $where.$group_by;
				}
			}
		}

		self::$_where = $group;
		self::$_inner .= $inner;

		return $args;
	}

	protected static function _get_data($where='',$args=array()){
		global $DB_NAME;

		$data = array();
		$ids = array();

		$_database = $DB_NAME;
		if(property_exists(new static,'database')){
			$DB_NAME = static::$database;
		}

		$table = !empty(self::$_temp_table) && self::$_temp ?self::$_temp_table : static::$table;
		$q = sobad_db::_select_table($where,$table,$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){

				$data[] = $r;//$item;
			}
		}

		self::$_temp_table = '';
		$DB_NAME = $_database;
		return $data;
	}

	public static function _get_union($data=array(),$type_union=false,$filter=''){
		global $DB_NAME;

		$union = [];

		$_database = $DB_NAME;
		if(property_exists(new static,'database')){
			$DB_NAME = static::$database;
		}

		/*
			untuk configurasi $data sama dengan get all
			example : 
				$data = [
					[
						blueprint 	=> static::class  // optional
						column  	=> [], // default
						database 	=> ''  // optional
						where 		=> '', // optional
						type 		=> ''  // optional
						base 		=> ''  // optional
					],
					[
						blueprint 	=> static::class  // optional
						column  	=> [], // default
						database 	=> ''  // optional
						where 		=> '', // optional
						type 		=> ''  // optional
						base 		=> ''  // optional
					],
					...
				]

			untuk $type_union, false => UNION dan true => UNION ALL
		*/

		$select = [];
		foreach ($data as $key => $val) {
			$blueprint = isset($val['blueprint']) ? $val['blueprint'] : static::class;
			$column = isset($val['column']) ? $val['column'] : [];

			$database = isset($val['database']) ? $val['database'] : $DB_NAME;
			self::$_database = $database;

			$base = isset($val['base']) ? $val['base'] : base;
			$blueprint::$_base = $base;
			new $blueprint();

			$limit = isset($val['where']) ? $val['where'] : '';
			$type = isset($val['type']) ? $val['type'] : '';

			$check = substr($limit,0,4);
			$check = trim($check);

			$limit = strtoupper($check)=="AND"?substr($limit, 4):$limit;

			$limit = empty($limit)?'1=1':$limit;
			$where = "WHERE $limit";

			$_filter = $blueprint::_filter_by_blueprint($where,$column,$type);

			$select[] = [
				'database'	=> $database,
				'table'		=> $blueprint::$table,
				'column'	=> $_filter['column'],
				'where'		=> $_filter['where']
			];
		}

		self::$_database = '';

		$q = sobad_db::_union_table($select,$type_union,$filter);
		if($q!==0){
			while($r=$q->fetch_assoc()){

				$union[] = $r;//$item;
			}
		}

		$DB_NAME = $_database;
		return $union;
	}
}