<?php

class module_admin extends _page{
	protected static $object = 'module_admin';

	protected static $table = 'kmi_module';

	// ----------------------------------------------------------
	// Layout ---------------------------------------------------
	// ----------------------------------------------------------

	protected static function _array(){
		$args = array(
			'ID',
			'user_id',
			'status',
			'admin'
		);

		return $args;
	}

	protected static function table(){
		$data = array();
		$args = self::_array();

		$module = kmi_module::get_all($args);

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach($module as $key => $val){
			$no += 1;
			$edit = array(
				'ID'	=> 'edit_'.$val['ID'],
				'func'	=> '_edit',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'edit'
			);

			$hapus = array(
				'ID'	=> 'del_'.$val['ID'],
				'func'	=> '_delete',
				'color'	=> 'red',
				'icon'	=> 'fa fa-trash',
				'label'	=> 'hapus'
			);

			$name = '-';

			$user = kmi_user::get_id($val['user_id'],array('name'));
			$check = array_filter($user);
			if(!empty($check)){
				$name = $user[0]['name'];
			}

			$status = $val['status']==1?'Enabled':'Disabled';
			
			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'		=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Nama'		=> array(
					'left',
					'auto',
					$name,
					true
				),
				'Status'	=> array(
					'left',
					'10%',
					$status,
					true
				),
				'Edit'			=> array(
					'center',
					'10%',
					edit_button($edit),
					false
				),
				'Hapus'			=> array(
					'center',
					'10%',
					hapus_button($hapus),
					false
				)
			);
		}
		
		return $data;
	}

	private static function head_title(){
		$args = array(
			'title'	=> 'Module <small>data module</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'module'
				)
			),
			'date'	=> false
		); 
		
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data Akses',
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

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	public static function add_form(){
		$vals = array(0,0,1,0);
		$vals = array_combine(self::_array(), $vals);

		$args = array(
			'title'		=> 'Tambah akses',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	protected static function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$args = array(
			'title'		=> 'Edit akses',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet'
			)
		);
		
		return self::_data_form($args,$vals);
	}

	private static function _data_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$user = kmi_user::get_all(array('ID','name'),"AND status NOT IN ('0','7') ");	
		$user = convToOption($user,'ID','name');

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			array(
				'func'			=> 'opt_select',
				'data'			=> $user,
				'key'			=> 'user_id',
				'label'			=> 'Karyawan',
				'class'			=> 'input-circle',
				'select'		=> $vals['user_id'],
				'searching'		=> true,
				'status'		=> ''
			),
			array(
				'func'			=> 'opt_box',
				'type'			=> 'radio',
				'key'			=> 'status',
				'label'			=> 'Status',
				'inline'		=> true,
				'value'			=> $vals['status'],
				'data'			=> array(
					0	=> array(
						'title'		=> 'Disabled',
						'value'		=> 0
					),
					1	=> array(
						'title'		=> 'Enabled',
						'value'		=> 1
					),
				)
			),
			array(
				'func'			=> 'opt_box',
				'type'			=> 'radio',
				'key'			=> 'admin',
				'label'			=> 'Admin',
				'inline'		=> true,
				'value'			=> $vals['admin'],
				'data'			=> array(
					0	=> array(
						'title'		=> 'No',
						'value'		=> 0
					),
					1	=> array(
						'title'		=> 'Yes',
						'value'		=> 1
					),
				)
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

}