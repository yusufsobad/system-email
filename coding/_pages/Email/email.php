<?php

require 'include/function.php';

class blast_email{
	public static function _reg($status=false){
		$GLOBALS['body'] = 'page-header-fixed';
		
		if(!isset($_SESSION[_prefix.'page'])){
			header('Location: /' . URL);
		}

		self::_script();
		reg_hook('reg_language',array());
		sobad_asset::_sidemenu('email');
	}

	public static function _page(){
		theme_layout('load_here');
	}

	private static function _script(){
		$script = new vendor_script();
		$theme = new theme_script();
	// libs
		//$rich = new richtextbox_script();

		// url script jQuery - Vendor
		$get_jquery = $script->_get_('_js_core',array('jquery-core'));
		$head[0] = '<script src="'.$get_jquery['jquery-core'].'"></script>';	

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$script->_get_('_css_page_level',array(
						'bootstrap-select',
						//'bootstrap-datepicker',
						//'fullcalender',
						//'bootstrap-editable',
						//'bootstrap-wysihtml5',
						//'bootstrap-summernote'
					)
				),
				$script->_get_('_css_datatable',array('datatable')),
				$script->_get_('_css_chart'),
				$theme->_get_('_css_page_level',array('themes-search')),
				$theme->_get_('_css_page'),
				$theme->_get_('_css_theme')
				//$rich->_get_('_css_core')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core'),
				$script->_get_('_js_page_level',array(
						'bootstrap-select',
						'bootstrap-toastr',
						'bootstrap-datepicker',
						//'boot-wysihtml5',
						//'bootstrap-wysihtml5',
						'bootstrap-summernote'
					)
				),
				$script->_get_('_js_chart'),
				$script->_get_('_js_ckeditor'),
				//$script->_get_('_js_form_editable'),
				//$script->_get_('_js_page_modal'),
				$theme->_get_('_js_page_level')
				//$rich->_get_('_js_core',array('loader'))
			);
		
	//	unset($script['bootstrap-modal']);
		unset($js['jquery-core']);
		unset($js['themes-modal']);	
		unset($js['themes-login-soft']);
		unset($js['themes-editable']);
		unset($js['themes-contextmenu']);
	
		$custom['login'] = self::load_script();

		reg_hook("reg_script_head",$head);
		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$custom);
	}

	private static function load_script(){
		$args = array(
			array(
				'func'	=> '_quickbar',
				'data'	=> ''
			),
		);

		ob_start();
		theme_layout('_custom_script',$args);
		return ob_get_clean();
	}
}