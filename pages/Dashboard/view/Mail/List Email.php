<?php

function daftar_head_title(){
	$args = array(
		'title'	=> 'Contacts Email<small>contacts email</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> 'daftar_mail',
				'label'	=> 'contacts'
			)
		),
		'date'	=> false
	);
	
	return $args;
}

function get_portlet_daftar($start,$type){
	$data = daftar_table($start,$type);
	
	$label = $type;
	if($type=='produsen'){
		$label = 'Email';
	}
	
	$box = array(
		'label'		=> 'Data '.$label,
		'tool'		=> '',
		'action'	=> $type.'_daftar',
		'func'		=> 'sobad_table',
		'data'		=> $data
	);
	
	return $box;
}

// ----------------------------------------------------------
// Layout barang --------------------------------------------
// ----------------------------------------------------------

function daftar_mail(){
	return daftar_layout(1,'produsen');
}

function portlet_daftar($type){
	$data = get_portlet_daftar(1,$type);
	
	ob_start();
	?>
		<div class="row">
			<?php sobad_content('sobad_portlet',$data); ?>
		</div>
	<?php
	return ob_get_clean();
}

function daftar_table($start=1,$tab='produsen',$search=false,$cari=array()){
	$data = array();
	$args = array('ID','name','email','note');
	$_args = array('`email-list`.ID','`email-list`.name','`email-list`.email','`email-list`.note');
	
	$kata = '';$where = '';
	if($search){		
		$src = like_pencarian($_args,$cari);
		$cari = $src[0];
		$where .= $src[0];
		$kata = $src[1];
	}else{
		$cari='';
	}
	
	$limit = 'LIMIT '.intval(($start - 1) * 10).',10';
	$where .= $limit;
	
	$email = new kmi_mail();
	
	$email_func = 'get_'.$tab.'s';
	$args = $email->{$email_func}($args,$where);
	$sum_data = $email->{$email_func}(array('ID'),$cari);
	
	$data['data'] = array('search_daftar',$kata,$tab);
	$data['search'] = array('Semua','nama','email','description');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'daftar_pagination',
			'type'		=> $tab
		)
	);
	
	$send = new kmi_send();
	foreach($args as $key => $val){	
		$id_meta = $val['ID'];
		$edit = array(
			'ID'	=> 'edit_'.$val['ID'],
			'func'	=> $tab.'Edit_daftar',
			'color'	=> 'blue',
			'icon'	=> 'fa fa-edit',
			'label'	=> 'edit',
			'type'	=> $tab
		);
		
		$hapus = array(
			'ID'	=> 'del_'.$val['ID'],
			'func'	=> $tab.'Hapus_daftar',
			'color'	=> 'red',
			'icon'	=> 'fa fa-trash',
			'label'	=> 'hapus',
			'type'	=> $tab
		);
		
		$jml_mail = 1;
		if($tab=='group'){
			$jml_mail = $val['id_mail'];
			$jml_mail = explode(',',$jml_mail);
			$jml_mail = count($jml_mail);
		}

		$status = '';$view = '';
		if($tab=='customer'){
			$tMail = $send->get_log_send("meta_mail='$id_meta' AND status IN ('3','4','5')");
			$rMail = $send->get_log_send("meta_mail='$id_meta' AND status IN ('4','5')");
			$cMail = $send->get_log_send("meta_mail='$id_meta' AND status = '5'");

			$tMail = '<span class="badge badge-success">'.count($tMail).'</span>';
			$rMail = '<span class="badge badge-success" style="background-color:#578ebe;">'.count($rMail).'</span>';
			$cMail = '<span class="badge badge-success" style="background-color:#8775a7;">'.count($cMail).'</span>';

			$status = $tMail.' '.$rMail.' '.$cMail;

			$view = array(
				'ID'	=> 'view_'.$val['ID'],
				'func'	=> $tab.'View_daftar',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-bar-chart-o',
				'label'	=> 'Statistik',
				'type'	=> $tab
			);

			$view = edit_button($view);
		}
		
		$data['table'][$key]['tr'] = array();
		$data['table'][$key]['td'] = array(
			'nama'			=> array(
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
				'15%',
				$val['email'],
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
		
		if($tab!='group'){
			unset($data['table'][$key]['td']['jumlah']);
			unset($data['table'][$key]['td']['description']);
		}
	}
	
	return $data;
}

function daftar_layout($start,$type){
	$email = new kmi_mail();	
	$box = get_portlet_daftar($start,$type);
	
	$qty1 = $email->get_produsens(array('ID'));
	$qty1 = count($qty1);
	
	$qty2 = $email->get_customers(array('ID'));
	$qty2 = count($qty2);
	
	$qty3 = $email->get_groups(array('ID'));
	$qty3 = count($qty3);

	$tabs = array(
		'tab'	=> array(
			0	=> array(
				'key'	=> 'produsen',
				'func'	=> 'portlet_daftar',
				'label'	=> 'My Email',
				'info'	=> 'badge-success',
				'qty'	=> $qty1
			),
			1	=> array(
				'key'	=> 'customer',
				'func'	=> 'portlet_daftar',
				'label'	=> 'Database',
				'info'	=> 'badge-success',
				'qty'	=> $qty2
			),
			2	=> array(
				'key'	=> 'group',
				'func'	=> 'portlet_daftar',
				'label'	=> 'Group',
				'info'	=> 'badge-success',
				'qty'	=> $qty3
			)
		),
		'func'	=> 'sobad_portlet',
		'data'	=> $box
	);
	
	$opt = array(
		'title'		=> daftar_head_title(),
		'style'		=> '',
		'script'	=> array('')
	);
	
	return tabs_admin($opt,$tabs);
}

function produsen_daftar(){
	return daftar_action('produsen');
}

function customer_daftar(){
	$add = daftar_action('customer');
	
	$import = array(
		'ID'	=> 'import_0',
		'func'	=> 'customer_import_daftar',
		'color'	=> 'btn-default',
		'icon'	=> 'fa fa-file-excel-o',
		'label'	=> 'Import'
	);
	
	$imp = edit_button($import);
	
	return $imp.$add;
}

function group_daftar(){
	return daftar_action('group');
}

function daftar_action($func){
	$add = array(
		'ID'	=> 'add_0',
		'func'	=> $func.'_add_form_daftar',
		'color'	=> 'btn-default',
		'icon'	=> 'fa fa-plus',
		'label'	=> 'Tambah'
	);
	
	return edit_button($add);
}

function groupIn_action($id){
	$import = array(
		'ID'	=> 'import_'.$id,
		'func'	=> 'group_import_daftar',
		'color'	=> 'btn-default',
		'load'	=> 'here_modal2',
		'icon'	=> 'fa fa-file-excel-o',
		'label'	=> 'Import'
	);
	
	return apply_button($import);
}
// ----------------------------------------------------------
// Form data barang -----------------------------------------
// ----------------------------------------------------------
function produsen_add_form_daftar(){
	return daftar_add_form('My Email','produsen');
}

function customer_add_form_daftar(){
	return daftar_add_form('Customer','customer');
}

function group_add_form_daftar(){
	return daftar_add_form('Group','group');
}

function daftar_add_form($label,$type){
	$vals = array(0,'','');
	
	if($type=='produsen'){
		array_push($vals,'','',1);
	}
	
	if($type=='group'){
		array_push($vals,0,'');
	}
	
	$args = array(
		'title'		=> 'Tambah data '.$label,
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'tambah_'.$type.'Email',
			'load'		=> 'sobad_portlet'
		)
	);
	
	return daftar_data_form($args,$vals,$type);
}

