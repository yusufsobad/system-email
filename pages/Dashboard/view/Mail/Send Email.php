<?php

function send_head_title(){
	$args = array(
		'title'	=> 'Send Email<small>data send email</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> 'send_mail',
				'label'	=> 'send & history'
			)
		),
		'date'	=> false
	);
	
	return $args;
}

// ----------------------------------------------------------
// Layout Send Mail  ----------------------------------------
// ----------------------------------------------------------
function send_mail(){
	return send_layout(1);
}

function send_table($start=1,$search=false,$cari=array()){
	$data = array();
	$args = array('ID','mail_subject','mail_to','date','status');
	
	$kata = '';$where = '';
	if($search){
		$src = like_pencarian($args,$cari);
		$cari = $src[0];
		$where = $src[0];
		$kata = $src[1];
	}else{
		$cari='';
	}
	
	$batas = " AND trash='0' ";
	$limit = 'ORDER BY date DESC LIMIT '.intval(($start - 1) * 10).',10';
	$where .= $batas.$limit;
	$cari .= $batas;
	
	$send = new kmi_send();
	$args = $send->get_sends($args,$where);
	$sum_data = $send->get_sends(array('ID'),$cari);
	
	$data['data'] = array('search_send',$kata);
	$data['search'] = array('Semua','subject','email');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'send_pagination'
		)
	);
	
	foreach($args as $key => $val){
		$id_meta = $val['ID'];
		$edit = array(
			'ID'	=> 'edit_'.$id_meta,
			'func'	=> 'edit_send',
			'color'	=> 'blue',
			'icon'	=> 'fa fa-edit',
			'label'	=> 'edit'
		);
		
		$hapus = array(
			'ID'	=> 'del_'.$id_meta,
			'func'	=> 'hapus_send',
			'color'	=> 'red',
			'icon'	=> 'fa fa-trash',
			'label'	=> 'hapus'
		);

		$preview = array(
			'ID'	=> 'preview_'.$id_meta,
			'func'	=> 'preview_send',
			'color'	=> 'yellow',
			'icon'	=> 'fa fa-eye',
			'label'	=> 'view'
		);
		
		// Check jumlah email
		$tMail = $send->get_log_meta($id_meta);
		$pMail = $send->get_log_meta($id_meta,"AND status IN ('0')");
		$sMail = $send->get_log_meta($id_meta,"AND status IN ('1')");
		$fMail = $send->get_log_meta($id_meta,"AND status IN ('2')");
		$rMail = $send->get_log_meta($id_meta,"AND status IN ('4','5')");
		$lMail = $send->get_log_meta($id_meta,"AND status='5'");

		if(count($fMail)<1 && count($sMail)<1 && count($pMail)<1){
			$send->_update_table($id_meta,'email-log',array('status' => 3));
			$val['status'] = 3;
		}	
		
		$disable = '';
		$status = conv_status_send($val['status']);		
		
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
			$sMail = $send->get_log_meta($id_meta,"AND status='1'");
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
		
		$data['table'][$key]['tr'] = array('');
		$data['table'][$key]['td'] = array(
			'nama'			=> array(
				'left',
				'auto',
				$val['mail_subject'],
				true
			),
			'email'			=> array(
				'left',
				'15%',
				$val['cust_type']==4?'Group : '.$val['cust_name']:$val['cust_mail'],
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
				email_button($kirim),
				false
			),
			'preview'		=> array(
				'center',
				'8%',
				edit_button($preview),
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
	
	return $data;
}

function send_layout($start){
	$data = send_table($start);
	
	$box = array(
		'label'		=> 'Data kirim email',
		'tool'		=> '',
		'action'	=> 'send_action',
		'func'		=> 'sobad_table',
		'data'		=> $data
	);
	
	$opt = array(
		'title'		=> send_head_title(),
		'style'		=> '',
		'script'	=> array('')
	);
	
	return portlet_admin($opt,$box);
}

function send_action(){
	$add = array(
		'ID'	=> 'add_0',
		'func'	=> 'send_add_form',
		'color'	=> 'btn-default',
		'icon'	=> 'fa fa-plus',
		'label'	=> 'Tambah'
	);
	
	return edit_button($add);
}

// ----------------------------------------------------------
// Form data send mail --------------------------------------
// ----------------------------------------------------------
function send_add_form(){
	$vals = array(0,'',0,0,'',0,0,0);
	
	$args = array(
		'title'		=> 'Tambah data kirim',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'tambah_send',
			'load'		=> 'sobad_portlet'
		)
	);
	
	return send_data_form($args,$vals);
}

function send_edit_form($vals=array()){
	$check = array_filter($vals);
	if(empty($check)){
		return '';
	}

	$type = $vals['status'];
	$vals = array(
		$vals['ID'],
		$vals['name'],
		$vals['mail_from'],
		$vals['mail_to'],
		$vals['mail_subject'],
		$vals['attachment'],
		$vals['template'],
		$vals['footer']
	);
	
	$args = array(
		'title'		=> 'Edit data category',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'update_send',
			'load'		=> 'sobad_portlet'
		)
	);
	
	return send_data_form($args,$vals,$type);
}

