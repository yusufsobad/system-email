<?php
if(!class_exists('send_mail')){
	require dirname(__FILE__).'/send_mail.php';
}

require dirname(__FILE__).'/Dashboard/include.php';

class dash_mail{
	private static function head_title(){
		$args = array(
			'title'	=> 'Dashboard <small>reports & statistics</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> 'dash_purchase',
					'label'	=> 'dashboard'
				)
			),
			'date'	=> false
		);
		
		return $args;
	}

	public static function _sidemenu(){
		$label = array();
		$data = array();

		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'status'	=> '',
			'func'		=> '_layout',
			'object'	=> 'dash_chart',
			'data'		=> ''
		);
		
		$data[] = array(
			'style'		=> array(),
			'script'	=> array('dash_mail','_script'),
			'func'		=> 'sobad_dashboard',
			'data'		=> self::_data()
		);
		
		$title = self::head_title();
		
		ob_start();
		theme_layout('_head_content',$title);
		theme_layout('_panel',$data);
		return ob_get_clean();
	}

	public static function _getChart(){
		return dash_chart::_chart();
	}

	public static function view_dash_block($id=0){
		$id = str_replace('mail_', '', $id);
		intval($id);

		return dash_block::_modal($id,1);
	}

	public static function _block_pagination($idx){
		return dash_block::_block_pagination($idx);
	}

	public static function _search_block($args=array()){
		return dash_block::_search_block($args);
	}

	private static function _data(){
		$args = array("ID");

		$pend = kmi_send::get_log_metas($args,"AND status IN ('0')");
		$fail = kmi_send::get_log_metas($args,"AND status IN ('2')");
		$send = kmi_send::get_log_metas($args,"AND status IN ('3','4','5')");
		$read = kmi_send::get_log_metas($args,"AND status IN ('4','5')");
		$link = kmi_send::get_log_metas($args,"AND status IN ('5')");

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'grey-intense',
				'qty'		=> count($pend),
				'desc'		=> 'Wait For Send',
				'button'	=> self::button_toggle_block(array('ID' => 'mail_0','func' => 'view_dash_block'))
			)
		);

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'red-intense',
				'qty'		=> count($fail),
				'desc'		=> 'Gagal terkirim',
				'button'	=> self::button_toggle_block(array('ID' => 'mail_2','func' => 'view_dash_block'))
			)
		);

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'green-haze',
				'qty'		=> count($send),
				'desc'		=> 'email terkirim',
				'button'	=> self::button_toggle_block(array('ID' => 'mail_3','func' => 'view_dash_block'))
			)
		);

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'blue-madison',
				'qty'		=> count($read),
				'desc'		=> 'Email Terbaca',
				'button'	=> self::button_toggle_block(array('ID' => 'mail_4','func' => 'view_dash_block'))
			)
		);

		$dash[] = array(
			'func'	=> '_block_info',
			'data'	=> array(
				'icon'		=> '',
				'color'		=> 'purple-plum',
				'qty'		=> count($link),
				'desc'		=> 'Click Link Email',
				'button'	=> self::button_toggle_block(array('ID' => 'mail_5','func' => 'view_dash_block'))
			)
		);

		return $dash;
	}

	protected static function button_toggle_block($val=array()){
		$check = array_filter($val);
		if(empty($check)){
			return '';
		}

		$val['toggle'] = 'modal';
		$val['load'] = 'here_modal';
		$val['href'] = '#myModal';

		return self::button_dash_block($val);
	}

	protected static function button_direct_block($val=array()){
		$check = array_filter($val);
		if(empty($check)){
			return '';
		}

		$val['toggle'] = '';
		$val['load'] = 'sobad_portlet';
		$val['href'] = 'javascript:;';
		$val['script'] = 'sobad_sidemenu(this)';

		return self::button_dash_block($val);
	}

	protected static function button_dash_block($val=array()){
		$status = '';
		if(isset($val['status'])){
			$status = $val['status'];
		}

		$onclick = 'sobad_button(this,false)';
		if(isset($val['script'])){
			$onclick = $val['script'];
		}

		$button = '
			<a id="'.$val['ID'].'" class="more" data-toggle="'.$val['toggle'].'" data-sobad="'.$val['func'].'" data-load="'.$val['load'].'" href="'.$val['href'].'" onclick="'.$onclick.'" '.$status.'>
				View more <i class="m-icon-swapright m-icon-white"></i>
			</a>';

		return $button;	
	}

	protected static function _check_sum_dash($args=array()){
		$check = array_filter($args);
		if(!empty($check) && !is_null($args[0]['sum'])){
			$value = $args[0]['sum'];
		}else{
			$value = 0;
		}

		return $value;
	}

	public static function _style(){
		?>
			<style type="text/css">
				.portlet > .portlet-title{
					border-bottom: unset;
				}
			</style>
		<?php
	}

	public static function get_color($int=0,$opc=1,$single=true){
		$color = array(
			'rgba(75, 192, 192,'.$opc.')', //green
			'rgba(255, 159, 64,'.$opc.')', //orange
			'rgba(255, 99, 132,'.$opc.')', //red
			'rgba(54, 162, 235,'.$opc.')', //blue
			'rgba(255, 205, 86,'.$opc.')', //yellow
			'rgba(153, 102, 255,'.$opc.')', //purple
			'rgba(201, 203, 207,'.$opc.')' //grey
		);

		if($single){
			$int = $int % 7;
			$warna = $color[$int];
		}else{
			$warna = array();
			foreach ($int as $ky => $val) {
				$val = $val % 7;
				$warna[$ky] = $color[$val];
			}
		}

		return $warna;
	}

	public static function _script(){
		?>
			<script type="text/javascript">
				var dash_year = <?php echo date('Y') ;?>;

				$(".chart_malika").ready(function(){
					if($('div').hasClass('chart_malika')){
						for(var i=0;i<$(".chart_malika").length;i++){
							var ajx = $('.chart_malika:eq('+i+')').attr('data-sobad');
							var id = $('.chart_malika:eq('+i+')').attr('data-load');
							var tp = $('.chart_malika:eq('+i+')').attr('data-type');
				
							data = "ajax="+ajx+"&object="+object+"&data="+dash_year+"&type="+tp;
							sobad_ajax(id,data,load_chart_dash);
						}
					}
				});

			// Function Option Omset Tahunan
				function _option_omset_bar(){
					var option = {
						responsive	: true,
						scales		: {
							yAxes		: [{
								ticks		: {
									callback 	: function(value, index, values) {return prefix_format(value,1);}
								}
							}]
						},
						tooltips	: {
							enabled		: true,
							mode		: 'single',
							callbacks	: {
								label 		: function(value, data) {return number_format(value.yLabel);}
							}
						}
					}

					return option;
				}

				function _option_omset_doughnut(){
					var option = {
						responsive	: true,
						animation	: {
							animateScale  : true,
							animateRotate : true
						},
						legend : {
							display : false
						},
						tooltips	: {
							enabled		: true,
							mode		: 'single',
							callbacks	: {
								label 		: function(value, data) {
									var idx = value.index;
									return number_format(data.datasets[0].data[idx]);
								},
								footer		: function(value, data) {
									var idx = value[0].index;
									return data.labels[idx];
								}
							}
						}
					}

					return option;
				}
			</script>
		<?php
	}
}