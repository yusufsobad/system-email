<?php

class dash_chart{
	public static function _layout(){
		$chart = self::_data();
		theme_layout('sobad_chart',$chart);
	}

	public static function _chart(){
		$year = date('Y');
		$months = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');

	// --------------- Create Data Chart
		$data = array();

		$args = array("ID");

		$email = array('Sending','Failed','Reading','Click Link','Not read');
		$color = array(0,2,4,5,6);
		for($i=0;$i<12;$i++) {
			$month = sprintf("%02d",$i+1);
			$whr = " AND YEAR(meta_date)='$year' AND MONTH(meta_date)='$month'";

			//$pend = kmi_send::get_log_metas($args,"AND status IN ('0')".$whr);
			$fail = kmi_send::get_log_metas($args,"AND status IN ('2') ".$whr);
			$send = kmi_send::get_log_metas($args,"AND status IN ('3','4','5') ".$whr);
			$read = kmi_send::get_log_metas($args,"AND status IN ('4','5') ".$whr);
			$link = kmi_send::get_log_metas($args,"AND status IN ('5') ".$whr);

			$data[0]['data'][$i] = count($send);
			$data[1]['data'][$i] = count($fail);
			$data[2]['data'][$i] = count($read);
			$data[3]['data'][$i] = count($link);
			$data[4]['data'][$i] = count($send) - count($read);
		}

		$data[0]['type'] = 'line';
		$data[1]['type'] = 'bar';
		$data[2]['type'] = 'bar';
		$data[3]['type'] = 'bar';
		$data[4]['type'] = 'bar';

		$jml = 5;
		for($i=0;$i<$jml;$i++){
			$data[$i]['label'] = $email[$i];
			$data[$i]['bgColor'] = dash_mail::get_color($color[$i],0.5);
			$data[$i]['brdColor'] = dash_mail::get_color($color[$i]);
		}

		$args = array(
			'type'		=> 'bar',
			'label'		=> $months,
			'data'		=> $data,
			'option'	=> '_option_omset_bar'
		);
		
		return $args;
	}

	protected static function _data(){
		$year = date('Y');
		$whr = "AND YEAR(meta_date)='$year'";
		$args = array("ID");

		$total = kmi_send::get_log_metas($args,$whr);
		$pend = kmi_send::get_log_metas($args,"AND status IN ('0')".$whr);
		$fail = kmi_send::get_log_metas($args,"AND status IN ('2')".$whr);
		$send = kmi_send::get_log_metas($args,"AND status IN ('3','4','5')".$whr);
		$read = kmi_send::get_log_metas($args,"AND status IN ('4','5')".$whr);
		$link = kmi_send::get_log_metas($args,"AND status IN ('5')".$whr);

		$omset = '<div>
					<div> Yearly Email : '.$year.'</div>
					<div style="font-size:30px;padding:7px;"> '.count($total).' </div>
					<div style="font-size:14px;text-align:left;"> Pending <i style="margin-left:10px;"></i>: '.count($pend).'</div>
					<div style="font-size:14px;text-align:left;"> Failed <i style="margin-left:23px;"></i>: '.count($fail).'</div>
					<div style="font-size:14px;text-align:left;"> Read <i style="margin-left:28px;"></i>: '.count($read).'</div>
					<div style="font-size:14px;text-align:left;"> Click <i style="margin-left:31px;"></i>: '.count($link).'</div>
				</div>';

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'omset_month',
				'func'		=> '_getChart',
				'col'		=> 12,
				'label'		=> $omset
			),
		);
		
		return $chart;
	}
}