function send_data_form($args=array(),$vals=array(),$type=0){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$tmpl = new kmi_template();
	$email = new kmi_mail();
	$my = $email->get_produsens(array('ID','name'));
	$cust = $email->get_customers(array('ID','name'));
	$group = $email->get_groups(array('ID','name'));
	$cont = $tmpl->get_contents(array('ID','name'));
	$sign = $tmpl->get_signatures(array('ID','name'));
	
	$my = convToOption($my,'ID','name');
	$cust = convToOption($cust,'ID','name');
	$group = convToOption($group,'ID','name');
	$cont = convToOption($cont,'ID','name');
	$sign = convToOption($sign,'ID','name');
	
	$opt_grp = array('Email' => $cust,'Group' => $group);
	
	$status = '';
	if($type==1){
		$status = 'disabled';
	}

	$data = array(
		0 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ID',
			'value'			=> $vals[0]
		),
		2 => array(
			'func'			=> 'opt_select',
			'data'			=> $my,
			'key'			=> 'mail_from',
			'label'			=> 'From',
			'class'			=> 'input-circle',
			'select'		=> $vals[2],
			'status'		=> $status
		),
		3 => array(
			'func'			=> 'opt_select',
			'group'			=> true,
			'data'			=> $opt_grp,
			'key'			=> 'mail_to',
			'label'			=> 'To',
			'class'			=> 'bs-select',
			'select'		=> $vals[3],
			'status'		=> 'data-live-search="true" multiple'.$status	
		),
		4 => array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'mail_subject',
			'label'			=> 'Subject',
			'class'			=> 'input-circle',
			'value'			=> $vals[4],
			'data'			=> 'placeholder="Subject" '.$status
		),
		5 => array(
			'func'			=> 'opt_input',
			'type'			=> 'file',
			'key'			=> 'attachment',
			'label'			=> 'Attachment',
			'class'			=> 'input-circle',
			'value'			=> $vals[5],
			'data'			=> 'placeholder="Attachment" multiple '.$status
		),
		6 => array(
			'func'			=> 'opt_select',
			'data'			=> $cont,
			'key'			=> 'template',
			'label'			=> 'Content',
			'class'			=> 'input-circle',
			'select'		=> $vals[6],
			'status'		=> $status
		),
		7 => array(
			'func'			=> 'opt_select',
			'data'			=> $sign,
			'key'			=> 'footer',
			'label'			=> 'Signature',
			'class'			=> 'input-circle',
			'select'		=> $vals[7],
			'status'		=> $status
		)
	);

	
	$args['func'] = array('sobad_form');
	$args['data'] = array($data);
	
	return modal_admin($args);
}

// ----------------------------------------------------------
// preview send mail ----------------------------------------
// ----------------------------------------------------------

