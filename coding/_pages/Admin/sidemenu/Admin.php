<?php

function sidemenu_admin(){
	$args = array();
	
	$args['module'] = array(
		'status'	=> 'active',
		'icon'		=> 'fa fa-group',
		'label'		=> 'Module',
		'func'		=> 'module_admin',
		'child'		=> null
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