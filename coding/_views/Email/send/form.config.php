<?php

$my = kmi_mail::get_produsens(array('ID','name'));
//$cust = kmi_mail::get_customers(array('ID','name'),"LIMIT 20");
$group = kmi_mail::get_groups(array('ID','name'),"LIMIT 20");
$exgroup = kmi_mail::get_exgroups(array('ID','name'),"LIMIT 20");

$cont = kmi_template::get_contents(array('ID','name'));
$sign = kmi_template::get_signatures(array('ID','name'));

$my = convToOption($my,'ID','name');
//$cust = convToOption($cust,'ID','name');
$group = convToOption($group,'ID','name');
$exgroup = convToOption($exgroup,'ID','name');

$cont = convToOption($cont,'ID','name');
$sign = convToOption($sign,'ID','name');

$opt_grp = array(/*'Email' => $cust,*/'Group' => $group,'ExGroup' => $exgroup);

$status = '';
if($data['status']==1){
	$status = 'disabled';
}

$config = array(
	0 => array(
		'func'			=> 'opt_hidden',
		'type'			=> 'hidden',
		'key'			=> 'ID',
		'value'			=> $data['ID']
	),
	array(
		'func'			=> 'opt_select',
		'data'			=> $my,
		'key'			=> 'from_mail',
		'label'			=> 'From',
		'class'			=> 'input-circle',
		'searching'		=> true,
		'select'		=> $data['from_mail'],
		'status'		=> $status
	),
	array(
		'id'			=> 'list_mail',
		'func'			=> 'opt_select',
		'group'			=> true,
		'data'			=> $opt_grp,
		'key'			=> 'to_mail',
		'label'			=> 'To',
		'class'			=> 'input-circle',
		'searching'		=> true,
		'select'		=> $data['to_mail'],
		'status'		=> $status	
	),
	array(
		'func'			=> 'opt_input',
		'type'			=> 'text',
		'key'			=> 'subject_mail',
		'label'			=> 'Subject',
		'class'			=> 'input-circle',
		'value'			=> $data['subject_mail'],
		'data'			=> 'placeholder="Subject" '.$status
	),
	array(
		'func'			=> 'opt_input',
		'type'			=> 'file',
		'key'			=> 'attachment',
		'label'			=> 'Attachment',
		'class'			=> 'input-circle',
		'value'			=> $data['attachment'],
		'data'			=> 'placeholder="Attachment" multiple '.$status
	),
	array(
		'func'			=> 'opt_select',
		'data'			=> $cont,
		'key'			=> 'template',
		'label'			=> 'Content',
		'class'			=> 'input-circle',
		'searching'		=> true,
		'select'		=> $data['template'],
		'status'		=> $status
	),
	array(
		'func'			=> 'opt_select',
		'data'			=> $sign,
		'key'			=> 'footer',
		'label'			=> 'Signature',
		'class'			=> 'input-circle',
		'searching'		=> true,
		'select'		=> $data['footer'],
		'status'		=> $status
	)
);