function layout_preview_send($id=0){
	$log = new kmi_send();
	$log = $log->get_log($id,array('mail_from','mail_to','mail_subject','attachment','template','footer'));

	$attach = '';
	$att = explode(',',$log[0]['attachment']);
	foreach ($att as $key => $val) {
		$attach .= '<a href="../asset/uploads/attachment/'.$val.'" target="_blank"> '.$val.'</a> ; ';
	}

	?>
		<div style="text-align: center;">
			<div style="text-align: left;width: 650px;display: inline-block;padding:0px 0px 5px 25px;">
				<div> <strong>From</strong><i style="margin-left:44px;"></i>: <?php print($log[0]['my_name']) ;?></div>
				<div> <strong>To</strong><i style="margin-left:62px;"></i>: <?php print($log[0]['cust_name']) ;?></div>
				<div> <strong>Subject</strong><i style="margin-left:30px;"></i>: <?php print($log[0]['mail_subject']) ;?></div>	
				<div> <strong>Attachment</strong><i style="margin-left:5px;"></i>: <?php print($attach) ;?></div>				
			</div>
		</div>
		<div style="text-align: center;background: #efefef;">
			<div style="text-align: left;width: 650px;display: inline-block;background: #fff;padding: 25px;">
	<?php
			sobad_view_template($log[0]['template']);
			sobad_view_template($log[0]['footer']);
	?>
			</div>
		</div>
	<?php

}

function _btn_preview_send($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$status = '';
	if(isset($args['status'])){
		$status = $args['status'];
	}

	$type = '';
	if(isset($args['type'])){
		$type = $args['type'];
	}

	$content = 0;
	if(isset($args['template'])){
		$content = $args['template'];
	}
	
	?>
	<button data-sobad="<?php print($args['link']) ;?>" data-load="<?php print($args['load']) ;?>" data-type="<?php print($type) ;?>" type="button" class="btn green" data-dismiss="modal" onclick="sobad_submit(this)" <?php print($status) ;?>>Send</button>
	<a id="edit_<?php print($content) ;?>" data-sobad="template_edit_form_preview" data-load="here_modal2" data-type="<?php print($type) ;?>" type="button" class="btn blue" data-toggle="modal" href="#myModal2" onclick="sobad_button(this,0)">Edit</a>
	<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
	<?php
}

function setMail_send_preview(){
	$id = isset($_POST['type'])?$_POST['type']:0;
	setMail_send($id);
}

function template_edit_form_preview($id=0){
	$id = str_replace('edit_', '', $id);
	intval($id);

	$type = isset($_POST['type'])?$_POST['type']:0;
	$args = array('ID','name','lokasi','type','locked');	

	$meta = new kmi_template();
	$q = $meta->get_template($id,$args);	
	$vals = $q[0];

	$vals = array(
		$vals['ID'],
		$vals['name'],
		$vals['lokasi'],
		$vals['type'],
		$vals['locked']
	);

	$args = array(
		'title'		=> 'Edit Content',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'update_template_preview',
			'load'		=> 'here_modal',
			'type'		=> $type
		)
	);
	
	return template_data_form_preview($args,$vals,$vals[3]);
}

function template_data_form_preview($args=array(),$vals=array(),$type=1){
	$form = get_template_data_form($args,$vals,$type);

	$args['func'] = array('sobad_form_template','script_preview_template');
	$args['data'] = array($form,'');
	
	return modal_admin($args);
}

function script_preview_template(){
	?>
		<script type="text/javascript">
			$('#button_save_content').hide();
		</script>
	<?php
}

function update_template_preview($args=array()){
	$tp = isset($_POST['type'])?$_POST['type']:0;
	return update_template($args,'preview_send',$tp);
}

// ----------------------------------------------------------
// View data send mail --------------------------------------
// ----------------------------------------------------------
function readView_send($id=0){
	$id = str_replace('read_','',$id);
	intval($id);

	return view_send($id,"AND `email-log-meta`.status='4'");
}

function clickView_send($id=0){
	$id = str_replace('click_','',$id);
	intval($id);

	return view_send($id,"AND `email-log-meta`.status='5'");	
}

