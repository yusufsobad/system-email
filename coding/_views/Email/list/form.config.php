<?php

$config = array(
	'cols'	=> array(2,9),
	0 => array(
		'func'			=> 'opt_hidden',
		'type'			=> 'hidden',
		'key'			=> 'ID',
		'value'			=> $data['ID']
	),
	array(
		'func'			=> 'opt_hidden',
		'type'			=> 'hidden',
		'key'			=> 'type',
		'value'			=> $data['type']
	),
	array(
		'func'			=> 'opt_input',
		'type'			=> 'text',
		'key'			=> 'name',
		'label'			=> 'Nama',
		'class'			=> 'input-circle',
		'value'			=> $data['name'],
		'data'			=> 'placeholder="Nama"'
	),
	array(
		'func'			=> 'opt_input',
		'type'			=> 'text',
		'key'			=> 'email',
		'label'			=> 'Email',
		'class'			=> 'input-circle',
		'value'			=> $data['email'],
		'data'			=> 'placeholder="Email"'
	)
);

if($type=='customer'){
	$config[] = array(
		'func'			=> 'opt_input',
		'type'			=> 'text',
		'key'			=> 'place',
		'label'			=> 'Place / Institusi',
		'class'			=> 'input-circle',
		'value'			=> $data['place'],
		'data'			=> 'placeholder="Tempat"'
	);
}

if($type=='produsen'){
	$secure = array(
		1	=> 'ssl',
		2	=> 'tls'
	);
	
	$config[] = array(
		'func'			=> 'opt_input',
		'type'			=> 'password',
		'key'			=> 'mail_pass',
		'label'			=> 'Password',
		'class'			=> 'input-circle',
		'value'			=>  kmi_decrypt($data['mail_pass']),
		'data'			=> ''
	);
	$config[] = array(
		'func'			=> 'opt_input',
		'type'			=> 'text',
		'key'			=> 'mail_host',
		'label'			=> 'Hostname',
		'class'			=> 'input-circle',
		'value'			=> $data['mail_host'],
		'data'			=> ''
	);
	$config[] = array(
		'func'			=> 'opt_select',
		'data'			=> $secure,
		'key'			=> 'mail_secure',
		'label'			=> 'Encrypt',
		'class'			=> 'input-circle',
		'select'		=> $data['mail_secure']
	);
}

if($type=='group'){
	// unset input email
	unset($config[2]);
	$email[0] = 'Tidak Ada';

	// get list email customer
//	$mail = kmi_mail::get_customers(array('ID','name','email'));
//	foreach($mail as $key => $val){
//		$email[$val['ID']] = $val['name'].' => '.$val['email'];
//	}

	$config[] = array(
		'func'			=> 'opt_textarea',
		'key'			=> 'note',
		'label'			=> 'Description',
		'class'			=> 'input-circle',
		'value'			=> $data['note'],
		'rows'			=> 3
	);
	
//	$config[] = array(
//		'func'			=> 'opt_select',
//		'data'			=> $email,
//		'key'			=> 'meta_value',
//		'label'			=> 'Email',
//		'class'			=> 'input-circle bs-select input-large',
//		'select'		=> $data['meta_value'],
//		'status'		=> 'data-live-search="true" data-size="6" data-style="blue" multiple'
//	);
}