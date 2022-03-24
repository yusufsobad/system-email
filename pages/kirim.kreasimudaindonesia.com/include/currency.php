<?php

function format_currency($current){
	$args = array(
		'id_ID'	=> 'Rp',
		'en_US'	=> '$',
	);
	
	return $args[$current];
}

function format_nominal($current,$nominal){
	$args = array(
		'id_ID'	=> array(0,',','.'),
		'en_US'	=> array(2,'.',','),
	);
	
	$val = $args[$current];
	
	return number_format($nominal,$val[0],$val[1],$val[2]);
}

function format_number_currency($current,$nominal){
	
	
	
	$format = format_currency($current);
	$format .= '<span class="sobad_currency"> ';
	$format .= format_nominal($current,$nominal);
	$format .= '</span>';
	
	return $format;
}

function format_date_id($date){
	$date = strtotime($date);
	$date = date('Y-m-d',$date);
	$date = explode('-',$date);
	
	$y = $date[0];
	$m = conv_month_id($date[1]);
	$d = $date[2];
	
	return $d.' '.$m.' '.$y;
}

function conv_day_id($int){
	intval($int);
	
	$args = array(
		'Minggu',
		'Senin',
		'Selasa',
		'Rabu',
		'Kamis',
		'Jum\'at',
		'Sabtu'
	);
	
	return $args[$int];
}

function conv_month_id($int){
	$int = intval($int) - 1;
	
	$args = array(
		'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	
	return $args[$int];
}