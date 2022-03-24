<?php

class setting_admin extends _page{
	protected static $object = 'setting_admin';

	protected static $table = 'sobad_meta';

	// ----------------------------------------------------------
	// Layout ---------------------------------------------------
	// ----------------------------------------------------------

	protected static function table(){
		$data = array();

		$data['class'] = '';
		$data['table'] = array();

		$args = sobad_meta::_gets('setting',array('ID','meta_value','meta_note','meta_reff'));

		$no = 0;
		foreach($args as $key => $val){
			$no += 1;

			if($val['meta_reff']==1){
				$func = '_stsDisable';
				$color = 'yellow';
				$label = 'disabled';
				$status = 'Enabled';
			}else{
				$func = '_stsEnable';
				$color = 'blue';
				$label = 'enabled';
				$status = 'Disabled';
			}

			$button = array(
				'ID'	=> 'status_'.$val['ID'],
				'func'	=> $func,
				'color'	=> $color,
				'icon'	=> 'fa fa-edit',
				'label'	=> $label,
				'spin'	=> true
			);
			
			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Kode'		=> array(
					'left',
					'10%',
					$val['meta_value'],
					true
				),
				'Name'		=> array(
					'left',
					'auto',
					$val['meta_note'],
					true
				),
				'Status'	=> array(
					'left',
					'10%',
					$status,
					true
				),
				'Button'		=> array(
					'center',
					'10%',
					_click_button($button),
					false
				)
			);
		}
		
		return $data;
	}

	private static function head_title(){
		$args = array(
			'title'	=> 'Setting <small>data setting</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'setting'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Setting',
			'tool'		=> '',
			'action'	=> '',
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

	// ----------------------------------------------------------
	// Function to database -------------------------------------
	// ----------------------------------------------------------

	public static function _stsEnable($id=0){
		return self::_statusChange($id,'1');
	}

	public static function _stsDisable($id=0){
		return self::_statusChange($id,'0');
	}

	public static function _statusChange($id=0,$status='0'){
		$id = str_replace('status_', '', $id);
		intval($id);

		$args = array();
		$args[] = array(
			'name'		=> 'ID',
			'value'		=> sobad_asset::ascii_to_hexa($id)
		);

		$args[] = array(
			'name'		=> 'meta_key',
			'value'		=> sobad_asset::ascii_to_hexa('setting')
		);

		$args[] = array(
			'name'		=> 'meta_reff',
			'value'		=> sobad_asset::ascii_to_hexa($status)
		);

		$json = json_encode($args);
		return self::_update_db($json);
	}
}