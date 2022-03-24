<?php

$portlet = array(
	'ID'		=> isset($ID)?$ID:'',				// ID tag HTML
	'label'		=> isset($label)?$label:'',			// label portlet
	'tool'		=> '',
	'action'	=> isset($action)?$action:'',		// Tombol action
	'func'		=> 'sobad_table',
	'data'		=> $data 							// Config table
);

$config = array(
	'title'		=> $title,
	'button'	=> '',
	'status'	=> array(
		'link'		=> '',
		'load'		=> 'sobad_portlet',
	),
	'func'		=> array('_portlet'),
	'data'		=> array($portlet)
);
