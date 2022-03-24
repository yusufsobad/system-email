<?php
//(!defined('AUTHPATH'))?exit:'';

// ---------------------------------------
// Set Time Jakarta ----------------------
// ---------------------------------------
ini_set('date.timezone', 'Asia/Jakarta');

// Database -------------------------------------------

// set Server
define('SERVER',"localhost");

// set Username
define('USERNAME',"root");

// set Password
define('PASSWORD','');

// set Database
define('DB_NAME','sobad_mail700');
$GLOBALS['DB_NAME'] = DB_NAME;

// set rule database
$database_sc = array(
	0 => array(
		'db' 	=> DB_NAME, // nama database
		'where'	=> '' // TABLE_NAME= . . .
	)
);

define('SCHEMA',serialize($database_sc));

// URL web --------------------------------------------

// set hostname
define('SITE','http');

// set hostname
define('HOSTNAME',$_SERVER['SERVER_NAME']);

// set name url
define('URL','system-email');

// set check table
define('ABOUT','');

// Setting -------------------------------------------

// prefix SESSION
define('_prefix','blastmail_');
		
// authentic include
define('AUTH_KEY','qJB0rGtInG03efyCpWs');

// PATH default
define('DEFPATH',dirname(__FILE__));

// set Multiple language
define('language',true);

// set nama Perusahaan
define('company','SASI');

// set judul Website
define('title','System Blast Email');

// set Auto Include Page
define('include_pages', true);

// set Callback URL after Logout
define('url_logout', '');

// Library ------------------------------------------
$library_sc = array(
	// name folder 		=> lokasi file,
	'createpdf'			=> 'html2pdf/html2pdf.class.php',
	'mpdf'				=> 'mpdf/autoload.php'
);

define('_library',serialize($library_sc));

// Mode Development

define('development',1);

// Default Theme

define('theme','metronic');

// Default Theme

define('convToPDF','createpdf');