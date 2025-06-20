<?php

if(!class_exists('daftar_mail')){
	include 'daftar_mail.php';
}

include 'contents_mail.php';

class send_mail extends _page{

	protected static $object = 'send_mail';

	protected static $table = 'kmi_send';

	protected static $loc_view = 'Email.send';

	protected static function _array(){
		$args = array(
			'ID',
			'subject_mail',
			'to_mail',
			'status',
			'from_mail',
			'attachment',
			'template',
			'footer',
			'name',
			'date',
			'user'
		);

		return $args;
	}

	protected static function table(){
		$admin = isset($_SESSION[_prefix . 'admin']) ? $_SESSION[_prefix . 'admin'] : 0;

		$data = array();
		$args = self::_array();
		$type = self::$type;

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$_search = '';$kata = '';

		$user = get_id_user();

		$whr = !$admin ? "AND `email-log`.user='$user'" : "";
		$where = $whr . " AND `email-log`.trash='0'";

		if(self::$search){		
			$src = self::like_search($args,$where);
			$cari = $src[0];
            $where = $src[0];
            $kata = $src[1];
            $_search = $src[2];
		}else{
			$cari=$where;
		}
		
		$limit = 'ORDER BY ID DESC LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		$sum_data = $object::count("1=1 ".$cari);

		$data = array(
            'kata'      => $kata,
            'search'    => $_search,
            'data'      => $args,
            'start'     => $start,
            'sum_data'  => $sum_data,
            'nLimit'    => $nLimit,
            'admin'		=> $admin
        );

		return self::_loadView('table',$data);
	}

