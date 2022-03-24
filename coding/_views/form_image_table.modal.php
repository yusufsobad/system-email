<?php

$config = array(
	'title'		=> $title,
	'button'	=> '_btn_modal_save',
	'status'	=> array(
		'link'		=> $link,
		'load'		=> isset($load)?$load:'sobad_portlet',
		'type'		=> isset($type)?$type:'',
	),
	'func'		=> array('_layout_form','_portlet_form'),
	'data'		=> array($data,$table)
);