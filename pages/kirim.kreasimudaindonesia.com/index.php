<?php
// -------------- show error reporting
//	ini_set('display_errors', 1);
//	ini_set('display_startup_errors', 1);
//	error_reporting(E_ALL);
// -------------- end error

date_default_timezone_set('Asia/Jakarta');
session_start();

$page = 'Home';

$_SESSION['kmi_language'] = 'id_ID';
if(isset($_SESSION['kmi_page'])){
	$page = $_SESSION['kmi_page'];
}

include dirname(__FILE__)."/function.php";
// include pages
sobad_pages();
sobad_getPage($page);

global $body;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php sobad_meta_html() ;?>
    <title>Send Mail KMI</title>
	
	<?php
		vendor_css();
		sobad_css_file();
		script_head();
	?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="<?php print($body) ;?>">

	<?php sobad_execute($page);?>

	
	<?php
		vendor_js();
		sobad_js_file();
		script_foot();
	?>
</body>

</html>
