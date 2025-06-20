<?php

require dirname(__FILE__).'/function.php';

class report{
	public static function view($lokasi='',$data=array()){
		$lokasi .= '.php';
		$lokasi = dirname(__FILE__) . '/' .$lokasi;

		if(!file_exists($lokasi)){
			return 'Not Load File !!!';
		}

		extract($data);
		include $lokasi;
	}
}
?>