function daftar_edit_form($val=array(),$type,$load='sobad_portlet',$func='',$tp=0){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}

	$vals = array(
		$val['ID'],
		$val['name'],
		$val['email']
	);
	
	if($type=='produsen'){
		$vals[] = kmi_decrypt($val['mail_pass']);
		$vals[] = $val['mail_host'];
		$vals[] = $val['mail_secure'];
	}
	
	if($type=='group'){
		$vals[3] = $val['note'];

		$val = explode(',',$val['id_mail']);
		$vals[2] = $val;
	}

	if(empty($func)){
		$func = $type.'Update_daftar';
	}
	
	$args = array(
		'title'		=> 'Edit data '.$type,
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> $func,
			'load'		=> $load,
			'type'		=> $tp
		)
	);
	
	return daftar_data_form($args,$vals,$type);
}

function daftar_data_form($args=array(),$vals=array(),$type){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$data = array(
		'cols'	=> array(2,9),
		0 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ID',
			'value'			=> $vals[0]
		),
		1 => array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'name',
			'label'			=> 'Nama',
			'class'			=> 'input-circle',
			'value'			=> $vals[1],
			'data'			=> 'placeholder="Nama"'
		),
		2 => array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'email',
			'label'			=> 'Email',
			'class'			=> 'input-circle',
			'value'			=> $vals[2],
			'data'			=> 'placeholder="Email"'
		)
	);
	
	if($type=='produsen'){
		$secure = array(
			1	=> 'ssl',
			2	=> 'tls'
		);
		
		$data[] = array(
			'func'			=> 'opt_input',
			'type'			=> 'password',
			'key'			=> 'mail_pass',
			'label'			=> 'Password',
			'class'			=> 'input-circle',
			'value'			=> $vals[3],
			'data'			=> ''
		);
		$data[] = array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'mail_host',
			'label'			=> 'Hostname',
			'class'			=> 'input-circle',
			'value'			=> $vals[4],
			'data'			=> ''
		);
		$data[] = array(
			'func'			=> 'opt_select',
			'data'			=> $secure,
			'key'			=> 'mail_secure',
			'label'			=> 'Encrypt',
			'class'			=> 'input-circle',
			'select'		=> $vals[5]
		);
	}
	
	if($type=='group'){
		// unset input email
		unset($data[2]);
		
		// get list email customer
		$mail = new kmi_mail();
		$mail = $mail->get_customers(array('ID','name','email'));
		foreach($mail as $key => $val){
			$email[$val['ID']] = $val['name'].' => '.$val['email'];
		}

		$data[] = array(
			'func'			=> 'opt_textarea',
			'key'			=> 'note',
			'label'			=> 'Description',
			'class'			=> 'input-circle',
			'value'			=> $vals[3],
			'rows'			=> 3
		);
		
		$data[] = array(
			'func'			=> 'opt_select',
			'data'			=> $email,
			'key'			=> 'mail_group',
			'label'			=> 'Email',
			'class'			=> 'input-circle bs-select input-large',
			'select'		=> $vals[2],
			'status'		=> 'data-live-search="true" data-size="6" data-style="blue" multiple'
		);
		
		$data_table = array('table' => array());
		$action_btn = '';
		if($vals[0]!=0){
			$action_btn = 'groupIn_action';
			$data_table = groupDaftar_table($vals[0]);
		}
		
		$portlet = array(
			'ID'		=> 'groupEmail_portlet',
			'label'		=> 'List Email',
			'tool'		=> '',
			'action'	=> $action_btn,
			'in_act'	=> $vals[0],
			'func'		=> 'sobad_table',
			'data'		=> $data_table
		);
	}
	
	$args['func'] = array('sobad_form');
	$args['data'] = array($data);
	
	if($type=='group'){
		array_push($args['func'],'sobad_portlet');
		array_push($args['data'],$portlet);
	}
	
	return modal_admin($args);
}

