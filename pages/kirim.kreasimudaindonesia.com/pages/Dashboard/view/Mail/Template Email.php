<?php

// ------------------------------------------------------------------
// ------------- Fiture Template Email ------------------------------
// ------------------------------------------------------------------

function contents_mail(){
	return template_mail(0);
}

function signatures_mail(){
	return template_mail(1);
}

// ------------------------------------------------------------------
// ------------- Function Template ----------------------------------
// ------------------------------------------------------------------

function template_url(){
	return "../asset/template/";
}

function _conv_type_mail($val=0){
	switch ($val) {
		case 0:
			$tp = 'Contents';
			break;
		
		case 1:
			$tp = 'Signatures';
			break;

		default:
			$tp = '';
			break;
	}

	return $tp;
}

function template_head_title($type=0){
	$type = _conv_type_mail($type);
	$typ = strtolower($type);

	$args = array(
		'title'	=> $type.' Email<small>data '.$typ.' email</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> $typ.'_mail',
				'label'	=> $type
			)
		),
		'date'	=> false
	);
	
	return $args;
}

function view_template_head_title($type=0){
	$type = _conv_type_mail($type);
	$typ = strtolower($type);

	$args = array(
		'title'	=> 'Edit Email<small>edit '.$typ.' email</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> $typ.'_mail',
				'label'	=> $type
			),
			1	=> array(
				'func'	=> 'view_template',
				'label'	=> 'View Email'
			)
		),
		'date'	=> false
	);
	
	return $args;
}

// ----------------------------------------------------------
// Layout Send Mail  ----------------------------------------
// ----------------------------------------------------------
function template_mail($val=0){
	return template_layout(1,$val);
}

function template_table($start=1,$type=0,$search=false,$cari=array()){
	$data = array();
	$args = array('ID','name','date','locked','user');
	
	$kata = '';$where = "AND type='$type' ";
	if($search){
		$src = like_pencarian($args,$cari,$where);
		$cari = $src[0];
		$where = $src[0];
		$kata = $src[1];
	}else{
		$cari=$where;
	}
	
	$limit = 'LIMIT '.intval(($start - 1) * 10).',10';
	$where .= $limit;
	$send = new kmi_template();
	$args = $send->_get_templates($args,$where);
	$sum_data = $send->_get_templates(array('ID'),$cari);
	
	$data['data'] = array('search_template',$kata,$type);
	$data['search'] = array('Semua','nama','type');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'template_pagination',
			'type'		=> $type
		)
	);
	
	foreach($args as $key => $val){
		$id_meta = $val['ID'];
		$save = array(
			'ID'	=> 'save_'.$id_meta,
			'func'	=> 'save_template',
			'color'	=> 'green',
			'icon'	=> 'fa fa-save',
			'label'	=> 'Save',
			'type'	=> $type
		);

		$edit = array(
			'ID'	=> 'edit_'.$id_meta,
			'func'	=> 'edit_template',
			'color'	=> 'blue',
			'icon'	=> 'fa fa-edit',
			'label'	=> 'edit',
			'type'	=> $type
		);

		$status = '';
		if($val['user']==0){
			$status = 'disabled';
			$sts = '<i class="fa fa-circle" style="color:#cb5a5e"></i>';
			$button = hapus_button($save);
		}else{
			$sts = '<i class="fa fa-circle" style="color:#26a69a"></i>';
			$button = page_button($edit);
		}		
		
		$view = array(
			'ID'	=> 'view_'.$id_meta,
			'func'	=> 'view_template',
			'color'	=> 'yellow',
			'icon'	=> 'fa fa-eye',
			'label'	=> 'view',
			'type'	=> $type,
			'status'=> $status
		);
		
		$status = '';
		if($val['locked']==1){
			$status = 'disabled';
		}
		
		$hapus = array(
			'ID'	=> 'del_'.$id_meta,
			'func'	=> 'hapus_template',
			'color'	=> 'red',
			'icon'	=> 'fa fa-trash',
			'label'	=> 'hapus',
			'status'=> $status,
			'type'	=> $type
		);
		
		$datetime = strtotime($val['date']);
		$date = format_date_id($val['date']);
		
		$data['table'][$key]['tr'] = array('');
		$data['table'][$key]['td'] = array(
			'title'		=> array(
				'left',
				'auto',
				$val['name'],
				true
			),
			'tanggal'	=> array(
				'center',
				'25%',
				$date,
				true
			),
			'status'	=> array(
				'center',
				'10%',
				$sts,
				true
			),
			'Edit'			=> array(
				'center',
				'10%',
				$button,
				false
			),
			'view'			=> array(
				'center',
				'10%',
				page_button($view),
				false
			),
			'hapus'			=> array(
				'center',
				'10%',
				hapus_button($hapus),
				false
			)
			
		);
	}
	
	return $data;
}