	protected static function head_title(){
		$args = array(
			'title'	=> 'Send Email<small>send email</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'send email',
					'uri'	=> 'send & history'
				)
			),
			'date'	=> false
		);
	
		return $args;
	}

	protected static function get_box(){
		$data = self::table();
		
		$box = array(
			'label'		=> 'Data kirim email',
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected static function layout(){
		$box = self::get_box();
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	public static function _conv_status($id=0){
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

	public static function template_url(){
		return contents_mail::$url;
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	public static function add_form(){
		$vals = array(0,'',0,0,0,'',0,0,'',date('Y-m-d'));
		$vals = array_combine(self::_array(), $vals);

		$config =self::_loadView('form',array(
			'data' 	=> $vals
		));

		$data = array(
			'title'		=> 'Tambah Kirim Email',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_db',
				'load'		=> 'sobad_portlet',
			),
			'func'		=> array('sobad_form','_script_form'),
			'data'		=> array($config,'')
		);

        return modal_admin($data);
	}

	protected static function edit_form($vals=array()){
		$check = array_filter($vals);
		if(empty($check)){
			return '';
		}
		
		$config =self::_loadView('form',array(
			'data' 	=> $vals
		));

        $data = array(
			'title'		=> 'Edit Kirim Email',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_db',
				'load'		=> 'sobad_portlet',
			),
			'func'		=> array('sobad_form','_script_form'),
			'data'		=> array($config,'')
		);

        return modal_admin($data);
	}

	public static function _script_form(){
		?>
			<script type="text/javascript">
				$('#list_mail').parent().children('.bs-select').children('div.dropdown-menu').children('.bs-searchbox').children().on('change',function(){
					sobad_loading('.bs-select ul.selectpicker');

					data = "ajax=_load_listmail&object="+object+"&type="+this.value+"&data="+$('select[name=to_mail]').val();
					sobad_ajax('#list_mail',data,select_option_search,false);
				});

				function select_option_search(data,id){
					$(id).html(data);
					$('.bs-select').selectpicker('refresh');

					$('div.bs-select:nth-child(2) ul.selectpicker .blockUI').remove();
				}
			</script>
		<?php
	}

	public static function _load_listmail($id=0){
		$search = isset($_POST['type'])?$_POST['type']:'';

		$where = empty($search)?"":"AND name LIKE '%$search%' ";
		$cust = kmi_mail::get_customers(array('ID','name'),$where . "LIMIT 20");
		$group = kmi_mail::get_groups(array('ID','name'),$where. "LIMIT 20");
		$exgroup = kmi_mail::get_exgroups(array('ID','name'),$where. "LIMIT 20");

		$groups = array(
			'Email'		=> $cust,
			'Group'		=> $group,
			'ExGroup'	=> $exgroup
		);

		$des = '';
		foreach ($groups as $ky => $val) {
			$des .= '<optgroup label="'.$ky.'">';
			foreach($val as $key => $cust){
				$des .= '<option value="'.$cust['ID'].'"> '.$cust['name'].' </option>';
			}
			$des .= '</optgroup>';
		}

		return $des;
	}

	public static function sendMail_fileUpload($args=array()){
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

	// ----------------------------------------------------------
	// preview send mail ----------------------------------------
	// ----------------------------------------------------------	

	public static function _preview($id){
		$id = str_replace('preview_', '', $id);
		intval($id);

		$send = kmi_send::get_log($id,array('template','status'));
		
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

		$args['func'] = array('_layout_preview');
		$args['data'] = array($id);
		
		return modal_admin($args);
	}

	public static function _layout_preview($id=0){
		$log = kmi_send::get_log($id,array('from_mail','to_mail','subject_mail','attachment','template','footer'));
		$log = $log[0];

		$attach = '';
		$att = explode(',',$log['attachment']);
		foreach ($att as $key => $val) {
			$attach .= '<a href="asset/uploads/attachment/'.$val.'" target="_blank"> '.$val.'</a> ; ';
		}

		?>
			<div style="text-align: center;">
				<div style="text-align: left;width: 650px;display: inline-block;padding:0px 0px 5px 25px;">
					<div> <strong>From</strong><i style="margin-left:44px;"></i>: <?php print($log['name_from']) ;?></div>
					<div> <strong>To</strong><i style="margin-left:62px;"></i>: <?php print($log['name_to_m']) ;?></div>
					<div> <strong>Subject</strong><i style="margin-left:30px;"></i>: <?php print($log['subject_mail']) ;?></div>	
					<div> <strong>Attachment</strong><i style="margin-left:5px;"></i>: <?php print($attach) ;?></div>				
				</div>
			</div>
			<div style="text-align: center;background: #efefef;">
				<div style="text-align: left;width: 650px;display: inline-block;background: #fff;padding: 25px;">
		<?php
				contents_mail::_preview(array(
					array('lokasi' => $log['lokasi_temp'])
				));

				contents_mail::_preview(array(
					array('lokasi' => $log['lokasi_foot'])
				));
		?>
				</div>
			</div>
		<?php

	}

	public static function _btn_preview_send($args=array()){
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

	public static function setMail_send_preview(){
		$id = isset($_POST['type'])?$_POST['type']:0;
		return self::setMail_send($id);
	}

	public static function template_edit_form_preview($id=0){
		$id = str_replace('edit_', '', $id);
		intval($id);

		$type = isset($_POST['type'])?$_POST['type']:0;
		$args = array('ID','name','lokasi','type','locked');	

		$q = kmi_template::get_template($id,$args);	
		$vals = $q[0];
		
		return self::template_data_form_preview($vals,$type);
	}

	public static function template_data_form_preview($vals=array(),$type=0){
		$args = array(
			'title'		=> 'Edit Content',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> 'update_template',
				'load'		=> 'here_modal',
				'type'		=> $type
			)
		);

		$args['func'] = array('_form_template');
		$args['data'] = array($vals);
		
		return modal_admin($args);
	}

	public static function _form_template($data=array()){
		$type = isset($_POST['type'])?$_POST['type']:0;
		contents_mail::_preview_form($data,$type);

		?>
			<script type="text/javascript">
				$('#button_save_content').hide();
			</script>
		<?php
	}

	public static function update_template($args=array()){
		$type = isset($_POST['type'])?$_POST['type']:0;

		contents_mail::_update_db($args,'');
		return self::_preview($type);
	}

	// ----------------------------------------------------------
	// Export Data ----------------------------------------------
	// ----------------------------------------------------------

	public static function _export_excel_detail($data){
		$id = str_replace('excel_','',$data);
		$id = intval($id);

		ob_start();
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Data Blast Email.xls");

		self::_html($id);
		return ob_get_clean();
	}

	public static function _html($id){
		$data = kmi_send::get_id($id)[0];
		$details = kmi_send::get_log_meta($id);

		report::view('Email/detail_data',compact('data','details'));
	}

	// ----------------------------------------------------------
	// Database send mail ---------------------------------------
	// ----------------------------------------------------------	

	public static function _callback($args=array()){
		$args['user'] = get_id_user();
		return $args;
	}

	protected static function _addDetail($args=array()){
		$q = self::send_set_log_meta($args['index'],$args['value']['to_mail']);
		return $q;
	}

	protected static function _updateDetail($args=array()){
		$id = $args['index'];

		$where = "meta_id='$id'";
		$q = sobad_db::_update_multiple($where,'email-log-meta',array('meta_id' => 0,'log' => $id));

		$q = self::send_set_log_meta($args['index'],$args['value']['to_mail']);
		return $q;
	}

	private static function send_set_log_meta($idx=0,$mail_to=0){
		// set log meta
		$user = get_id_user();

		$email = kmi_mail::get_email($mail_to,array('type'));
		if($email[0]['type']==4){

			// get data group
			$email = kmi_mail::get_group($mail_to,array('ID','meta_value'));
			$email = explode(',',$email[0]['meta_value']);
			
			foreach($email as $key => $mail_to){
				$metas = array(
					'meta_id'		=> $idx,
					'meta_mail'		=> $mail_to,
					'user'			=> $user
				);
			
				$q = sobad_db::_insert_table('email-log-meta',$metas);
			}
		}else{
			$metas = array(
				'meta_id'		=> $idx,
				'meta_mail'		=> $mail_to,
				'user'			=> $user
			);
			
			$q = sobad_db::_insert_table('email-log-meta',$metas);
		}
	}

	// ----------------------------------------------------------
	// View data send mail --------------------------------------
	// ----------------------------------------------------------
	public static function readView_send($id=0){
		$id = str_replace('read_','',$id);
		intval($id);

		return self::view_send($id,"AND `email-log-meta`.status IN ('4','5')",4);
	}

	public static function clickView_send($id=0){
		$id = str_replace('click_','',$id);
		intval($id);

		return self::view_send($id,"AND `email-log-meta`.status='5'",5);	
	}

	public static function view_send($id=0,$limit='',$view_type=0){
		$id = str_replace('view_','',$id);
		intval($id);

		// Check status group atau tidak
		$email = kmi_send::get_id($id,array('to_mail'));
		$email = $email[0];

		$status = $email['type_to_m']==4 && empty($limit)?true:false;
		
		$meta = kmi_send::get_log_meta($id,$limit);		
		$data = self::_loadView('table_detail',array(
			'data'		=> $meta,
			'type'		=> $id,
			'group'		=> $status,
			'view_type'	=> $view_type
		));

		$args = array(
			'title'		=> 'Data Email Di kirim',
			'button'	=> '',
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public static function _edit_mail($id=''){
		$type = $_POST['type'];
		$_POST['type'] = 2;
		return daftar_mail::edit_form($id,'_update_mail','here_modal',$type);
	}

	public static function _update_mail($args=array()){
		daftar_mail::_update_db($args,'');
		return self::view_send($_POST['type']);
	}

	public static function _delete_detail($id=0){
		$idg = isset($_POST['type'])?$_POST['type']:0;

		$id = str_replace("del_", "", $id);
		intval($id);

		// Hapus log meta
		$where = "meta_mail=" . $id . " AND meta_id=" . $idg;
		sobad_db::_delete_multiple($where,'email-log-meta');

		// Get group email
		$mail_to = kmi_send::get_id($idg,array('to_mail'));
		$mail_to = $mail_to[0]['to_mail'];

		// Hapus ID dari group email
		$mail = kmi_mail::get_group($mail_to,array('id_join','meta_value'));
		$group = $mail[0]['meta_value'];

		$group = explode(',', $group);
		foreach ($group as $key => $val) {
			if($val==$id){
				unset($group[$key]);
			}
		}

		// ---> Update
		$group = implode(',', $group);
		sobad_db::_update_single($mail[0]['id_join'],'email-group-meta',array(
			'meta_key'		=> 'mail_group',
			'meta_value'	=> $group
		));

		return self::view_send($idg);
	}

	// ----------------------------------------------------------
	// Function send Mail Option --------------------------------
	// ----------------------------------------------------------
	public static function _conv_limit_mail($load=0,$total=0,$limit=0){
		$hasil = floor($limit / $total * $load);
		return intval($hasil);
	}

	public static function setMail_send($id=0){
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
		
		$email = kmi_send::get_log($id,array('status'));
		
		if($email[0]['status']!=0){
			$q = re_log_meta($id);
		}
		
		$q = sobad_db::_update_multiple($where,'email-log-meta',$args);
		$q = sobad_db::_update_single($id,'email-log',$arg_log);
		
		// Send Mail ------
		$msg = self::sendMail_send(0,1,0);
		foreach($msg as $key => $val){
			$req = self::sobad_send_mail($val['data']);
			
			if($req===0){
				self::sendMail_send($val['index'],2,$val['meta_id']);
			}else{
				self::sendMail_send($val['index'],3,$val['meta_id']);
			}
		}
		
		$table = self::table(1);
		return table_admin($table);
	}

	public static function setMail_sendMeta($id=''){
		$id = str_replace('send_','',$id);
		intval($id);
		
		$limit = "AND `email-log-meta`.ID='$id'";			
		$meta = kmi_send::get_log_send($limit);
		
		$id_mail = $meta[0]['ID'];
		$idx = $meta[0]['meta_id'];
		
		$log = kmi_send::get_log($idx);
		$data = self::setMail_option($log[0],$meta[0]);
						
		$req = self::sobad_send_mail($data);
		
		if($req===0){
			self::sendMail_send($id_mail,2,$idx);
			die( _error::_alert_db('Send Failed') );
		}else{
			self::sendMail_send($id_mail,3,$idx);
			return self::view_send($idx);
		}
	}

	public static function sendMail_send($id=0,$type=0,$meta=0){
		// type
		// if 1 == sending
		// if 2 == gagal
		// if 3 == success
		// if 4 == read
		// if 5 == click link
		
		switch($type){
			case 1:

				$mail_send = array();
				$limit = "AND `email-log-meta`.status='1'";			
				$meta = kmi_send::get_log_send($limit);

				$check = array_filter($meta);
				if(empty($check)){
					return $mail_send;
				}

				// Get Jumlah user
				$total = count($meta);
				$users = array();$data = array();
				foreach ($meta as $key => $val) {
					$idx = $val['user'];
					if(!isset($users[$idx])){
						$data[$idx] = array();
						$users[$idx] = 0;
					}

					$users[$idx] += 1;

					$nom = $users[$idx] - 1;
					$data[$idx][$nom] = $val;
				}

				$max_mail = 1200; // per 1 Jam
				$cronjob = 5; // per 5 menit (cronjob)
				$lmt = floor($max_mail / (60 / $cronjob) );

				foreach($data as $ky => $val){
					$qty = $users[$ky];

					$limit = self::_conv_limit_mail($users[$ky],$total,$lmt);
					$limit = $limit>=$qty?$qty:$limit;

					for($i=0;$i<$limit;$i++){
						$vl = $val[$i];

						$id_mail = $vl['ID'];
						$idx = $vl['meta_id'];
						$log = kmi_send::get_log($idx);
			
						$args = array(
							'index'		=> $id_mail,
							'meta_id'	=> $idx,
							'data'		=> self::setMail_option($log[0],$vl)
						);
					
						$mail_send[] = $args;
					}
				}
				
				return $mail_send;
				break;
			case 2:
			case 3:
				$args = array(
			            'meta_date' 	=> date('Y-m-d H:i:s'),
			            'status' 		=> $type,
			            'error_note'	=> isset($_SESSION['ERROR_NOTE']) ? $_SESSION['ERROR_NOTE'] : ''
			        );
			        
				$q = sobad_db::_update_single($id,'email-log-meta',$args);

				// get status belum terkirim
				$q = kmi_send::get_log_meta($meta,"AND status='1'");
				
				$check = array_filter($q);
				if(empty($q)){
					// get status tidak terkirim
					$q = kmi_send::get_log_meta($meta,"AND status='2'");
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
					
					$q = sobad_db::_update_single($meta,'email-log',$args);
				}
				break;
			case 4:
			case 5:
				$link = isset($_GET['link'])?$_GET['link']:'';

				$q = sobad_db::_select_table("WHERE ID='$meta'",'email-log-meta',array('status'));
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
					$q = sobad_db::_update_single($meta,'email-log-meta',$args);
				}

				// Conversi Link
				if(!empty($link)){
					$_link = kmi_link::get_id($link,array('href'));

					$check = array_filter($_link);
					if(!empty($check)){
						$link = str_replace('&amp;', '&', $_link[0]['href']);

						sobad_db::_update_single($link,'email-link',array(
							'status'	=> 1,
							'link_date'	=> date('Y-m-d H:i:s')
						));
					}
				}

				header('Location : '.$link);
				break;
			default:
				break;
		}
	}

	public static function setMail_option($args=array(),$metas=array()){
		$check = array_filter($args);
		if(empty($check)){
			return array();
		}
		
		$check = array_filter($metas);
		if(empty($check)){
			return array();
		}

	// get option server email
		$opts = kmi_mail::get_option($args['from_mail']);
		
		foreach($opts as $key => $val){
			$args[$val['meta_key']] = $val['meta_value'];
		}
		
	// decrypt pass
		$pass = $args['mail_pass'];
		$pass = kmi_decrypt($pass);
		
	// get template HTML	
		$url = self::template_url().$args['lokasi_temp'];
		$footer = self::template_url().$args['lokasi_foot'];
		
		ob_start();
		include $url;
		include $footer;
		$html = ob_get_clean();
		
		$html = self::convMail_html($html,$metas);
		
		$opt = array(
			'secure'		=> $args['mail_secure'],
			'host'			=> $args['mail_host'],
			'mail_from'		=> $args['email_from'],
			'pass'			=> $pass,
			'name_from'		=> $args['name_from'],
			'subject'		=> $args['subject_mail'],
			'attachment'	=> $args['attachment'],
			'mail_to'		=> $metas['email_meta'],
			'name_to'		=> $metas['name_meta'],
			'place_to'		=> $metas['place_meta'],
			'html'			=> $html
		);
		
		return $opt;
	}

	public static function convMail_html($html='',$args=array()){
		/*
			::full_name:: => name
		*/
		
		if(empty($html)){
			return '';
		}
		
		$url = 'https://'.HOSTNAME.'/'.URL.'/include/sending.php?';
		$dt_read = $url.'page=mail&object=send&send=sendMail_send&data='.$args['meta_id'].'-4-'.$args['ID'];
		$dt_link = $url.'page=mail&object=send&send=sendMail_send&data='.$args['meta_id'].'-5-'.$args['ID'];
		
		$read = '<img style="display:none;" src="'.$dt_read.'"></br>';	
		$link = $dt_link.'&link=';
		
		$html = str_replace('::full_name::',$args['name_meta'],$html); // ::full_name::
		$html = str_replace('::place_name::',$args['place_meta'],$html); // ::place_name::

		$html = str_replace('{{full_name}}',$args['name_meta'],$html); // {{full_name}}
		$html = str_replace('{{place_name}}',$args['place_meta'],$html); // {{place_name}}

		//$html = str_replace('::link::',$link,$html); // ::link::
		//$html = str_replace('href="', 'href="'.$link, $html);

		// Get link
		// Conversi link href
		preg_match_all("/href=\"(.*?)\"/", $html, $href);
		$check = array_filter($href[0]);
		if(!empty($check)){
			foreach ($href[1] as $key => $val) {
				$idl = sobad_db::_insert_table('email-link',array(
					'link_meta'		=> $args['ID'],
					'href'			=> $val,
					'link_date'		=> '0000-00-00 00:00:00'
				));

				$html = str_replace('href="' . $val, 'href="' . $link . $idl, $html);
			}
		}

		// Mencari src image lokal --> replace
		$dicari = 'src=\"\/' . URL;
		if(preg_match("/$dicari/", $html)) {
			$replace = 'src="https://'. HOSTNAME . '/' . URL;
            $html = str_replace('src="/' . URL, $replace, $html);
        }
		
		$html .= $read;
		return $html;
	}

	public static function sobad_send_mail($args=array()){
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

		if(!class_exists('PHPMailer')){
			new _libs_(array('phpmailer'));
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

		if(!empty($args['attachment'])){ 
			$attach = explode(',',$args['attachment']); 
			foreach ($attach as $ky => $val) {
				$mail->addAttachment($folder.$val,$val);
			}
		}
		
		foreach($mail_to as $ky => $val){
			$subject = str_replace('::full_name::',$args['name_to'],$args['subject']); 	//subyek email
			$subject = str_replace('::place_name::',$args['place_to'],$subject); 		//subyek email

			$subject = str_replace('{{full_name}}',$args['name_to'],$args['subject']); 	//subyek email
			$subject = str_replace('{{place_name}}',$args['place_to'],$subject); 		//subyek email

		    $mail->Subject = $subject;
			$mail->AddAddress($val,$args['name_to']); //tujuan email
		}
		
		$mail->MsgHTML($args['html']);
		if($mail->Send()){ 
			$_SESSION['ERROR_NOTE'] = '';
			return 1;
		}else{ 
			$_SESSION['ERROR_NOTE'] = $mail->ErrorInfo;
			return 0;
		}
	}
}