// ----------------------------------------------------------
// Form import daftar ---------------------------------------
// ----------------------------------------------------------
function groupDaftar_table($id=0,$start=1,$search=false,$cari=array()){
	$data = array();
	$args = array('ID','name','email');
	$_args = array('`email-list`.ID','`email-list`.name','`email-list`.email');
	
	$kata = '';$where = '';
	if($search){		
		$src = like_pencarian($_args,$cari);
		$cari = $src[0];
		$where = $src[0];
		$kata = $src[1];
	}else{
		$cari='';
	}
	
	$email = new kmi_mail();
	$clist = $email->get_group($id);	
	$clist = $clist[0]['id_mail'];
	
	$batas = "AND `email-list`.ID IN($clist) ";
	$limit = 'LIMIT '.intval(($start - 1) * 10).',10';
	$where .= $batas.$limit;
	$cari .= $batas;
	
	$email_func = 'get_customers';
	$args = $email->{$email_func}($args,$where);
	$sum_data = $email->{$email_func}(array('ID'),$cari);
	
	$data['data'] = array('groupListSearch_daftar',$kata,$id,'groupEmail_portlet');
	$data['search'] = array('Semua','nama','email');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'groupList_pagination',
			'load'		=> 'groupEmail_portlet',
			'type'		=> $id
		)
	);
	
	foreach($args as $key => $val){
		$data['table'][$key]['tr'] = array();
		$data['table'][$key]['td'] = array(
			'nama'	=> array(
				'left',
				'auto',
				$val['name'],
				true
			),
			'email'	=> array(
				'left',
				'50%',
				$val['email'],
				true
			)
		);
	}
	
	return $data;
}

