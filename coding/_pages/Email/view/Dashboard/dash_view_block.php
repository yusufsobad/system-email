<?php

class dash_block extends _page{
	public static function _modal($id=0,$start=1){
		$table = self::_block_table($id,$start);

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

	public static function _block_table($id=0,$start=1,$search=false,$cari=array()){
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
		
		$_search = '';
		$kata = '';$where = "AND status IN ($status)";
		if(self::$search){		
			$src = self::like_search($args,$where);
			$cari = $src[0];
            $where = $src[0];
            $kata = $src[1];
            $_search = $src[2];
		}else{
			$cari=$where;
		}

		$limit = $where.' ORDER BY meta_date DESC LIMIT '.intval(($start - 1) * 10).',10';

		$send = kmi_send::get_status_mail($status,$limit);
		$sum_data = kmi_send::get_status_mail($status,$cari);

		$data['data'] = array('search_email_block',$kata,$id,"dash_block_email");

		$data['data'] = array(
			'func' 	=> '_search_block',
			'data'	=> $kata,
			'value'	=> $_search,
			'type'	=> $id,
			'load'	=> 'dash_block_email'
		);

		$data['search'] = array('Semua','nama','email');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> count($sum_data),
				'limit'		=> 10,
				'func'		=> '_block_pagination',
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

			$status = send_mail::_conv_status($val['status']);
			$status = '<i class="fa fa-circle" style="color:'.$status[1].'"></i> '.$status[0];

			$data['table'][$key]['tr'] = array();
			$data['table'][$key]['td'] = array(
				'nama'			=> array(
					'left',
					'auto',
					$val['name_meta'],
					true
				),
				'email'			=> array(
					'left',
					'30%',
					str_replace(';', '; ', $val['email_meta']),
					true
				),
				'Tanggal'		=> array(
					'left',
					'15%',
					format_date_id(date($date)),
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

	// ----------------------------------------------------------
	// Function send mail to database ---------------------------
	// ----------------------------------------------------------
	private static function _get_block_table($idx,$args=array()){
		if($idx==0){
			$idx=1;
		}

		$tp = isset($_POST['type'])?$_POST['type']:'';
		$args = isset($_POST['args'])?sobad_asset::ajax_conv_json($_POST['args']):$args;		
		
		$table = self::_block_table($tp,$idx,true,$args);
		return table_admin($table);
	}

	public static function _block_pagination($idx){
		return self::_get_block_table($idx);
	}

	public static function _search_block($args=array()){
		$args = sobad_asset::ajax_conv_json($args);

		return self::_get_block_table(1,$args);
	}
}