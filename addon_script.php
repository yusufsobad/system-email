<?php
(!defined('DEFPATH'))?exit:'';

abstract class addon_script{

	protected function _js_ckeditor($idx=array()){
		$loc = 'vendor/';
		$js = array(
			'ckeditor'				=> $loc.'ckeditor/ckeditor.js'
		);
		
		$check = array_filter($idx);
		if(!empty($check)){
			foreach($idx as $key){
				$js[$key];
			}
		}
	
		return $js;
	}
}