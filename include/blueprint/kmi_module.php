<?php

class kmi_module extends _class{

	public static $table = 'email-module';

	public static function blueprint(){
		$args = array(
			'type'		=> 'module',
			'table'		=> self::$table,
		);

		return $args;
	}
}