function customer_import_daftar(){
	$data = array(
		'id'	=> 'importForm',
		'cols'	=> array(3,8),
		0 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ajax',
			'value'			=> 'import_customerEmail'
		),
		1 => array(
			'id'			=> 'file_import',
			'func'			=> 'opt_file',
			'type'			=> 'file',
			'key'			=> 'data',
			'label'			=> 'Filename',
			'accept'		=> '.csv',
			'data'			=> ''
		)
	);
	
	$args = array(
		'title'		=> 'Import Daftar Customer',
		'button'	=> '_btn_modal_import',
		'status'	=> array(
			'id'		=> 'importForm',
			'link'		=> 'import_customerEmail',
			'load'		=> 'sobad_portlet'
		)
	);
	
	$args['func'] = array('sobad_form');
	$args['data'] = array($data);
	
	return modal_admin($args);
}

function group_import_daftar($id){
	$id = str_replace('import_','',$id);
	$data = array(
		'id'	=> 'importForm',
		'cols'	=> array(3,8),
		0 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ajax',
			'value'			=> 'import_groupEmail'
		),
		1 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ID',
			'value'			=> $id
		),
		2 => array(
			'id'			=> 'file_import',
			'func'			=> 'opt_file',
			'type'			=> 'file',
			'key'			=> 'data',
			'label'			=> 'Filename',
			'accept'		=> '.csv',
			'data'			=> ''
		)
	);
	
	$args = array(
		'title'		=> 'Import Daftar Customer',
		'button'	=> '_btn_modal_import',
		'status'	=> array(
			'id'		=> 'importForm',
			'link'		=> 'import_groupEmail',
			'load'		=> 'here_modal'
		)
	);
	
	$args['func'] = array('sobad_form');
	$args['data'] = array($data);
	
	return modal_admin($args);
}

function import_customerEmail($args=array()){
	$fileName = $_FILES["data"]["tmp_name"];
	
	if ($_FILES["data"]["size"] > 0) {
		$db = new kmi_db();
		$grp = new kmi_mail();
        $delimiter = _detectDelimiter($filename);
        $file = fopen($fileName, "r");
        
        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
        	$email = str_replace('"', '', $column[1]);
        	$email = preg_replace('/\s+/', '', $email);
			$email = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $email);

			$colm = explode(";",$email);
        	foreach ($colm as $key => $val) {
				if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
					continue;
				}
			}
			
			$id_user = $_SESSION['kmi_ID'];
			$whr = "user='$id_user' AND email='$email' AND type='2'";
			$q = $grp->get_email_custom($whr,array('ID'));
			
			$check = array_filter($q);
			if(empty($check)){
				$args = array(
					'name'	=> _filter_string($column[0]),
					'email'	=> $email,
					'type'	=> 2
				);	
				$q = $db->_insert_table('email-list',$args);
			}
        }
		
		if($q!==0){
			$title = daftar_table(1,'customer');
			return table_admin($title);
		}
    }
}

