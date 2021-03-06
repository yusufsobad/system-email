<?php
(!defined('DEFPATH'))?exit:'';

function sobad_meta_html(){ 
?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>

<?php
}

function sobad_themes(){
	global $reg_theme;

	if(empty($reg_theme)){
		$reg_theme = theme;
	}

	// definisi path theme
	if(!defined('THEMEPATH')){
		define('THEMEPATH',dirname(__FILE__));
	}

	if(is_dir(THEMEPATH.'/'.$reg_theme)){
		require THEMEPATH.'/'.$reg_theme.'/template.php';
		require THEMEPATH.'/'.$reg_theme.'/view.php';
	}
}

function theme_layout($func='',$data=''){
	if(empty($func)){
		die(_error::_alert_db($func . "::Not Load Layout!!!"));
	}

	$theme = _theme_name;

	if(!is_callable(array($theme,$func))){
		die(_error::_alert_db($theme . "::Not Function Layout!!!"));
	}

	$theme::{$func}($data);
}