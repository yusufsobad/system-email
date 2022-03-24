<?php

$config['class'] = '';
$config['table'] = array();

$check = array_filter($data);
if(empty($check)){
	return '';
}

foreach($data as $key => $val){
	$email = array(
		'ID'	=> 'send_'.$val['ID'],
		'func'	=> 'setMail_failMeta',
		'load'	=> 'here_modal',
		'color'	=> 'red',
		'icon'	=> 'fa fa-send-o',
		'label'	=> 'batal',
		'status'=> 'disabled'
	);

	$edit = array(
		'ID'	=> 'edit_'.$val['meta_mail'],
		'func'	=> '_edit_mail',
		'color'	=> 'blue',
		'icon'	=> 'fa fa-edit',
		'label'	=> 'edit',
		'type'	=> $type,
		'spin'	=> false
	);

	$hapus = array(
		'ID'	=> 'del_'.$val['meta_mail'],
		'func'	=> '_delete_detail',
		'load'	=> 'here_modal',
		'color'	=> 'red',
		'icon'	=> 'fa fa-trash',
		'label'	=> 'hapus',
		'type'	=> $type,
	);
	
	if($val['status']!=1){
		$email['func'] = 'setMail_sendMeta';
		$email['color'] = 'green';
		$email['icon'] = 'fa fa-send-o';
		$email['label'] = 'send';
	}
	
	if($val['status']==2){
		$email['status'] = '';
	}
	
	$status = send_mail::_conv_status($val['status']);
	$status = '<i class="fa fa-circle" style="color:'.$status[1].'"></i> '.$status[0];

	$datetime = strtotime($val['meta_date']);
	$date = format_date_id($val['meta_date']);
	$time = date('H:i:s',$datetime);
	$date .= ' '.$time;
	
	$config['table'][$key]['tr'] = array('');
	$config['table'][$key]['td'] = array(
		'no'		=> array(
			'center',
			'5%',
			$key + 1,
			true
		),
		'nama'		=> array(
			'left',
			'auto',
			$val['name_meta'],
			true
		),
		'email'		=> array(
			'center',
			'25%',
			str_replace(';','; ', $val['email_meta']),
			true
		),
		'status'	=> array(
			'center',
			'10%',
			$status,
			true
		),
		'updated'	=> array(
			'center',
			'15%',
			$date,
			true
		),
		'send'	=> array(
			'center',
			'8%',
			_click_button($email),
			false
		),
		'edit'	=> array(
			'center',
			'8%',
			_modal_button($edit,2),
			false
		),
		'hapus'			=> array(
			'center',
			'8%',
			$group?hapus_button($hapus):'',
			false
		)
	);
}