function import_groupEmail($args=array()){
	if(!isset($_POST['ID'])){
		return '';
	}
	
	$id_grp = $_POST['ID'];
	$fileName = $_FILES["data"]["tmp_name"];
	
	if ($_FILES["data"]["size"] > 0) {
		$db = new kmi_db();
		$grp = new kmi_mail();
		
		$clist = $grp->get_group($id_grp);
		$clist = explode(',',$clist[0]['id_mail']);
		
        $delimiter = _detectDelimiter($filename);
        $file = fopen($fileName, "r");
		
		$where = "meta_id='$id_grp' AND meta_key='mail_group'";
		$lst_grp = $db->_select_table("WHERE ".$where,'email-group-meta',array('meta_value'));
		
		$list_group = array();
		if($lst_grp!==0){
			$r = $lst_grp->fetch_assoc();
			$list_group = explode(',',$r['meta_value']);
		}
		
        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
        	$email = str_replace('"', '', $column[1]);
        	$email = preg_replace('/\s+/', '', $email);
			$email = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $email);

			$colm = explode(";",$email);
        	foreach ($colm as $key => $val) {
				if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
					continue;
				}
			}
			
			$id_user = $_SESSION['kmi_ID'];
			$whr = "user='$id_user' AND email='$email' AND type='2'";
			$q = $grp->get_email_custom($whr,array('ID'));
			
			$check = array_filter($q);
			if(empty($check)){
				$args = array(
					'name'	=> _filter_string($column[0]),
					'email'	=> $email,
					'type'	=> 2
				);
				$q = $db->_insert_table('email-list',$args);
				$idx = $q;
			}else{
				$idx = $q[0]['ID'];
			}
			
			if(! in_array($idx,$list_group)){
				array_push($clist,$idx);
			}
        }
	
		$arg = array(
			'meta_value'	=> implode(',',$clist)
		);
		
		$q = $db->_update_multiple($where,'email-group-meta',$arg);
		
		if($q!==0){
			return groupEdit_daftar($id_grp);
		}
    }
}

// ----------------------------------------------------------
// Function channel to database -----------------------------
// ----------------------------------------------------------
function _get_list_table($idx,$args=array()){
	if($idx==0){
		$idx = 1;
	}
	
	$type = isset($_POST['type'])?$_POST['type']:'';
	$args = isset($_POST['args'])?ajax_conv_json($_POST['args']):$args;	
	
	$table = daftar_table($idx,$type,true,$args);
	return table_admin($table);
}

function _get_group_table($idx,$args=array()){
	if($idx==0){
		$idx = 1;
	}
	
	$type = isset($_POST['type'])?$_POST['type']:'';
	$args = isset($_POST['args'])?ajax_conv_array_json($_POST['args']):$args;	
	$args = array(
		'words'		=> $args['words'][0],
		'search'	=> $args['search'][0]
	);
	
	$table = groupDaftar_table($type,$idx,true,$args);
	return table_admin($table);
}

function daftar_pagination($idx){
	if($idx==0){
		die('');
	}
	
	return _get_list_table($idx);
}

function groupList_pagination($idx){
	if($idx==0){
		$idx = 1;
	}
	
	return _get_group_table($idx);
}

// funtion Search Meta in Database
function search_daftar($args=array()){
	$args = ajax_conv_json($args);
	
	return _get_list_table(1,$args);
}

function groupListSearch_daftar($args=array()){
	$args = ajax_conv_array_json($args);

	return _get_group_table(1,$args);;
}

// funtion Hapus Meta in Database

function produsenHapus_daftar($id){
	$q = hapus_option($id);
	
	if($q===1){
		return hapus_meta_daftar($id,'produsen');
	}
}

function customerHapus_daftar($id){
	return hapus_meta_daftar($id,'customer');
}

function groupHapus_daftar($id){
	$q = hapus_meta_mail($id);
	
	if($q===1){
		return hapus_meta_daftar($id,'group');
	}
}

function hapus_meta_daftar($id,$type){
	$q = hapus_email($id);
	
	if($q===1){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_list_table($pg);
	}
}

// function View Statistik Customer

function customerView_daftar($id=0){
	$id = str_replace('view_', '', $id);
	intval($id);

	$args = array(
		'title'		=> 'Statistik Email Customer',
		'button'	=> '',
		'status'	=> array()
	);

	$args['func'] = array('customerView_modal','dash_script');
	$args['data'] = array($id,'');

	return modal_admin($args);;
}

// funtion Edit Meta in Database

function produsenEdit_daftar($id){
	return edit_item_daftar($id,'produsen','sobad_portlet');
}

function customerEdit_daftar($id){
	return edit_item_daftar($id,'customer','sobad_portlet');
}

function groupEdit_daftar($id){
	return edit_item_daftar($id,'group','sobad_portlet');
}

