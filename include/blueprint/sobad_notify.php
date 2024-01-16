<?php

class sobad_notify extends _class{
	
	public static $table = ''. base .'notify';

	public static function blueprint(){
		$args = array(
			'type'	=> 'notify',
			'table'	=> self::$table,
		);

		return $args;
	}
}