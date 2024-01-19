<?php 
//	ini_set('display_errors', 1);
//	ini_set('display_startup_errors', 1);
//	error_reporting(E_ALL);

session_start();

define('AUTHPATH',$_SERVER['SERVER_NAME']);

require 'config/hostname.php';
require 'config/notification.php';

// Get Define
new hostname();

// get file component
new _component();

$role = $_SESSION[_prefix.'page'];

// include pages
$asset = new sobad_asset();
$asset->_pages("../coding/_pages/");

$pages = new sobad_page($role);
$pages->_get();

// Check Notification

$data_notif = isset($_POST['data']) ? $_POST['data'] : '';
$data_notif = explode('#', $data_notif);

$id_notif = $data_notif[0];
$post_id = $data_notif[1];

$id_user = get_id_user();

$status = $break = false;
$msg = $title = $link = $content = '';
$type = 0;
$icon = 'toast toast-info ';

if(!empty($id_notif)){
	foreach ($notify as $key => $value) {
		if($key == $id_notif){
			foreach ($value as $ky => $val) {
				if($role == $val['role']){
					if(!isset($val['user'][0])){
						$status = true;
					}else{
						$status = in_array($id_user, $val['user']) ? true : false;
					}

					$icon .= isset($val['icon']) && !empty($val['icon']) ? $val['icon'] : '';
					$msg = $content = $val['message'] ?? '';
					$title = $val['title'] ?? '';
					$type = $val['type'] ?? 0;
					$post_id = $val['post_id'] ?? $post_id;

					if(isset($val['link']) && !empty($val['link'])){
						$link = $val['link'];
						$newtab = isset($val['newtab']) && !empty($val['newtab']) ? $val['newtab'] : false;

						$newtab = $newtab ? 'target="_blank"' : '';
						$msg = '<a href="'.$val['link'].'" '.$newtab.'> '.$msg.' </a>';
					}

					$break = true;
					break;
				}
			}
		}

		if($break){
			break;
		}
	}

	if($status){
		$where = "AND `".base . "notify`.notify_id='$id_notif' AND `".base . "notify`.post_id='$post_id' AND `".base . "notify`.user='$id_user' AND `".base . "notify`.department='$role'";

		$check = sobad_notify::get_all(['ID'],$where);
		if(!isset($check[0])){
			sobad_db::_insert_table(base . 'notify',[
				'post_id'	=> $post_id,
				'user'		=> $id_user,
				'department'=> $role,
				'content'	=> $content,
				'status'	=> 1,
				'type'		=> $type,
				'link'		=> $link,
				'icon'		=> $icon,
				'notify_id'	=> $id_notif
			]);
		}else{
			sobad_db::_update_multiple("notify_id='$id_notif' AND post_id='$post_id'",base . 'notify',[
				'post_id'	=> $post_id,
				'date'		=> date('Y-m-d H:i:s'),
				'content'	=> $content,
				'status'	=> 1,
				'type'		=> $type,
				'link'		=> $link,
				'icon'		=> $icon,
			]);
		}
	}
}

// Check notif in menu
$bell = sobad_notification::_notification();
$child = sobad_notification::_get();

ob_start();
theme_layout('_conv_content_notification',$msg);
$msg = ob_get_clean();

$ajax = array(
	'icon'			=> $icon,
	'notify' 		=> $status,
	'msg'    		=> $msg,
	'title'			=> $title,
	'bell_notify'	=> $bell,
	'menu_notify'	=> $child
);

$ajax = json_encode($ajax);		
print_r($ajax);

class sobad_notification{
	public static function _notification(){
		$role = $_SESSION[_prefix.'page'];
		$id_user = get_id_user();

		$where = "AND `".base . "notify`.status='1' ORDER BY date DESC";
		$notif = sobad_notify::get_all([],$where);

		ob_start();
		theme_layout('_notification',$notif);
		$notif_html = ob_get_clean();

		return [
			'status'	=> isset($notif[0]) ? true : false,
			'qty'		=> count($notif),
			'data'		=> $notif_html
		];
	}

	public static function _get(){
		global $reg_sidebar;

		return self::_child($reg_sidebar);
	}

	public static function _child($args=[]){
		$data = [];

		foreach ($args as $key => $val) {
			$val['id'] = $child['id'] ?? 'mn_' . $key;
			$val['notify'] = $child['notify'] ?? 0;

			if($val['child']!=null){
				$child = self::_child($val['child']);
				$val['child'] = $child;

				foreach ($child as $ky => $vl) {
					$val['notify'] += $vl['notify'];
				}

			}else{
				$func = $val['func'];

				if(isset($val['loc'])){
					$loc = empty($val['loc'])?$func:$val['loc'].'.'.$func;
					sobad_asset::_loadFile($loc);
				}
		
				if(class_exists($func)){
					if(is_callable(array($func,'_notify'))){	
						$val['notify'] = $func::_notify($data);
					}
				}
			}

			$data[$key] = $val;
		}

		return $data;
	}
}