function template_layout($start,$type){
	$data = template_table($start,$type);
	$typ = _conv_type_mail($type);

	$box = array(
		'label'		=> 'Data '.$typ.' email',
		'tool'		=> '',
		'action'	=> 'template_action',
		'in_act'	=> $type,
		'func'		=> 'sobad_table',
		'data'		=> $data
	);
	
	$opt = array(
		'title'		=> template_head_title($type),
		'style'		=> '',
		'script'	=> array('')
	);
	
	return portlet_admin($opt,$box);
}

function view_template_layout($id=0,$type=0){
	$box = array(
		'label'		=> 'view email',
		'tool'		=> '',
		'action'	=> '',		
		'func'		=> 'sobad_view_template',
		'data'		=> $id
	);
	
	$opt = array(
		'title'		=> view_template_head_title($type),
		'style'		=> '',
		'script'	=> array('')
	);
	
	return portlet_admin($opt,$box);
}

function sobad_view_template($id=0){
	$args = array('ID','lokasi');
	
	$meta = new kmi_template();
	$q = $meta->get_template($id,$args);

	$html = '';
	if(!empty($q[0]['lokasi'])){
		$url = template_url().$q[0]['lokasi'];
		
		ob_start();
		include $url;
		$html = ob_get_clean();
	}

	print($html);
}

function template_action($type=0){	
	$add = array(
		'ID'	=> 'add_0',
		'func'	=> 'template_add_form',
		'color'	=> 'btn-default',
		'icon'	=> 'fa fa-plus',
		'label'	=> 'Tambah',
		'type'	=> $type
	);
	
	return page_button($add);
}

function sobad_form_template($args=array()){	
	$data = $args['data'];
	$btn = $args['button'];

	?>
		<div class="row">
			<?php echo sobad_form($data) ;?>
			<div id="button_save_content" class="col-md-12" style="text-align:right;">
				<?php echo _btn_modal_save($btn) ;?>
			</div>	
		</div>
		<script>
			var domain_url = 'vendor';
			CKEDITOR.replace( 'editor_text',{
				// Link dialog, "Browse Server" button
				filebrowserBrowseUrl : domain_url+'/ckfinder/ckfinder.html',
				// Image dialog, "Browse Server" button
				filebrowserImageBrowseUrl : domain_url+'/ckfinder/ckfinder.html?type=Images',
				// Flash dialog, "Browse Server" button
				filebrowserFlashBrowseUrl : domain_url+'/ckfinder/ckfinder.html?type=Flash',
				// Upload tab in the Link dialog
				filebrowserUploadUrl : domain_url+'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
				// Upload tab in the Image dialog
				filebrowserImageUploadUrl : domain_url+'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
				// Upload tab in the Flash dialog
				filebrowserFlashUploadUrl : domain_url+'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
			} );
		</script>
	<?php
}

// ----------------------------------------------------------
// Form data send mail --------------------------------------
// ----------------------------------------------------------
function template_add_form(){
	$type = $_POST['type'];
	$vals = array(0,'','',$type,0);
	
	$args = array(
			'link'		=> 'tambah_template',
			'load'		=> 'here_content',
			'type'		=> $type
		);
	
	return template_data_form($args,$vals,$type);
}

function template_edit_form($vals=array()){
	$check = array_filter($vals);
	if(empty($check)){
		return '';
	}

	$vals = array(
		$vals['ID'],
		$vals['name'],
		$vals['lokasi'],
		$vals['type'],
		$vals['locked']
	);

	$args = array(
		'link'		=> 'update_template',
		'load'		=> 'here_content',
		'type'		=> $vals[3]
	);
	
	return template_data_form($args,$vals,$vals[3]);
}

function template_data_form($args=array(),$vals=array(),$type=0){
	$form = get_template_data_form($args,$vals,$type);

	$box = array(
		'label'		=> 'template email',
		'func'		=> 'sobad_form_template',
		'data'		=> $form
	);

	$opt = array(
		'title'		=> view_template_head_title($type),
		'style'		=> '',
		'script'	=> array('')
	);
	
	return portlet_admin($opt,$box);
}

