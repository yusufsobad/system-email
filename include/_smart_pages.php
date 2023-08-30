<?php

abstract class _smart_page extends _page{

	protected static $head_title = '';

	protected static $breadcrumb = '';

	protected static $modal = 2;

	protected static $type_layout = 1; // 0 : basic , 1 : portlet , 2 : tabs

	protected static $active = '';

	protected static $data_tabs = array();

	protected static $label_card = '';

	protected static $action = 'action';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
		if(is_callable(array(new static(), '_array_default'))){
			$array = static::_array_default();
		}else{
			$table = static::$table;
			$array = sobad_table::_get_table($table);
		}

		return array_keys($array);
	}

	protected static function smart_table($where=''){
		$data = array();
		$args = self::_array();
		$type = self::$type;

		$tp = str_replace('tab_', '', $type);
		$tp = intval($tp);
		
		$kata = $_search = '';
		if(parent::$search){
			$src = parent::like_search($args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
			$_search = $src[2];
		}else{
			$cari=$where;
		}

		$args = self::_gets_db($args, $where);

		$data = array(
            'kata'      => $kata,
            'search'    => $_search,
            'data'      => $args,
            'type'		=> $type
        );

		return self::_loadView('table',$data);
	}

	private static function head_title(){
		$args = array(
			'title'	=> static::$head_title,
			'link'	=> array(
				0	=> array(
					'func'	=> static::$object,
					'label'	=> static::$breadcrumb,
				)
			),
			'date'	=> false,
			'modal'	=> static::$modal
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = static::table();
		
		$action = static::$action;
		$action = empty($action) ? '' : static::{$action}(); 
		$box = array(
			'label'		=> static::$label_card,
			'tool'		=> '',
			'action'	=> $action,
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected static function layout(){
		$type = static::$type_layout;
		if($type == 0){
			if(is_callable(array(new static(), 'basic_layout'))){
				return static::basic_layout();
			}
		}else if($type == 1){
			return static::portlet_layout();
		}else if($type == 2){
			return static::tabs_layout();
		}

		return '';
	}

	protected static function portlet_layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(static::$object, '_style'),
			'script'	=> array(static::$object, '_script')
		);
		
		return portlet_admin($opt,$box);
	}

	protected static function tabs_layout(){
		$active = empty(static::$active) ? static::$data_tabs[0] : static::$active;

		self::$type = $active;
        $box = self::get_box();

        $data = static::$data_tabs;

        $object = static::$table;
        $tabs = array();
        foreach ($data as $key => $val) {
            $tabs[$key] = array(
                'key'    => 'tab_' . $key,
                'label'  => $val,
                'qty'    => $object::count("status='$key'")
            );
        }
        $tabs = array(
        	'active'	=> static::$active,
            'tab'    	=> $tabs,
            'func'    	=> '_portlet',
            'data'    	=> $box
        );

        $opt = array(
            'title'        => self::head_title(),
            'style'     => array(static::$object, '_style'),
            'script'    => array(static::$object, '_script')
        );
        return tabs_admin($opt, $tabs);
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------
	public static function add_form(){
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		$data = self::_array_default();

		$config =self::_loadView('form',array('data' => $data));
		$data = array(
			'title'		=> 'Tambah data module',
			'link'		=> '_add_db',
			'data'		=> $config,
			'type'		=> $type
		);

		return sobad_asset::_loadView('form',$data);
	}

	protected static function edit_form($data=array()){
		$type = isset($_POST['type']) ? $_POST['type'] : '';

		$config =self::_loadView('form',array('data' => $data));
		$data = array(
			'title'		=> 'Edit data module',
			'link'		=> '_update_db',
			'data'		=> $config,
			'type'		=> $type
		);

		return sobad_asset::_loadView('form',$data);
	}  	
}