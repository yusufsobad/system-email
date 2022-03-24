<?php

$email = self::_getSidemenu('child_menu.email');

$config = array();
$config['dashboard'] = array(
	'status'	=> 'active',
	'icon'		=> 'icon-home',
	'label'		=> 'Dashboard',
	'func'		=> 'dash_mail',
	'loc'		=> 'Email.view',
	'child'		=> null
);

$config['mail'] = array(
	'status'	=> '',
	'icon'		=> 'fa fa-dashboard',
	'label'		=> 'Email',
	'func'		=> '#',
	'child'		=> $email
);

$config['about'] = array(
	'status'	=> '',
	'icon'		=> 'fa fa-dashboard',
	'label'		=> 'About',
	'func'		=> '',
	'child'		=> null
);