function get_template_data_form($args=array(),$vals=array(),$type=0){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$status = '';
	if($vals[4]==1){
		$status = 'disabled';
	}
	
	$html = '';
	if(!empty($vals[2])){
		$url = template_url().$vals[2];
		
		ob_start();
		include $url;
		$html = ob_get_clean();
	}

	$data = array(
		0 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ID',
			'value'			=> $vals[0]
		),
		1 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'lokasi',
			'value'			=> $vals[2]
		),
		2 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'type',
			'value'			=> $vals[3]
		),
		3 => array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'name',
			'label'			=> 'title',
			'class'			=> 'input-circle',
			'value'			=> $vals[1],
			'data'			=> 'placeholder="title"'
		),
		4 => array(
			'func'			=> 'opt_textarea',
			'id'			=> 'editor_text',
			'key'			=> 'html',
			'class'			=> '',
			'rows'			=> 60,
			'value'			=> $html
		)
	);
	
	$form = array(
		'data'		=> $data,
		'button'	=> $args
	);

	return $form;
}

// ----------------------------------------------------------
// View data Template mail ----------------------------------
// ----------------------------------------------------------

function view_template($id=0){
	$id = str_replace('view_', '', $id);
	intval($id);

	$tp = isset($_POST['type'])?$_POST['type']:'';
	return view_template_layout($id,$tp);
}

// ----------------------------------------------------------
// Function send mail to database ---------------------------
// ----------------------------------------------------------
function _get_template_table($idx,$args=array()){
	if($idx==0){
		$idx=1;
	}

	$tp = isset($_POST['type'])?$_POST['type']:'';
	$args = isset($_POST['args'])?ajax_conv_json($_POST['args']):$args;		
	
	$table = template_table($idx,$tp,true,$args);
	return table_admin($table);
}

function template_pagination($idx){
	return _get_template_table($idx);
}

function search_template($args=array()){
	$args = ajax_conv_json($args);

	return _get_template_table(1,$args);
}

function hapus_template($id){
	$q = hapus_tmplate($id);
	
	if($q===1){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_template_table($pg);
	}
}

function save_template($id){
	$id = str_replace('save_', '', $id);
	intval($id);

	$id_user = $_SESSION['kmi_ID'];

	$db = new kmi_db();
	$q = $db->_update_table($id,'email-template',array('user' => $id_user));
	
	if($q===1){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_template_table($pg);
	}
}

function edit_template($id){
	$id = str_replace('edit_','',$id);
	intval($id);
	
	$args = array(
		'ID',
		'name',
		'lokasi',
		'type',
		'locked'
	);
	
	$meta = new kmi_template();
	$q = $meta->get_template($id,$args);
	
	if(count($q)===0){
		return '';
	}

	return template_edit_form($q[0]);
}

function update_template($args=array(),$callback='',$dt_call=''){
	$args = ajax_conv_json($args);
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		$src = array(
			'search'	=> $args['search'],
			'words'		=> $args['words']
		);

		unset($args['search']);
		unset($args['words']);
	}
	
	if(isset($args['lokasi'])){
		$url = template_url().$args['lokasi'];
		$html = kmi_hex_toChar($args['ckeditor']);
		sobad_save_file($url,$html);

		unset($args['html']);
		unset($args['ckeditor']);
	}
	
	if(isset($args['type'])){
		if($args['type']==1){
			$args['reff'] = 0;
		}
	}
	
	$db = new kmi_db();
	$q = $db->_update_table($id,'email-template',$args);
	
	if($q!==0){
		if(is_callable($callback)){
			return $callback($dt_call);
		}else{
			$tp = isset($_POST['type'])?$_POST['type']:1;
			return template_mail($tp);
		}
	}
}

function tambah_template($args=array()){
	$args = ajax_conv_json($args);
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		unset($args['search']);
		unset($args['words']);
	}	
	
	if(isset($args['lokasi'])){
		$i = 1;
		$args['lokasi'] = 'email/'.$args['name'].'.php';
		
		cek_file:
		$url = template_url().$args['lokasi'];
		if(is_file($url)){
			$args['lokasi'] = 'email/'.$args['name'].'_'.$i.'.php';
			goto cek_file;
		}
		
		fopen($url,'w'); // buat file
		$html = kmi_hex_toChar($args['ckeditor']);
		sobad_save_file($url,$html);

		unset($args['html']);
		unset($args['ckeditor']);
	}
	
	if(isset($args['type'])){
		if($args['type']==1){
			$args['reff'] = 0;
		}
	}
	
	$db = new kmi_db();
	$q = $db->_insert_table('email-template',$args);
	
	if($q!==0){
		$tp = isset($_POST['type'])?$_POST['type']:1;
		return template_mail($tp);
	}
}