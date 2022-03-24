<?php

$config['data'] = array('data' => $kata, 'value' => $search);
$config['search'] = array('Semua', 'nama');

$config['class'] = '';
$config['table'] = array();
//Pagination
$config['page'] = array(
    'func'    => '_pagination',
    'data'    => array(
        'start'        => $start,
        'qty'        => $sum_data,
        'limit'        => $nLimit
    )
);

$check = array_filter($data);
if(empty($check)){
	return '';
}

$no = ($start - 1) * $nLimit;
foreach($data as $key => $val){	
	$id_meta = $val['ID'];
	$save = array(
		'ID'	=> 'save_'.$id_meta,
		'func'	=> '_save',
		'color'	=> 'green',
		'icon'	=> 'fa fa-save',
		'label'	=> 'Save'
	);

	$edit = array(
		'ID'	=> 'edit_'.$id_meta,
		'func'	=> '_edit',
		'color'	=> 'blue',
		'icon'	=> 'fa fa-edit',
		'label'	=> 'edit',
		'load'	=> 'here_content'
	);

	$status = '';
	if($val['user']==0){
		$status = 'disabled';
		$sts = '<i class="fa fa-circle" style="color:#cb5a5e"></i>';
		$button = hapus_button($save);
	}else{
		$sts = '<i class="fa fa-circle" style="color:#26a69a"></i>';
		$button = _click_button($edit);
	}		
	
	$view = array(
		'ID'	=> 'view_'.$id_meta,
		'func'	=> '_view',
		'color'	=> 'yellow',
		'icon'	=> 'fa fa-eye',
		'label'	=> 'view',
		'status'=> $status,
		'load'	=> 'here_content'
	);
	
	$status = '';
	if($val['locked']==1){
		$status = 'disabled';
	}
	
	$hapus = array(
		'ID'	=> 'del_'.$id_meta,
		'func'	=> '_del_template',
		'color'	=> 'red',
		'icon'	=> 'fa fa-trash',
		'label'	=> 'hapus',
		'status'=> $status,
		'load'	=> 'here_content'
	);
	
	$date = format_date_id($val['date']);
	
	$config['table'][$key]['tr'] = array('');
	$config['table'][$key]['td'] = array(
		'title'		=> array(
			'left',
			'auto',
			$val['name'],
			true
		),
		'tanggal'	=> array(
			'center',
			'25%',
			$date,
			true
		),
		'status'	=> array(
			'center',
			'10%',
			$sts,
			true
		),
		'Edit'			=> array(
			'center',
			'10%',
			$button,
			false
		),
		'view'			=> array(
			'center',
			'10%',
			_click_button($view),
			false
		),
		'hapus'			=> array(
			'center',
			'10%',
			hapus_button($hapus),
			false
		)
		
	);
}
