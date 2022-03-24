<?php
/*
Version 1.1.2
*/

function sobad_name_file($dir){
	if(is_dir($dir)){
		if($handle = opendir($dir)){
			$i = 0;
			while(($file = readdir($handle)) !== false){
				if($file == "."){
					continue;
				}
				if($file == ".."){
					continue;
				}
				
				$list[$i] = $file;
				$i += 1;
			}
			closedir($handle);
			
			return $list;
		}
	}
}

function sobad_js_file(){
	$dir = "asset/js/";
	$list = sobad_name_file($dir);
	if(count($list)>0){
		for($i=0;$i<count($list);$i++){
			echo '<script src="'.$dir.$list[$i].'"></script>';
		}
	}
}

function sobad_css_file(){
	$dir = "asset/css/";
	$list = sobad_name_file($dir);
	if(count($list)>0){
		for($i=0;$i<count($list);$i++){
			echo '<link rel="stylesheet" type="text/css" href="'.$dir.$list[$i].'">';
		}
	}
}

function sobad_url_set(){

	echo '<!-- --custom stylesheet-- -->';
	sobad_css_file();
	echo '<!-- -function javascript- -->';
	sobad_js_file();
}

function vendor_css(){
	global $reg_script_css;
	
	foreach($reg_script_css as $key => $val){
		echo "<!-- $key CSS -->";
		echo '<link rel="stylesheet" type="text/css" href="'.$val.'">';
	}
}

function vendor_js(){
	global $reg_script_js;

	foreach($reg_script_js as $key => $val){
		echo "<!-- $key JS -->";
		echo '<script src="'.$val.'"></script>';
	}
}

function script_head(){
	global $reg_script_head;

	echo "<!-- Script Head Sobad -->";
	foreach($reg_script_head as $key => $val){
		echo $val;
	}
}

function script_foot(){
	global $reg_script_foot;

	echo "<!-- Script Foot Sobad -->";
	foreach($reg_script_foot as $key => $val){
		echo $val;
	}
}

function sobad_pages($dir = "pages/"){
	$pages = sobad_name_file($dir);
	if(count($pages)>0){
		for($i=0;$i<count($pages);$i++){
			if(is_dir($dir.$pages[$i])){
				if(file_exists($dir.$pages[$i]."/index.php")){
					require_once $dir.$pages[$i]."/index.php";
				}else{
					die("halaman gagal dimuat!!!");
				}
			}
		}
	}
}

function ajax_conv_json($args){
	$args = json_decode($args,true);
	$data = array();
	
	if (is_array($args) || is_object($args)){	
		foreach($args as $key => $val){
			$name = stripcslashes($val['name']);
			$data[$name] = stripcslashes($val['value']);
		}
	
		return $data;
	}
	
	return array();
}

function ajax_conv_array_json($args){
	$args = json_decode($args,true);
	$data = array();
	
	if (is_array($args) || is_object($args)){	
		foreach($args as $key => $val){
			$name = stripcslashes($val['name']);
			if(!array_key_exists($name,$data)){
				$data[$name] = array();
			}
			
			array_push($data[$name],stripcslashes($val['value']));
		}
		
		return $data;
	}
	
	return array();
}