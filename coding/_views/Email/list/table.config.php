<?php

$config['data'] = array('data' => $kata, 'value' => $search, 'type' => $type);
$config['search'] = array('Semua', 'nama', 'email','diskripsi');

$config['class'] = '';
$config['table'] = array();
//Pagination
$config['page'] = array(
    'func'    => '_pagination',
    'data'    => array(
        'start'        => $start,
        'qty'        => $sum_data,
        'limit'        => $nLimit,
        'type'        => $type
    )
);

$check = array_filter($data);
if(empty($check)){
	return '';
}

$no = ($start - 1) * $nLimit;
foreach($data as $key => $val){	
	$id_meta = $val['ID'];
	$edit = array(
		'ID'	=> 'edit_'.$val['ID'],
		'func'	=> 'edit_form',
		'color'	=> 'blue',
		'icon'	=> 'fa fa-edit',
		'label'	=> 'edit',
		'type'	=> $type
	);
	
	$hapus = array(
		'ID'	=> 'del_'.$val['ID'],
		'func'	=> '_delete',
		'color'	=> 'red',
		'icon'	=> 'fa fa-trash',
		'label'	=> 'hapus',
		'type'	=> $type
	);
	
	$jml_mail = 0;
	if($type=='mail_4' || $type=='mail_5'){
		$mail_group = kmi_mail::get_id($val['ID'],array('ID','meta_value'),"",'group');
		$mail_group = $mail_group[0];

		$jml_mail = $mail_group['meta_value'];
		$jml_mail = explode(',',$jml_mail);
		$jml_mail = count($jml_mail);
	}

	$status = '';$view = '';
	if($type=='mail_2'){
		$tMail = kmi_send::count_log_metas("meta_mail='$id_meta' AND status IN ('3','4','5')");
		$rMail = kmi_send::count_log_metas("meta_mail='$id_meta' AND status IN ('4','5')");
		$cMail = kmi_send::count_log_metas("meta_mail='$id_meta' AND status = '5'");

		$tMail = '<span class="badge badge-success">'.$tMail.'</span>';
		$rMail = '<span class="badge badge-success" style="background-color:#578ebe;">'.$rMail.'</span>';
		$cMail = '<span class="badge badge-success" style="background-color:#8775a7;">'.$cMail.'</span>';

		$status = $tMail.' '.$rMail.' '.$cMail;

		$view = array(
			'ID'	=> 'view_'.$val['ID'],
			'func'	=> '_view',
			'color'	=> 'yellow',
			'icon'	=> 'fa fa-bar-chart-o',
			'label'	=> 'Statistik',
			'type'	=> $type
		);

		$view = _modal_button($view);
	}
	
	$confid['table'][$key]['tr'] = array();
	$config['table'][$key]['td'] = array(
		'name'			=> array(
			'left',
			'auto',
			$val['name'],
			true
		),
		'description'	=> array(
			'left',
			'20%',
			$val['note'],
			true
		),
		'email'			=> array(
			'left',
			'12%',
			$val['email'],
			true
		),
		'place'		=> array(
			'left',
			'15%',
			$val['place'],
			true
		),
		'status'		=> array(
			'center',
			'12%',
			$status,
			true
		),
		'jumlah'		=> array(
			'center',
			'12%',
			$jml_mail,
			true
		),
		'Edit'			=> array(
			'center',
			'10%',
			edit_button($edit),
			false
		),
		'View'			=> array(
			'center',
			'10%',
			$view,
			false
		),
		'Hapus'			=> array(
			'center',
			'10%',
			hapus_button($hapus),
			false
		)
	);

	if($type=='mail_1' || $type=='mail_4'){
		unset($config['table'][$key]['td']['place']);
	}
	
	if(in_array($type,array('mail_1','mail_2'))){
		unset($config['table'][$key]['td']['jumlah']);
		unset($config['table'][$key]['td']['description']);
	}

	if($type=='mail_5'){
		//unset($config['table'][$key]['td']['Edit']);
		unset($config['table'][$key]['td']['Hapus']);
	}
}
