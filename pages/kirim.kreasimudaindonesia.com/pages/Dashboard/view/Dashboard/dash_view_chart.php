<?php
function kmi_dash_year(){
	$chart = data_chart_year();
	sobad_chart($chart);
}

function dash_chart_email_year(){
	$year = date('Y');
	$months = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');

// --------------- Create Data Chart
	$sends = new kmi_send();
	$data = array();

	$args = array("COUNT(ID) AS mail");

	$email = array('Sending','Failed','Reading','Click Link','Not read');
	$color = array(0,2,4,5,6);
	for($i=0;$i<12;$i++) {
		$month = sprintf("%02d",$i+1);
		$whr = " AND YEAR(meta_date)='$year' AND MONTH(meta_date)='$month'";

		//$pend = $sends->get_log_metas($args,"AND status IN ('0')".$whr);
		$fail = $sends->get_log_metas($args,"AND status IN ('2') ".$whr);
		$send = $sends->get_log_metas($args,"AND status IN ('3','4','5') ".$whr);
		$read = $sends->get_log_metas($args,"AND status IN ('4','5') ".$whr);
		$link = $sends->get_log_metas($args,"AND status IN ('5') ".$whr);

		$data[0]['data'][$i] = $send[0]['mail'];
		$data[1]['data'][$i] = $fail[0]['mail'];
		$data[2]['data'][$i] = $read[0]['mail'];
		$data[3]['data'][$i] = $link[0]['mail'];
		$data[4]['data'][$i] = $send[0]['mail'] - $read[0]['mail'];
	}

	$data[0]['type'] = 'line';
	$data[1]['type'] = 'bar';
	$data[2]['type'] = 'bar';
	$data[3]['type'] = 'bar';
	$data[4]['type'] = 'bar';

	$jml = 5;
	for($i=0;$i<$jml;$i++){
		$data[$i]['label'] = $email[$i];
		$data[$i]['bgColor'] = get_chartColor($color[$i],0.5);
		$data[$i]['brdColor'] = get_chartColor($color[$i]);
	}

	$args = array(
		'type'		=> 'bar',
		'label'		=> $months,
		'data'		=> $data,
		'option'	=> '_option_omset_bar'
	);
	
	return $args;
}

function data_chart_year(){
	$year = date('Y');
	$whr = "AND YEAR(meta_date)='$year'";
	$args = array("COUNT(ID) AS mail");
	$email = new kmi_send();

	$total = $email->get_log_metas($args,$whr);
	$pend = $email->get_log_metas($args,"AND status IN ('0')".$whr);
	$fail = $email->get_log_metas($args,"AND status IN ('2')".$whr);
	$send = $email->get_log_metas($args,"AND status IN ('3','4','5')".$whr);
	$read = $email->get_log_metas($args,"AND status IN ('4','5')".$whr);
	$link = $email->get_log_metas($args,"AND status IN ('5')".$whr);

	$omset = '<div>
				<div> Yearly Email : '.$year.'</div>
				<div style="font-size:30px;padding:7px;"> '.$total[0]['mail'].' </div>
				<div style="font-size:14px;text-align:left;"> Pending <i style="margin-left:10px;"></i>: '.$pend[0]['mail'].'</div>
				<div style="font-size:14px;text-align:left;"> Failed <i style="margin-left:23px;"></i>: '.$fail[0]['mail'].'</div>
				<div style="font-size:14px;text-align:left;"> Read <i style="margin-left:28px;"></i>: '.$read[0]['mail'].'</div>
				<div style="font-size:14px;text-align:left;"> Click <i style="margin-left:31px;"></i>: '.$link[0]['mail'].'</div>
			</div>';

	$chart[] = array(
		'func'	=> '_site_load',
		'data'	=> array(
			'id'		=> 'omset_month',
			'func'		=> 'dash_chart_email_year',
			'col'		=> 12,
			'label'		=> $omset
		),
	);
	
	return $chart;
}