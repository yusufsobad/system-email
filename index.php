<?php
// -------------- show error reporting
//	ini_set('display_errors', 1);
//	ini_set('display_startup_errors', 1);
//	error_reporting(E_ALL);
// -------------- end error

define('AUTHPATH',$_SERVER['SERVER_NAME']);
require "include/config/hostname.php";

session_start();

// ----- Set Menu
$menu = isset($_GET['direct'])?$_GET['direct']:'';
$menu = get_uri($menu);
define('load_menu',$menu);

// Check Hostname yang mengakses
new hostname();

// ----- set page
$prefix = constant('_prefix');
$page = isset($_SESSION[$prefix.'page'])?$_SESSION[$prefix.'page']:'Home';

// get file component
new _component();

// include pages
$asset = new sobad_asset();
$asset->_pages();

$pages = new sobad_page($page);
$pages->_get();

global $body;
?>
<!DOCTYPE html>
<html>

<head>
	<?php sobad_meta_html() ;?>
    <title><?php print(constant('title')) ;?></title>
	<link rel="icon" type="image/ico" href="favicon.ico" /> 

	<!-- Firebase notif -->	
<!-- 	<script src="https://cdn.firebase.com/js/client/2.2.3/firebase.js"></script>
	<script src="https://www.gstatic.com/firebasejs/3.6.1/firebase.js"></script> -->
	
	<?php
		$asset->_vendor_css();
		$asset->_css_file();
		$asset->_script_head();
	?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript">
    	var object = "<?= $menu ;?>";
    	var system = "<?= URL ;?>";
    	var hosting = "<?= SITE.'://'.HOSTNAME ;?>/";
    	var websocket = "<?= SITE.'://'.HOSTNAME ;?>:3000";
    </script>

</head>

<body class="<?php print($body) ;?>">

	<?php $pages->_execute();?>

	
	<?php
		$asset->_vendor_js();
		$asset->_js_file();
		$asset->_script_foot();
	?>
</body>

</html>
