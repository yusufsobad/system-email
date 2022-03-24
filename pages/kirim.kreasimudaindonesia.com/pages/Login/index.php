<?php
$args = array();
$args['Login'] = array(
	'page'	=> 'login_reg',
	'home'	=> true
);
reg_hook('reg_page',$args);

function login_reg(){
	$GLOBALS['body'] = 'login';
	script_login();
	reg_hook('reg_exe','login_page');
}

function script_login(){
	$script = new vendor_script();
	$theme = new theme_script();

	// url script css ----->
	$css = array_merge(
			$script->_get_('_css_global'),
			$script->_get_('_css_page_level',array('select2','bootstrap-toastr')),
			$theme->_get_('_css_page_level',array('themes-login-soft')),
			$theme->_get_('_css_theme')
		);
	
	// url script css ----->
	$js = array_merge(
			$script->_get_('_js_core'),
			$script->_get_('_js_page_level',array('bootstrap-toastr')),
			$script->_get_('_js_page_login'),
			$theme->_get_('_js_page_level')
		);
	
	unset($js['jquery-ui']);
	unset($js['bootstrap-hover']);
	unset($js['bootstrap-hover-dropdown']);
	unset($js['jquery-slimscroll']);
	unset($js['bootstrap-switch']);
	
	unset($js['themes-quick-sidebar']);
	unset($js['themes-index']);
	unset($js['themes-task']);
	unset($js['themes-editable']);
	unset($js['themes-picker']);
	
	ob_start();
	login_script();
	$custom['login'] = ob_get_clean();

	reg_hook("reg_script_css",$css);
	reg_hook("reg_script_js",$js);
	reg_hook("reg_script_foot",$custom);
}

function login_page(){
	?>
	<!-- BEGIN LOGO -->
	<div class="logo">
		<img src="asset/img/logo-big.png" alt=""> 
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content">
	<?php
		$log = new user_login();
		print($log->login('check_login'));
	?>
	</div>
	<!-- END LOGIN -->
	<!-- BEGIN COPYRIGHT -->
	<div class="copyright">
		2019 © Send Mail CV. KMI
	</div>
	<!-- END COPYRIGHT -->
	<?php
}

function login_script(){
	?>
<script>
jQuery(document).ready(function() {     
  Metronic.init(); // init metronic core components
  Layout.init(); // init current layout
  Login.init();
  Demo.init();
       // init background slide images
       $.backstretch([
        "asset/img/bg/1.jpg",
        "asset/img/bg/2.jpg",
		"asset/img/bg/3.jpg",
        "asset/img/bg/4.jpg",
		"asset/img/bg/5.jpg",
        "asset/img/bg/6.jpg",
        ], {
          fade: 1000,
          duration: 8000
    }
    );
});
</script>
	<?php
}

function check_login($args=array()){
	$data = ajax_conv_json($args);
	$user = $data['username'];
	$pass = md5($data['password']);
	
	$tbl = 'email-user';
	$where = "WHERE user='$user' AND pass='$pass'";
	
	$db = new kmi_db();
	$q = $db->_select_table($where,$tbl,array('`email-user`.ID'));
	if($q!==0)
	{	
		$r=$q->fetch_assoc();
		$_SESSION['kmi_ID'] = $r['ID'];
		$_SESSION['kmi_page'] = 'dashboard';
		$_SESSION['kmi_user'] = $user;
		$_SESSION['kmi_name'] = 'Mail';
		
		return 'index.php';
	}
	else
	{
		$db->_user_login();
	}
}