function edit_item_daftar($id,$type,$load='',$func='',$tp=0){
	$id = str_replace('edit_','',$id);
	intval($id);
	
	$args = array(
		'ID',
		'name',
		'email',
		'note'
	);
	
	$meta = new kmi_mail();
	if($type!='group'){
		$q = $meta->get_email($id,$args);
	}else{
		$q = $meta->get_group($id,$args);
	}
	
	if($q===0){
		return '';
	}
	
	$q[0]['mail_pass'] = '';
	$q[0]['mail_host'] = '';
	$q[0]['mail_secure'] = 1;
	
	$q2 = $meta->get_option($id);
	
	if($q2!==0){
		foreach($q2 as $key => $val){
			$idx = $val['meta_key'];
			$q[0][$idx] = $val['meta_value'];
		}
	}
	
	return daftar_edit_form($q[0],$type,$load,$func,$tp);
}

// funtion Update Meta in Database

function produsenUpdate_daftar($args){
	return update_meta_daftar($args,'produsen');
}

function customerUpdate_daftar($args){
	return update_meta_daftar($args,'customer');
}

function groupUpdate_daftar($args){
	return update_meta_daftar($args,'group');
}

function update_meta_daftar($_args=array(),$type,$callback='',$dt_back=0){
	$args = ajax_conv_json($_args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['ajax'])){
		unset($args['ajax']);
	}
	
	if(isset($args['search'])){
		unset($args['search']);
		unset($args['words']);
	}
	
	if(isset($args['attachment']))unset($args['attachment']);

	switch($type){
		case 'produsen':
			$tp = 1;
			break;
		case 'customer':
			$tp = 2;
			break;
		case 'group':
			$tp = 4;
			break;
		default:
			$tp = 0;
			break;	
	}	
	
	$db = new kmi_db();
	
	// check email lain... apakah sudah ada?
	$id_user = $_SESSION['kmi_ID'];
	$email = isset($args['email'])?$args['email']:strtolower($args['name'].'@group.kmi');
	$where = "WHERE email='$email' AND user='$id_user' AND type='$tp'";
	$mail = $db->_select_table($where,'email-list',array('ID'));

    if($mail!==0){
    	$mail = $mail->fetch_assoc();
    	if($mail['ID']!=$id){
    		$err = new _error();
    		$err = $err->_alert_db("Email Sudah ada");
    		die($err);
    	}
    }
	
	// set to option
	if($type=='produsen'){
		// tampung di option
		$opts = array();
		$opts['mail_pass'] = str_replace('-plus-','+',$args['mail_pass']);
		$opts['mail_host'] = $args['mail_host'];
		$opts['mail_secure'] = $args['mail_secure'];
	
		// hapus di argument
		unset($args['mail_pass']);
		unset($args['mail_host']);
		unset($args['mail_secure']);
	}
	
	if($type=='group'){
		$group = ajax_conv_array_json($_args);
		$args['email'] = $email;
		unset($args['mail_group']);
	}

	$mail = new kmi_mail();
	$q = $db->_update_table($id,'email-list',$args);
	
	if($type=='produsen'){
		foreach($opts as $key => $val){
			$where = "WHERE meta_id='$id' AND meta_key='$key'";
			$idx = $mail->get_options($where,array('ID'));
			
			if($key=='mail_pass'){
				$val = kmi_encrypt($val);
			}
			
			if($idx!==0){
				// if option sudah ada
					$opt['meta_value'] = $val;		
					$q = $db->_update_table($idx[0]['ID'],'email-option',$opt);
			}else{
				// if option sudah ada
				
				$opt = array(
					'meta_id'		=> $id,
					'meta_key'		=> $key,
					'meta_value'	=> $val
				);
				
				$q = $db->_insert_table('email-option',$opt);
			}
		}
	}
	
	if($type=='group'){
		$group = implode(',',$group['mail_group']);

		$grp = array();
		$grp['meta_value'] = $group;
		
		$where = "WHERE meta_id='$id'";
		$q = $db->_select_table($where,'email-group-meta',array('ID'));
		$r = $q->fetch_assoc();
		$q = $db->_update_table($r['ID'],'email-group-meta',$grp);
	}
	
	if($q===1){
		if(is_callable($callback)){
			return $callback($dt_back);
		}else{
			$title = daftar_table(1,$type);
			return table_admin($title);
		}
	}
}

// funtion Tambah Meta in Database

function tambah_produsenEmail($args){
	return tambah_meta_daftar('produsen',$args);
}