function view_send($id=0,$limit=''){
	$id = str_replace('view_','',$id);
	intval($id);
	
	$send = new kmi_send();
	$meta = $send->get_log_meta($id,$limit);
	
	$data['table'] = array();
	
	foreach($meta as $key => $val){
		$email = array(
			'ID'	=> 'send_'.$val['ID'],
			'func'	=> 'setMail_failMeta',
			'load'	=> 'here_modal',
			'color'	=> 'red',
			'icon'	=> 'fa fa-send-o',
			'label'	=> 'batal',
			'status'=> 'disabled'
		);

		$edit = array(
			'ID'	=> 'edit_'.$val['meta_mail'],
			'func'	=> 'customerEdit_send',
			'color'	=> 'blue',
			'icon'	=> 'fa fa-edit',
			'label'	=> 'edit',
			'type'	=> $id,
			'spin'	=> false
		);
		
		if($val['status']!=1){
			$email['func'] = 'setMail_sendMeta';
			$email['color'] = 'green';
			$email['icon'] = 'fa fa-send-o';
			$email['label'] = 'send';
		}
		
		if($val['status']==2){
			$email['status'] = '';
		}
		
		$status = conv_status_send($val['status']);
		$status = '<i class="fa fa-circle" style="color:'.$status[1].'"></i> '.$status[0];

		$datetime = strtotime($val['meta_date']);
		$date = format_date_id($val['meta_date']);
		$time = date('H:i:s',$datetime);
		$date .= ' '.$time;
		
		$data['table'][$key]['tr'] = array('');
		$data['table'][$key]['td'] = array(
			'no'		=> array(
				'center',
				'5%',
				$key + 1,
				true
			),
			'nama'		=> array(
				'left',
				'auto',
				$val['name'],
				true
			),
			'email'		=> array(
				'center',
				'25%',
				str_replace(';','; ', $val['email']),
				true
			),
			'status'	=> array(
				'center',
				'10%',
				$status,
				true
			),
			'updated'	=> array(
				'center',
				'15%',
				$date,
				true
			),
			'send'	=> array(
				'center',
				'10%',
				email_button($email),
				false
			),
			'edit'	=> array(
				'center',
				'10%',
				apply_button($edit),
				false
			),
		);
	}

	$args['title'] = 'Data Email Di kirim';
	$args['button'] = '';
	$args['func'] = array('sobad_table');
	$args['data'] = array($data);
	
	return modal_admin($args);
}

function customerEdit_send($id){
	$tp = isset($_POST['type'])?$_POST['type']:0;
	return edit_item_daftar($id,'customer','here_modal','customerUpdate_send',$tp);
}

function customerUpdate_send($args=array()){
	$tp = isset($_POST['type'])?$_POST['type']:0;
	return update_meta_daftar($args,'customer','view_send',$tp);
}

// ----------------------------------------------------------
// Function send mail to database ---------------------------
// ----------------------------------------------------------
function send_pagination($idx){
	if($idx==0){
		die('');
	}
	
	$table = send_table($idx);
	return table_admin($table);
}

function search_send($args=array()){
	$args = ajax_conv_json($args);
	
	$table = send_table(1,true,$args);
	return table_admin($table);
}

function hapus_send($id){
	$q = hapus_log_send($id);
	
	if($q===1){
		$title = send_table(1);
		return table_admin($title);
	}
}

function sendMail_fileUpload($args=array()){
	$fileName = $_FILES["file"];
	$jumlah = count($fileName['name']);

	$data = array();
	for($i=0;$i<$jumlah;$i++){
		$file = $fileName['tmp_name'][$i];
		$folder = 'attachment/'.$fileName['name'][$i];
		kmi_upload_file($file,$folder);

		$data[] = $fileName['name'][$i];
	}

	return implode(',',$data);
}

function preview_send($id){
	$id = str_replace('preview_', '', $id);
	intval($id);

	$send = new kmi_send();
	$send = $send->get_log($id,array('template','status'));
	
	$status = '';
	if($send[0]['status']==1){
		$status = 'disabled';
	}

	$args = array(
		'title'		=> 'Preview Email',
		'button'	=> '_btn_preview_send',
		'status'	=> array(
			'link'		=> 'setMail_send_preview',
			'load'		=> 'sobad_portlet',
			'type'		=> $id,
			'template'	=> $send[0]['template'],
			'status'	=> $status
		)
	);

	$args['func'] = array('layout_preview_send');
	$args['data'] = array($id);
	
	return modal_admin($args);
}

