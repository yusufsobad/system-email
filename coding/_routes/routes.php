<?php

(!defined('DEFPATH'))?exit:'';

// registry page
$args = array();
$args['login'] = array(
	'home'	=> false,
	'view'	=> 'Login.login',
	'page'	=> 'login_system'
);

$args['trial'] = array(
	'home'	=> true,
	'page'	=> 'trial_sasi'
);

reg_hook('reg_page',$args);