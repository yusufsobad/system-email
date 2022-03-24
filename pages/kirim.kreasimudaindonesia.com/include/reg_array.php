<?php
$sobad_data = array(
	'reg_page'			=> array(),
	'reg_sidebar'		=> array(),
	'reg_script_css'	=> array(),
	'reg_script_js'		=> array(),
	'reg_script_head'	=> array(),
	'reg_script_foot'	=> array(),
	'reg_ajax'			=> array(),
	'reg_exe'			=> ''
);

global_data($sobad_data);

// function registry array
function reg_hook($name,$arr = array()){
	global $sobad_data;
	
	if(isset($sobad_data[$name])){
		if($name=='reg_exe'){
			$sobad_data[$name] = $arr;
		}else{
			foreach($arr as $key => $val){
				$sobad_data[$name][$key] = $val;
			}
		}
	}
	global_data($sobad_data);
}

function global_data($data){
	$GLOBALS['sobad_data'] = $data;
	// data array sidebar
	foreach($data as $key => $val){
		$GLOBALS[$key] = $val;
	}
}

function sobad_getPage($page){
	global $reg_page;
	
	foreach($reg_page as $key => $val){
		if($val['home']==true){
			$reg_page['Home'] = array(
				'page'	=> $val['page'],
				'home'	=> true
			);
		}
	}

	$func = $reg_page[$page]['page'];
	if(is_callable($func)){
		$GLOBALS['reg_page'] = $reg_page;
		$func();
	}else{
		$err = new _error();
		$err->_page404();
	}
}

function sobad_execute(){
	global $reg_exe;
	
	if(is_callable($reg_exe)){
		$reg_exe();
	}else{
		die('');
	}
}