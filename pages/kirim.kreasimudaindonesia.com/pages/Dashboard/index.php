<?php
require dirname(__FILE__).'/function.php';

$args = array();
$args['dashboard'] = array(
	'page'		=> 'reg_interface',
	'home'		=> false
);
reg_hook('reg_page',$args);

function reg_interface(){
	$GLOBALS['body'] = 'page-header-fixed';
	
	if(!isset($_SESSION['kmi_name'])){
		$err = new error();
		$err->_page404();
	}
	
	script_dashboard();
	reg_hook('reg_sidebar',reg_sidemenu());
	reg_hook('reg_exe','home_page');
}

function home_page(){
	print(sobad_here());
}

function script_dashboard(){
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
		
	ob_start();
	dashboard_script();
	$custom['login'] = ob_get_clean();

	reg_hook("reg_script_head",$head);
	reg_hook("reg_script_css",$css);
	reg_hook("reg_script_js",$js);
	reg_hook("reg_script_foot",$custom);
}

function dashboard_script(){
	?>
<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   QuickSidebar.init(); // init quick sidebar
Demo.init(); // init demo features
   Index.init();   
   Index.initDashboardDaterange();
//   Index.initJQVMAP(); // init index page's custom scripts
   Index.initCalendar(); // init index page's custom scripts
//   Index.initCharts(); // init index page's custom scripts
//   Index.initChat();
//   Index.initMiniCharts();
   Tasks.initDashboardWidget();
//   UIExtendedModals.init();
//FormEditable.init();
});
</script>
	<?php
}