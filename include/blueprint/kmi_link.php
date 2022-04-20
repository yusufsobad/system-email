<?php

class kmi_link extends _class{

	public static $table = 'email-link';

	public static function blueprint(){
		$args = array(
			'type'		=> 'link',
			'table'		=> self::$table,
		);

		return $args;
	}
}