function tambah_customerEmail($args){
	return tambah_meta_daftar('customer',$args);
}

function tambah_groupEmail($args){
	return tambah_meta_daftar('group',$args);
}

function tambah_meta_daftar($type,$_args=array()){
	$args = ajax_conv_json($_args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['ajax'])){
		unset($args['ajax']);
	}
	
	if(isset($args['search'])){
		unset($args['search']);
		unset($args['words']);
	}
	
	if(isset($args['attachment']))unset($args['attachment']);

	switch($type){
		case 'produsen':
			$tp = 1;
			break;
		case 'customer':
			$tp = 2;
			break;
		case 'group':
			$tp = 4;
			break;
		default:
			$tp = 0;
			break;	
	}
	
	// check email... apakah sudah ada?
	$db = new kmi_db();
	
	$id_user = $_SESSION['kmi_ID'];
	$email = isset($args['email'])?$args['email']:strtolower($args['name'].'@group.kmi');
	$where = "WHERE email='$email' AND user='$id_user' AND type='$tp'";
	$mail = $db->_select_table($where,'email-list',array('ID'));

	if($mail!==0){
		$err = new _error();
		$err = $err->_alert_db("Email Sudah ada");
		die($err);
	}
	
	// set to option
	if($type=='produsen'){
		// tampung di option
		$opts = array();
		$opts['mail_pass'] = str_replace('-plus-','+',$args['mail_pass']);
		$opts['mail_host'] = $args['mail_host'];
		$opts['mail_secure'] = $args['mail_secure'];
	
		// hapus di argument
		unset($args['ajax']);
		unset($args['mail_pass']);
		unset($args['mail_host']);
		unset($args['mail_secure']);
	}
	
	if($type=='group'){
		$group = ajax_conv_array_json($_args);
		$args['email'] = $email;
		unset($args['mail_group']);
	}
	
	// get type
	$types = new kmi_mail();
	$types = $types->get_types();
	
	foreach($types as $key => $t){
		if($t['name']==$type){
			$code = $t['ID'];
		}
	}
	
	$args['type'] = $code;
	
	// tambah data	
	$q = $db->_insert_table('email-list',$args);
	
	if($type=='produsen'){
		$idx = $q;
		foreach($opts as $key => $val){
			if($key=='mail_pass'){
				$val = kmi_encrypt($val);
			}
			
			$opt = array(
				'meta_id'		=> $idx,
				'meta_key'		=> $key,
				'meta_value'	=> $val
			);
			
			$q = $db->_insert_table('email-option',$opt);
		}
	}
	
	if($type=='group'){
		$grp = array();
		
		$group = 0;
		if(isset($group['mail_group'])){
			$group = implode(',',$group['mail_group']);
		}
		
		$grp['meta_id'] = $q;
		$grp['meta_key'] = 'mail_group';
		$grp['meta_value'] = $group;
		
		$q = $db->_insert_table('email-group-meta',$grp);
	}
	
	if($q!==0){
		$title = daftar_table(1,$type);
		return table_admin($title);
	}
}

// ------------------------------------------------------
// -- Diagram Statistik ---------------------------------
// ------------------------------------------------------

function customerView_modal($id=0){
	$dough = data_customer_mail_dough($id);
	$tabel = data_customer_mail_year($id);

	?>
		<div class="row">
			<?php sobad_chart($dough) ;?>
			<?php sobad_chart($tabel) ;?>
		</div>
	<?php
}

function data_customer_mail_dough($id=0,$style=''){
	$send = new kmi_send();
	$sMail = $send->get_log_send("meta_mail='$id' AND status IN ('2','3','4','5')");
	$sMail = count($sMail);

	$omset = 'Jumlah Pengiriman '.$sMail;

	$chart[] = array(
		'func'	=> '_site_load',
		'data'	=> array(
			'id'		=> 'cust_mail_dough',
			'func'		=> 'dash_send_mail_dough',
			'status'	=> $style,
			'col'		=> 4,
			'label'		=> $omset,
			'type'		=> $id
		),
	);
	
	return $chart;
}

