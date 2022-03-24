<?php

function view_dash_block($id=0){
	$id = str_replace('mail_', '', $id);
	intval($id);

	return dash_block_modal($id,1);
}

function dash_block_table($id=0,$start=1,$search=false,$cari=array()){
	$data = array();
	$args = array('ID','mail_subject','mail_to','date','status');
	$_args = array('`email-list`.ID','`email-list`.name','`email-list`.email');

	$status = $id;
	if($id==3){
		$status = "'3','4','5'";
	}

	if($id==4){
		$status = "'4','5'";
	}
	
	$kata = '';$where = "AND status IN ($status)";
	if($search){
		$src = like_pencarian($_args,$cari,$where);
		$cari = $src[0];
		$where = $src[0];
		$kata = $src[1];
	}else{
		$cari=$where;
	}

	$limit = $where.' ORDER BY meta_date DESC LIMIT '.intval(($start - 1) * 10).',10';

	$sends = new kmi_send();
	$send = $sends->get_status_mail($status,$limit);
	$sum_data = $sends->get_status_mail($status,$cari);

	$data['data'] = array('search_email_block',$kata,$id,"dash_block_email");
	$data['search'] = array('Semua','nama','email');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'email_block_pagination',
			'load'		=> 'dash_block_email',
			'type'		=> $id
		)
	);

	foreach ($send as $key => $val) {
		$date = strtotime($val['meta_date']);

		if($id==4){
			$date = strtotime($val['read_date']);
		}

		if($id==5){
			$date = strtotime($val['link_date']);
		}

		$status = conv_status_send($val['status']);
		$status = '<i class="fa fa-circle" style="color:'.$status[1].'"></i> '.$status[0];

		$data['table'][$key]['tr'] = array();
		$data['table'][$key]['td'] = array(
			'nama'			=> array(
				'left',
				'auto',
				$val['name'],
				true
			),
			'email'			=> array(
				'left',
				'30%',
				str_replace(';', '; ', $val['email']),
				true
			),
			'Tanggal'		=> array(
				'left',
				'15%',
				format_date_id(date('Y-m-d',$date)),
				true
			),
			'Waktu'		=> array(
				'left',
				'10%',
				date('H:i'),
				true
			),
			'status'		=> array(
				'center',
				'12%',
				$status,
				true
			)
		);
	}

	return $data;
}

function dash_block_modal($id=0,$start=1){
	$table = dash_block_table($id,$start);

	$args = array(
		'id'		=> 'dash_block_email',
		'title'		=> 'Data Email',
		'button'	=> '',
		'status'	=> array(),
		'func'		=> array('sobad_table'),
		'data'		=> array($table)
	);
	
	return modal_admin($args);
}

// ----------------------------------------------------------
// Function send mail to database ---------------------------
// ----------------------------------------------------------
function _get_dash_block_table($idx,$args=array()){
	if($idx==0){
		$idx=1;
	}

	$tp = isset($_POST['type'])?$_POST['type']:'';
	$args = isset($_POST['args'])?ajax_conv_json($_POST['args']):$args;		
	
	$table = dash_block_table($tp,$idx,true,$args);
	return table_admin($table);
}

function email_block_pagination($idx){
	return _get_dash_block_table($idx);
}

function search_email_block($args=array()){
	$args = ajax_conv_json($args);

	return _get_dash_block_table(1,$args);
}