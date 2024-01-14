<?php 
//	ini_set('display_errors', 1);
//	ini_set('display_startup_errors', 1);
//	error_reporting(E_ALL);

session_start();

require 'config/hostname.php';
require 'config/notification.php';

// Get Define
new hostname();

// get file component
new _component();

$id_notif = isset($_POST['data']) ? $_POST['data'] : '';
$role = $_SESSION[_prefix.'page'];
$id_user = get_id_user();

$status = false;$msg = '';$break = false;
if(!empty($id_notif)){
	foreach ($notify as $ky => $value) {
		if($key == $id_notif){
			foreach ($value as $key => $val) {
				if($role == $val['role']){
					if(!isset($val['user'][0])){
						$status = true;
					}else{
						$status = in_array($id_user, $val['user']) ? true : false;
					}

					$msg = $val['message'];
					$break = true;
					break;
				}
			}
		}

		if($break){
			break;
		}
	}
}

$ajax = array(
	'notify' 	=> $status,
	'msg'    	=> $msg,
);

$ajax = json_encode($ajax);		
print_r($ajax);