function edit_send($id){
	$id = str_replace('edit_','',$id);
	intval($id);
	
	$args = array(
		'ID',
		'mail_from',
		'mail_to',
		'status',
		'mail_subject',
		'attachment',
		'template',
		'footer',
		'name'
	);
	
	$meta = new kmi_send();
	$q = $meta->get_log($id,$args);
	
	if(count($q)===0){
		return '';
	}

	return send_edit_form($q[0]);
}

function update_send($args=array()){
	$args = ajax_conv_json($args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		unset($args['search']);
		unset($args['words']);
	}

	$args['status'] = 0;
	
	$db = new kmi_send();
	$q = $db->_update_table($id,'email-log',$args);

	// set log meta
	if(isset($args['mail_to'])){
		$where = "meta_id='$id'";
		$q = $db->_update_multiple($where,'email-log-meta',array('meta_id' => 0,'log' => $id));
		
		if($q!==0){
			$q = send_set_log_meta($id,$args['mail_to']);
		}
	}
	
	if($q!==0){
		$title = send_table(1);
		return table_admin($title);
	}
}

function tambah_send($args=array()){
	$args = ajax_conv_json($args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		unset($args['search']);
		unset($args['words']);
	}
	
	$db = new kmi_db();
	$q = $db->_insert_table('email-log',$args);
	
	// set log meta
	$q = send_set_log_meta($q,$args['mail_to']);
	
	if($q!==0){
		$title = send_table(1);
		return table_admin($title);
	}
}

function send_set_log_meta($q,$idx){
	$db = new kmi_db();
	$mail = new kmi_mail();
	
	// set log meta
	$email = $mail->get_email($idx);
	if($email[0]['type']==4){
		$q2 = $q;
		
		// get data group
		$email = $mail->get_group($idx,array('id_mail'));
		$email = explode(',',$email[0]['id_mail']);
		
		foreach($email as $key => $mail_to){
			$metas = array(
				'meta_id'		=> $q2,
				'meta_mail'		=> $mail_to,
			);
		
			$q = $db->_insert_table('email-log-meta',$metas);
		}
	}else{
		$metas = array(
			'meta_id'		=> $q,
			'meta_mail'		=> $idx,
		);
		
		$q = $db->_insert_table('email-log-meta',$metas);
	}
}

// ----------------------------------------------------------
// Function send Mail Option --------------------------------
// ----------------------------------------------------------
function setMail_send($id){
	$id = str_replace('send_','',$id);
	intval($id);
	
	$where = "meta_id='$id'";
	$args = array(
		'meta_date'	=> date('Y-m-d H:i:s'),
		'status'	=> 1
	);
	
	$arg_log = array(
		'date'		=> date('Y-m-d H:i:s'),
		'status'	=> 1
	);
	
	$db = new kmi_send();
	$email = $db->get_log($id,array('status'));
	
	if($email[0]['status']!=0){
		$q = re_log_meta($id);
	}
	
	$q = $db->_update_multiple($where,'email-log-meta',$args);
	$q = $db->_update_table($id,'email-log',$arg_log);
	
	// Send Mail ------
	$msg = sendMail_send(0,1,0);
	foreach($msg as $key => $val){
		$req = sobad_send_mail($val['data']);
		
		if($req===0){
			sendMail_send($val['index'],2,$val['meta_id']);
		}else{
			sendMail_send($val['index'],3,$val['meta_id']);
		}
	}
	
	if($q!==0){
		$title = send_table(1);
		return table_admin($title);
	}
}