function data_customer_mail_year($id=0,$style=''){
	$omset = 'Grafik Tahun '.date('Y');

	$chart[] = array(
		'func'	=> '_site_load',
		'data'	=> array(
			'id'		=> 'cust_mail_year',
			'func'		=> 'dash_chart_omset_year',
			'status'	=> $style,
			'col'		=> 8,
			'label'		=> $omset,
			'type'		=> $id
		),
	);
	
	return $chart;
}

function dash_send_mail_dough($id=0){
	$id = isset($_POST['type'])?$_POST['type']:0;
	$year = date('Y');$month = date('m');$day = date('d');
	$days = array();

// --------------- Create Data Chart
	$send = new kmi_send();
	$sMail = $send->get_log_send("meta_mail='$id' AND status IN ('3','4','5')");
	$fMail = $send->get_log_send("meta_mail='$id' AND status='2'");
	$rMail = $send->get_log_send("meta_mail='$id' AND status IN ('4','5')");
	$cMail = $send->get_log_send("meta_mail='$id' AND status='5'");

	$sMail = count($sMail);$fMail = count($fMail);
	$rMail = count($rMail);$cMail = count($cMail);
	$nMail = $sMail - $rMail;

	$label = array('Belum di Read','Gagal Terkirim','Email Terbaca','Email di Click Link');
	$type = array($nMail,$fMail,$rMail,$cMail);
	$color = array(6,2,4,5);
	$idx = 0;

	foreach($type as $key => $val){
		if($val>0){
			$data[0]['data'][$idx] = $val;
			$types[$idx] = $label[$key];

			$data[0]['bgColor'][$idx] = get_chartColor($color[$key],0.8);
			$data[0]['brdColor'][$idx] = 'rgba(256,256,256,1)';
			$idx += 1;
		}
	}

	if(empty($sMail)){	
		$data[0]['data'][0] = 1;
		$types[0] = 'Unknown';

		$data[0]['bgColor'] = 'rgba(256,256,256,1)';
		$data[0]['brdColor'] = get_chartColor();
	}	

	$data[0]['label'] = 'Type';
	$data[0]['type'] = 'doughnut';

	$args = array(
		'type'		=> 'doughnut',
		'label'		=> $types,
		'data'		=> $data,
		'option'	=> '_option_omset_doughnut'
	);
	
	return $args;
}

function dash_chart_omset_year($id=0){
	$year = date('Y');
	$id = isset($_POST['type'])?$_POST['type']:0;
	$months = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');

// --------------- Create Data Chart
	$send = new kmi_send();

	$label = array('Email Terbaca','Email di Click Link','Belum di Read');
	$color = array(4,5,6,0);
	$idx = 0;
	for($i=0;$i<12;$i++) {
		$month = sprintf("%02d",$i+1);
		$tanggal = "AND YEAR(meta_date)='$year' AND MONTH(meta_date)='$month'";

		$sMail = $send->get_log_send("meta_mail='$id' AND status IN ('3','4','5') $tanggal");
		//$fMail = $send->get_log_send("meta_mail='$id' AND status='2' $tanggal");
		$rMail = $send->get_log_send("meta_mail='$id' AND status IN ('4','5') $tanggal");
		$cMail = $send->get_log_send("meta_mail='$id' AND status='5' $tanggal");		

		$sMail = count($sMail);
		$rMail = count($rMail);
		$cMail = count($cMail);
		$nMail = $sMail - $rMail;

		$data[0]['data'][$i] = $rMail;
		$data[1]['data'][$i] = $cMail;
		$data[2]['data'][$i] = $nMail;
		$data[3]['data'][$i] = $sMail;
	}

	$data[0]['label'] = 'Read';
	$data[1]['label'] = 'Click';
	$data[2]['label'] = 'No Read';
	$data[3]['label'] = 'Send';

	$data[0]['type'] = 'bar';
	$data[1]['type'] = 'bar';
	$data[2]['type'] = 'bar';
	$data[3]['type'] = 'line';

	$jml = 4;
	for($i=0;$i<$jml;$i++){
		$data[$i]['bgColor'] = get_chartColor($color[$i],0.5);
		$data[$i]['brdColor'] = get_chartColor($color[$i]);
	}

	$args = array(
		'type'		=> 'bar',
		'label'		=> $months,
		'data'		=> $data,
		'option'	=> '_option_omset_bar'
	);
	
	return $args;
}