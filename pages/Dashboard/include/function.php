<?php
// include function external ----------
require 'layout_admin.php';

// ------------------------------------
// ---------- List Function -----------
// ------------------------------------
function like_pencarian($args=array(),$cari=array(),$whr=''){
	$kata = '';
	$where = '';
	$search = $cari['search'];
	$src = array();
	
	if(!empty($cari['words'])){
		if($search==0){
			$search = implode(',',$args);
			$kata = $cari['words'];
			
			foreach($args as $key => $val){
				$src[] = "$val LIKE '%$kata%' ".$whr;
			}
				
			$src = implode(" OR ",$src);
			$where = "AND ".$src." ";
		}else{
			$search = $args[$search];
			$kata = $cari['words'];
			$where = "AND $search LIKE '%$kata%' ".$whr;
		}
	}else{
		$where = $whr;
	}
	
	return array($where,$kata);
}

function convToOption($args=array(),$id,$value){
	$check = array_filter($args);
	if(empty($check)){
		return array();
	}
	
	$option = array();
	foreach($args as $key => $val){
		$option[$val[$id]] = $val[$value];
	}
	
	return $option;
}

function sobad_save_file($url,$txt){
	$err = new _error();
	$err = $err->_alert_db("Unable to open file!");
	
	$myfile = fopen($url, "w") or die($err);
	fwrite($myfile, $txt);
	fclose($myfile);
}

function hapus_email($id){
	$id = str_replace('del_','',$id);
	intval($id);
	
	$meta = new kmi_db();
	$q = $meta->_delete_single($id,'email-list');
	
	return $q;
}

function hapus_tmplate($id){
	$id = str_replace('del_','',$id);
	intval($id);
	
	$tmpl = new kmi_template();
	$meta = new kmi_db();
	
	$q = $tmpl->get_template($id,array('lokasi','locked'));
	$url = template_url().$q[0]['lokasi'];
	if($q[0]['locked']!=1){
		unlink($url) or $q=0; // hapus file lokasi
	
		if($q!==0){
			$q = $meta->_delete_single($id,'email-template');
		}
	
		return $q;
	}
	
	return 0;
}

function hapus_option($id){
	$id = str_replace('del_','',$id);
	intval($id);
	
	$where = "meta_id='$id'";
	
	$meta = new kmi_db();
	$q = $meta->_delete_multiple($where,'email-option');
	
	return $q;
}

function hapus_meta_mail($id){
	$id = str_replace('del_','',$id);
	intval($id);
	
	$where = "meta_id='$id'";
	
	$meta = new kmi_db();
	$q = $meta->_delete_multiple($where,'email-group-meta');
	
	return $q;
}

function hapus_log_send($id){
	$id = str_replace('del_','',$id);
	intval($id);
	
	$where = "meta_id='$id'";

	$meta = new kmi_db();
	$q = $meta->_update_multiple($where,'email-log-meta',array('meta_id' => 0,'log' => $id));
	$q = $meta->_update_table($id,'email-log',array('trash' => 1));
	
	return $q;
}

function re_log_meta($id){	
	$where = "meta_id='$id'";

	$meta = new kmi_send();
	$log = $meta->get_log_meta($id,'');	
	$q = $meta->_update_multiple($where,'email-log-meta',array('meta_id' => 0,'log' => $id));
	
	foreach($log as $key => $val){
		unset($val['email']);
		unset($val['name']);
		unset($val['ID']);
		
		$q = $meta->_insert_table('email-log-meta',$val);
	}
	
	return $q;
}

function hapus_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['load'] = 'sobad_portlet';
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function page_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['load'] = 'here_content';
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function print_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['load'] = 'sobad_preview';
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function email_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function edit_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = 'modal';
	$val['load'] = 'here_modal';
	$val['href'] = '#myModal';
	$val['spin'] = false;
	
	return buat_button($val);
}

function apply_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = 'modal';
	$val['load'] = 'here_modal2';
	$val['href'] = '#myModal2';
	
	return buat_button($val);
}

function edit_button_custom($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	return buat_button($val);
}

function editable_click($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$edit = '<a href="javascript:;" id="'.$args['key'].'" class="edit_input_txt" data-type="text" data-sobad="'.$args['func'].'" data-name="'.$args['name'].'" data-title="'.$args['title'].'" class="editable editable-click">'.$args['label'].'</a>';
	
	return $edit;
}

function editable_value($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$edit = '<input type="'.$args['type'].'" name="'.$args['key'].'" value="'.$args['value'].'" '.$args['status'].'>';
	
	return $edit;
}

function buat_button($val=array()){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	} 
	
	$status = '';
	if(isset($val['status'])){
		$status = $val['status'];
	}

	$type = '';
	if(isset($val['type'])){
		$type = $val['type'];
	}

	$alert = false;
	if(isset($val['alert'])){
		$alert = $val['alert'];
	}

	$class = 'btn-xs';
	if(isset($val['class'])){
		$class = $val['class'];
	}

	$spin = true;
	if(isset($val['spin'])){
		$spin = $val['spin'];
	}
	
	$onclick = 'sobad_button(this,'.$spin.')';
	if(isset($val['script'])){
		$onclick = $val['script'];
	}
	
	$btn = '
	<a id="'.$val['ID'].'" data-toggle="'.$val['toggle'].'" data-sobad="'.$val['func'].'" data-load="'.$val['load'].'" data-type="'.$type.'" data-alert="'.$alert.'" href="'.$val['href'].'" class="btn '.$class.' '.$val['color'].' btn_data_malika" onclick="'.$onclick.'" '.$status.'>
		<i class="'.$val['icon'].'"></i> '.$val['label'].'
	</a>';
	
	return $btn;
}

function script_chart(){
	?>
	<script>
		$(".chart_malika").ready(function(){
			var ajx = $('.chart_malika').attr('data-sobad');
			var id = $('.chart_malika').attr('data-load');
			
			data = "ajax="+ajx+"&data=2019";
			sobad_ajax(id,data,sobad_chart);
		});
	</script>
	<?php
}

function _check_array($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return 0;
	}
	
	return $args;
}

function _valid_mail($email){
	return filter_var($email,FILTER_VALIDATE_EMAIL);
}

function _filter_string($str){
	return filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
}

function _detectDelimiter($csvFile){
    $delimiters = array(
        ';' => 0,
        ',' => 0,
        "\t" => 0,
        "|" => 0
    );

    $handle = fopen($csvFile, "r");
    $firstLine = fgets($handle);
    fclose($handle); 
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
}

function kmi_hex_toChar($str=''){
	if(empty($str)){
		return '';
	}
	
	$html = '';
	$jml = strlen($str);
	for($i=0;$i<$jml;$i+=2){
		$hex = substr($str,$i,2);
		$html .= chr(hexdec($hex));
	}
	
	$html = urldecode($html);
	$html = str_replace('-plus-','+',$html);
	return $html;
}

function kmi_encrypt( $q ) {
    $cryptKey  = 'qJB0rGtInG03efyCp';
    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}

function kmi_decrypt( $q ) {
    $cryptKey  = 'qJB0rGtInG03efyCp';
    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

function kmi_upload_file($file='',$folder=''){
	$target_file = '../asset/uploads/'.$folder;
	if (move_uploaded_file($file, $target_file)) {
		return 1;
    } else {
        return 0;
    }
}

// ----------------------------------------------
// Function Logout Admin ------------------------
// ----------------------------------------------

function logout_admin(){
	unset($_SESSION['kmi_page']);
	unset($_SESSION['kmi_user']);
	unset($_SESSION['kmi_name']);
	
	return 'index.php';
}