function setMail_sendMeta($id=''){
	$send = new kmi_send();
	$id = str_replace('send_','',$id);
	intval($id);
	
	$limit = "`email-log-meta`.ID='$id'";			
	$meta = $send->get_log_send($limit);
	
	$id_mail = $meta[0]['ID'];
	$idx = $meta[0]['meta_id'];
	
	$log = $send->get_log($idx);
	$data = setMail_option($log[0],$meta[0]);
					
	$req = sobad_send_mail($data);
	
	if($req===0){
		sendMail_send($id_mail,2,$idx);
		return $send->_alert_db('Send Failed');
	}else{
		sendMail_send($id_mail,3,$idx);
		return view_send($idx);
	}
}

function conv_status_send($id=0){
	switch($id){
		case 0:
			$status = 'pending';
			$color = '#888';
			break;
		case 1:
			$status = 'sending';
			$color = '#fff';
			break;
		case 2:
			$status = 'failed';
			$color = '#cb5a5e';
			break;
		case 3:
			$status = 'success';
			$color = '#44b6ae';
			break;
		case 4:
			$status = 'reading';
			$color = '#578ebe';
			break;
		case 5:
			$status = 'click link';
			$color = '#8775a7';
			break;
		default:
			$status = '';
			$color = '';
			break;
	}
	
	return array($status,$color);
}

function sendMail_send($id,$type,$meta=0){
	// type
	// if 1 == sending
	// if 2 == gagal
	// if 3 == success
	// if 4 == read
	// if 5 == click link
	
	switch($type){
		case 1:
			$send = new kmi_send();
			$q = $send->_select_table('WHERE 1=1','email-user',array('ID'));
			if($q!==0){
				$id_user = array();
				while($r=$q->fetch_assoc()){
					$id_user[] = $r['ID'];
				}
			}
			
			$cnt_user = count($id_user);
			$max_mail = 82; // per 5 menit (cronjob)
			$lmt = floor(82/$cnt_user);
			
			$mail_send = array();
			foreach($id_user as $key => $val){
				$limit = "`email-log-meta`.user='$val' AND `email-log-meta`.status='1' ORDER BY ID ASC LIMIT $lmt";			
				$meta = $send->get_log_send($limit);

				$check = array_filter($meta);
				if(!empty($check)){
					foreach($meta as $ky => $vl){
						$id_mail = $vl['ID'];
						$idx = $vl['meta_id'];
						$log = $send->get_log($idx);
			
						$args = array(
							'index'		=> $id_mail,
							'meta_id'	=> $idx,
							'data'		=> setMail_option($log[0],$vl)
						);
					
						$mail_send[] = $args;
					}
				}
			}
			
			return $mail_send;
			break;
		case 2:
		case 3:
			$args = array(
		            'meta_date' => date('Y-m-d H:i:s'),
		            'status' => $type
		        );
		        
			$db = new kmi_db();
			$q = $db->_update_table($id,'email-log-meta',$args);
			
			$send = new kmi_send(); 
			
			// get status belum terkirim
			$q = $send->get_log_meta($meta,"AND status='1'");
			
			$check = array_filter($q);
			if(empty($q)){
				// get status tidak terkirim
				$q = $send->get_log_meta($meta,"AND status='2'");
				if(count($q)<1){
					$type = 3;
				}else{
					$type = 2;
				}
				// update status to log
				$args = array(
					'date'		=> date('Y-m-d H:i:s'),
					'status' 	=> $type
				);
				
				$q = $db->_update_table($meta,'email-log',$args);
			}
			break;
		case 4:
		case 5:
			$db = new kmi_db();
			$link = isset($_GET['link'])?$_GET['link']:'';

			$q = $db->_select_table("WHERE ID='$meta'",'email-log-meta',array('status'));
			$r = $q->fetch_assoc();
			$status = $r['status'];

			switch($type){
				case 4:
					$args = array(
						'status'	=> $type,
						'read_date'	=> date('Y-m-d H:i:s')
					);
					break;
				case 5:
					$args = array(
						'status'	=> $type,
						'link_date'	=> date('Y-m-d H:i:s')
					);
					break;
				default:
					$args = array(
						'status'	=> $type,
						'meta_date'	=> date('Y-m-d H:i:s')
					);
					break;
			}
			
			if($type>=$status){
				$q = $db->_update_table($meta,'email-log-meta',$args);
			}

			header('Location : '.$link);
			break;
		default:
			break;
	}
}

