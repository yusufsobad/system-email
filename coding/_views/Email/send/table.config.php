<?php

$config['data'] = array('data' => $kata, 'value' => $search);
$config['search'] = array('Semua', 'nama', 'email','diskripsi');

$config['class'] = '';
$config['table'] = array();
//Pagination
$config['page'] = array(
    'func'    => '_pagination',
    'data'    => array(
        'start'        => $start,
        'qty'        => $sum_data,
        'limit'        => $nLimit
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
		'ID'	=> 'edit_'.$id_meta,
		'func'	=> '_edit',
		'color'	=> 'blue',
		'icon'	=> 'fa fa-edit',
		'label'	=> 'edit'
	);
	
	$hapus = array(
		'ID'	=> 'trash_'.$id_meta,
		'func'	=> '_trash',
		'color'	=> 'red',
		'icon'	=> 'fa fa-trash',
		'label'	=> 'hapus'
	);

	$preview = array(
		'ID'	=> 'preview_'.$id_meta,
		'func'	=> '_preview',
		'color'	=> 'yellow',
		'icon'	=> 'fa fa-eye',
		'label'	=> 'view'
	);
	
	// Check jumlah email
	$tMail = kmi_send::get_log_meta($id_meta);
	$pMail = kmi_send::get_log_meta($id_meta,"AND status IN ('0')");
	$sMail = kmi_send::get_log_meta($id_meta,"AND status IN ('1')");
	$fMail = kmi_send::get_log_meta($id_meta,"AND status IN ('2')");
	$rMail = kmi_send::get_log_meta($id_meta,"AND status IN ('4','5')");
	$lMail = kmi_send::get_log_meta($id_meta,"AND status='5'");

	if(count($fMail)<1 && count($sMail)<1 && count($pMail)<1){
		sobad_db::_update_single($id_meta,'email-log',array('status' => 3));
		$val['status'] = 3;
	}	
	
	$disable = '';
	$status = send_mail::_conv_status($val['status']);		
	
	// jumlah read email
	if(count($rMail)>0){
		$read = array(
			'ID'	=> 'read_'.$id_meta,
			'func'	=> 'readView_send',
			'class'	=> 'link_click_malika',
			'color'	=> '',
			'icon'	=> '',
			'label'	=> count($rMail) .'/'. count($tMail)
		);

		$read = edit_button($read);
	}else{
		$read = count($rMail) .'/'. count($tMail);
	}
	
	// jumlah click link
	if(count($lMail)>0){
		$link = array(
			'ID'	=> 'click_'.$id_meta,
			'func'	=> 'clickView_send',
			'class'	=> 'link_click_malika',
			'color'	=> '',
			'icon'	=> '',
			'label'	=> count($lMail) .'/'. count($tMail)
		);

		$link = edit_button($link);
	}else{
		$link = count($lMail) .'/'. count($tMail);
	}
	
	if($val['status']==1){
		// Check jumlah email terkirim
		$sMail = kmi_send::get_log_meta($id_meta,"AND status='1'");
		$a = count($sMail);$b = count($tMail);
		$c = $b-$a;
		if($c>0){
			$persen = round($c/$b*100,1);
		}else{
		    $persen = 0;
		}

		$view = array(
			'ID'	=> 'view_'.$id_meta,
			'func'	=> 'view_send',
			'color'	=> '',
			'icon'	=> 'fa fa-circle',
			'label'	=> $persen."%"
		);

		$status = edit_button($view);			
		$disable = 'disabled';
	}

	if($val['status']>1 || $val['status']==0){
		$fcnt = '';$color='green';
		if($val['status']==0){
			$color = 'default';
			$fcnt = '';
		}

		if($val['status']==2){
			$color = 'red';
			$fcnt = '('.count($fMail).')';
		}

		$view = array(
			'ID'	=> 'view_'.$id_meta,
			'func'	=> 'view_send',
			'color'	=> $color,
			'icon'	=> 'fa fa-circle',
			'label'	=> $status[0].' '.$fcnt
		);

		$status = edit_button($view);
	}
	
	$kirim = array(
		'ID'	=> 'send_'.$val['ID'],
		'func'	=> 'setMail_send',
		'load'	=> 'sobad_portlet',
		'color'	=> 'green',
		'icon'	=> 'fa fa-send-o',
		'label'	=> 'send',
		'status'=> $disable
	);
	
	$datetime = strtotime($val['date']);
	$date = format_date_id($val['date']);
	$time = date('H:i:s',$datetime);
	
	$config['table'][$key]['tr'] = array('');
	$config['table'][$key]['td'] = array(
		'nama'			=> array(
			'left',
			'auto',
			$val['subject_mail'],
			true
		),
		'email'			=> array(
			'left',
			'15%',
			$val['type_to_m']==4?'Group : '.$val['name_to_m']:$val['email_to_m'],
			true
		),
		'tanggal'		=> array(
			'center',
			'15%',
			$date.' '.$time,
			true
		),
		'status'		=> array(
			'center',
			'7%',
			$status,
			true
		),
		'read'			=> array(
			'center',
			'7%',
			$read,
			true
		),
		'link'			=> array(
			'center',
			'7%',
			$link,
			true
		),
		'send'			=> array(
			'center',
			'8%',
			_click_button($kirim),
			false
		),
		'preview'		=> array(
			'center',
			'8%',
			_modal_button($preview),
			false
		),
		'Edit'			=> array(
			'center',
			'8%',
			edit_button($edit),
			false
		),
		'hapus'			=> array(
			'center',
			'8%',
			hapus_button($hapus),
			false
		)
	);
}
