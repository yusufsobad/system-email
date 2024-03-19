<?php

(!defined('DEFPATH'))?exit:'';

// registry page
$args = array();
$args['login'] = array(
	'home'	=> true,
	'view'	=> 'Login.login',
	'page'	=> 'login_system'
);

$args['administrator'] = array(
	'page'		=> 'admin_kmi',
	'home'		=> false
);

$args['mail'] = array(
	'page'		=> 'blast_email',
	'home'		=> false,
	'view'		=> 'Email.email',	
);

reg_hook('reg_page',$args);