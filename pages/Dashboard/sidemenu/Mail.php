<?php

function reg_sidemenu(){
	$args = array();
	$args['dashboard'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Dashboard',
		'func'		=> 'dash_admin',
		'child'		=> null
	);
	
	$args['mail'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'Email',
		'func'		=> '#',
		'child'		=> menu_email()
	);
	
	$args['about'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'About',
		'func'		=> '',
		'child'		=> null
	);
	
	return $args;
}

function menu_email(){
	$args = array();
	$args['email'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Contacts',
		'func'		=> 'daftar_mail',
		'child'		=> NULL
	);
	
	$args['template'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Contents',
		'func'		=> 'contents_mail',
		'child'		=> NULL
	);

	$args['signature'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Signature',
		'func'		=> 'signatures_mail',
		'child'		=> NULL
	);
	
	$args['send'] = array(
		'status'	=> '',
		'icon'		=> '',
		'label'		=> 'Sends & Histories',
		'func'		=> 'send_mail',
		'child'		=> NULL
	);
	
	return $args;
}