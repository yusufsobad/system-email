<?php

function portlet_admin($opt = array(),$args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$title = isset($opt['title'])?$opt['title']:'';
	$data = array();
	
	$data[] = array(
		'style'		=> isset($opt['style'])?$opt['style']:'',
		'script'	=> isset($opt['script'])?$opt['script']:'',
		'func'		=> 'sobad_portlet',
		'data'		=> $args
	);
	
	ob_start();
	sobad_head_content($title);
	sobad_content('sobad_panel',$data);
	return ob_get_clean();
}

function tabs_admin($opt = array(),$args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$title = isset($opt['title'])?$opt['title']:'';
	$data = array();
	
	$data[] = array(
		'style'		=> isset($opt['style'])?$opt['style']:'',
		'script'	=> isset($opt['script'])?$opt['script']:'',
		'func'		=> 'sobad_tabs',
		'data'		=> $args
	);
	
	ob_start();
	sobad_head_content($title);
	sobad_content('sobad_panel',$data);
	return ob_get_clean();
}

function modal_admin($args = array()){	
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	ob_start();
	sobad_content('_modal_content',$args);
	return ob_get_clean();
}

function table_admin($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	ob_start();
	sobad_content('sobad_table',$args);
	return ob_get_clean();
}