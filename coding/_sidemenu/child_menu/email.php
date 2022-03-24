<?php

$config = array();
$config['contact'] = array(
	'status'	=> '',
	'icon'		=> '',
	'label'		=> 'Contacts',
	'func'		=> 'daftar_mail',
	'loc'		=> 'Email.view',
	'child'		=> NULL
);

$config['content'] = array(
	'status'	=> '',
	'icon'		=> '',
	'label'		=> 'Contents',
	'func'		=> 'contents_mail',
	'loc'		=> 'Email.view',
	'child'		=> NULL
);

$config['signature'] = array(
	'status'	=> '',
	'icon'		=> '',
	'label'		=> 'Signature',
	'func'		=> 'signatures_mail',
	'loc'		=> 'Email.view',
	'child'		=> NULL
);

$config['send'] = array(
	'status'	=> '',
	'icon'		=> '',
	'label'		=> 'Sends and Histories',
	'func'		=> 'send_mail',
	'loc'		=> 'Email.view',
	'child'		=> NULL
);