function setMail_option($args=array(),$metas=array()){
	$check = array_filter($args);
	if(empty($check)){
		return array();
	}
	
	$check = array_filter($metas);
	if(empty($check)){
		return array();
	}

// get option server email
	$option = new kmi_mail();
	$opts = $option->get_option($args['mail_from']);
	
	foreach($opts as $key => $val){
		$args[$val['meta_key']] = $val['meta_value'];
	}
	
// decrypt pass
	$pass = $args['mail_pass'];
	$pass = kmi_decrypt($pass);
	
// get template HTML	
	$url = template_url().$args['url_tmplate'];
	$footer = template_url().$args['url_footer'];
	
	ob_start();
	include $url;
	include $footer;
	$html = ob_get_clean();
	
	$html = convMail_html($html,$metas);
	
	$opt = array(
		'secure'		=> $args['mail_secure'],
		'host'			=> $args['mail_host'],
		'mail_from'		=> $args['my_mail'],
		'pass'			=> $pass,
		'name_from'		=> $args['my_name'],
		'subject'		=> $args['mail_subject'],
		'attachment'	=> $args['attachment'],
		'mail_to'		=> $metas['email'],
		'name_to'		=> $metas['name'],
		'html'			=> $html
	);
	
	return $opt;
}

function convMail_html($html='',$args=array()){
	/*
		::full_name:: => name
	*/
	
	if(empty($html)){
		return '';
	}
	
	$url = 'https://kirim.kreasimudaindonesia.com/include/sending.php?';
	$dt_read = $url.'send=sendMail_send&data='.$args['meta_id'].'-4-'.$args['ID'];
	$dt_link = $url.'send=sendMail_send&data='.$args['meta_id'].'-5-'.$args['ID'];
	
	$read = '<img style="display:none;" src="'.$dt_read.'"></br>';	
	$link = $dt_link.'&link=';
	
	$html = str_replace('::full_name::',$args['name'],$html); // ::full_name::
	//$html = str_replace('::link::',$link,$html); // ::link::
	$html = str_replace('href="', 'href="'.$link, $html);
	
	$html .= $read;
	return $html;
}

function sobad_send_mail($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return 0;
	}
	
	//check validasi email
	$mail_to = explode(';',$args['mail_to']);
	if(!is_array($mail_to)){
		$mail_to[0] = $args['mail_to'];
	}
	
	foreach($mail_to as $ky => $vl){
		if(!_valid_mail($vl)){
			unset($mail_to[$ky]);
		}
	}
	
	array_filter($mail_to);
	if(empty($mail_to)){
		return 0;
	}

	// File Attachment
	$folder = '../asset/uploads/attachment/';
	
	switch($args['secure']){
		case 0:
			$secure = 'no';
			$port = 110;
			break;
		case 1:
			$secure = 'ssl';
			$port = 465;
			break;
		case 2:
			$secure = 'tls';
			$port = 587;
			break;
		default:
			$port = 465;
	}

	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->SMTPSecure = $secure; // no/ssl/tls
	$mail->Host = $args['host']; //hostname masing-masing provider email
	$mail->SMTPDebug = 0; // Debug 0,1,2
	$mail->Port = $port; // no = 110 , ssl = 465 , tls = 587
	$mail->SMTPAuth = true;
	$mail->Username = $args['mail_from']; //user email
	$mail->Password = $args['pass']; //password email
	$mail->SetFrom($args['mail_from'],$args['name_from']); //set email pengirim

	if(!empty($attach)){
		$attach = explode(',',$args['attachment']);
		foreach ($attach as $ky => $val) {
			$mail->addAttachment($folder.$val,$val);
		}
	}
	
	foreach($mail_to as $ky => $val){
	    $mail->Subject = str_replace('::full_name::',$args['name_to'],$args['subject']); //subyek email
		$mail->AddAddress($val,$args['name_to']); //tujuan email
	}
	
	$mail->MsgHTML($args['html']);
	if($mail->Send()){ 
		return 1;
	}else{ 
		return 0;
	}
}