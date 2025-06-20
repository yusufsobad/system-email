<?php
include 'Template/data_form.php';

class signatures_mail extends template_mail{

	protected static $object = 'signatures_mail';

	protected static $table = 'kmi_template';

	protected static $loc_view = 'Email.template';

	protected static function _array(){
		$args = array(
			'ID',
			'name',
			'lokasi',
			'date',
			'type',
			'user',
			'locked'
		);

		return $args;
	}

	protected static function table(){
		$data = array();
		$args = self::_array();
		$type = self::$type;

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$_search = '';$kata = '';

		$user = get_id_user();
		$where = "AND type='1'"; // AND user='$user'
		if(self::$search){		
			$src = self::like_search($args,$where);
			$cari = $src[0];
            $where = $src[0];
            $kata = $src[1];
            $_search = $src[2];
		}else{
			$cari=$where;
		}
		
		$limit = 'ORDER BY ID DESC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		$sum_data = $object::count("1=1 ".$cari);

		$data = array(
            'kata'      => $kata,
            'search'    => $_search,
            'data'      => $args,
            'start'     => $start,
            'sum_data'  => $sum_data,
            'nLimit'    => $nLimit,
        );

		return self::_loadView('table',$data);
	}

	protected static function head_title(){
		$args = array(
			'title'	=> 'Signature Email<small>signature email</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'signature email',
					'uri'	=> 'signature'
				)
			),
			'date'	=> false
		);
	
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data signature',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected static function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	protected static function action($type=0){	
		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah',
			'load'	=> 'here_content'
		);
		
		return _click_button($add);
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	public static function add_form(){
		$vals = array(0,'','',date('Y-m-d'),1,get_id_user(),0);
		$vals = array_combine(self::_array(), $vals);
		
		return self::_template($vals,0);
	}

	protected static function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		return self::_template($vals,0,true);
	}

	public static function _view($id=0){
		$id = str_replace('view_', '', $id);
		intval($id);

		$object = self::$table;
		$data = $object::get_id($id,self::_array());
		
		return self::_template($data,2);
	}

	public static function _preview($data=array()){
		parent::$data_form = $data;
		return self::view_template();
	}

	public static function _preview_form($data=array(),$type=0){
		parent::$form = true;
		parent::$type_mail = $type;
		parent::$data_form = $data;

		$data = self::_form();
		self::template_form($data);
		self::_script_template();
	}

	// ----------------------------------------------------------
	// Database -------------------------------------------------
	// ----------------------------------------------------------

	public static function _del_template($id=0){
		$id = str_replace('del_', '', $id);

		$q = kmi_template::get_id($id,array('lokasi','locked'));
		$url = self::$url . $q[0]['lokasi'];
		if($q[0]['locked']!=1){
			unlink($url) or $q=0; // hapus file lokasi

			$q = self::_delete($id,false);
		}

		return self::layout();
	}

	public static function _add_template($args=array()){
		$q = self::_add_db($args,'');
		return self::layout();
	}

	public static function _update_template($args=array()){
		$q = self::_update_db($args,'');
		return self::layout();
	}

	public static function _callback($args=array()){
		$args['user'] = get_id_user();

		if(isset($args['lokasi'])){
			$i = 0;
			$args['lokasi'] = 'email/'.$args['name'].'.php';
			
			cek_file:
			$url = self::$url . $args['lokasi'];
			if(is_file($url)){
				$i += 1;
				$args['lokasi'] = 'email/'.$args['name'].'_'.$i.'.php';
				goto cek_file;
			}
			
			$html = sobad_asset::hexa_to_ascii($args['ckeditor']);
			sobad_save_file($url,$html);
		}

		return $args;
	}
}