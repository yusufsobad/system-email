<?php

function _valid_mail($email=''){
    return filter_var($email,FILTER_VALIDATE_EMAIL);
}

function _filter_string($str=''){
    return filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
}

function re_log_meta($id=0){  
    $where = "meta_id='$id'";

    $log = kmi_send::get_log_meta($id,''); 
    $q = sobad_db::_update_multiple($where,'email-log-meta',array('meta_id' => 0,'log' => $id));
    
    foreach($log as $key => $val){
        unset($val['email_meta']);
        unset($val['name_meta']);
        unset($val['place_meta']);
        unset($val['type_meta']);
        unset($val['ID']);
        
        $q = sobad_db::_insert_table('email-log-meta',$val);
    }
    
    return $q;
}

function sobad_save_file($url='',$txt=''){
    $err = _error::_alert_db("Unable to open file!");
    
    $myfile = fopen($url, "w") or die($err);
    fwrite($myfile, $txt);
    fclose($myfile);
}

function kmi_encrypt( $q='' ) {
    $cryptKey  = 'qJB0rGtInG03efyCp';
    $qEncoded      = $q;//base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}

function kmi_decrypt( $q='' ) {
    $cryptKey  = 'qJB0rGtInG03efyCp';
    $qDecoded      = $q;//rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
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