<?php
require 'template/chart.php';
require 'template/dashboard.php';
require 'template/form.php';
require 'template/login.php';
require 'template/table.php';

// ---------------------------------------------
// Create Panel --------------------------------
// ---------------------------------------------
function sobad_panel($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	foreach($args as $key => $val){
		if(is_callable($val['func'])){
			// add style
			if(is_callable($val['style'])){
				$val['style']();
			}
		
			echo '<div class="row">';
				$val['func']($val['data']);
			echo '</div>';
				sobad_clearfix();
				
			// add script
			$js = array_filter($val['script']);
			if(!empty($js)){
				foreach($val['script'] as $js => $script){		
					if(is_callable($script)){
						$script();
					}
				}
			}
		}
	}
}

// ---------------------------------------------
// Header Content ------------------------------
// ---------------------------------------------

function _modal_form(){
	?>
		<div class="modal fade bs-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div id="here_modal" class="modal-dialog modal-full-custom">
			<!-- /.modal-dialog -->
				<?php _modal_loading() ;?>
				<!-- /.modal-content -->
			</div>
		</div>
		<div class="modal fade bs-modal-lg" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div id="here_modal2" class="modal-dialog modal-lg">
			<!-- /.modal-dialog -->
			</div>
		</div>
	<?php
}

function _modal_loading(){
	?>
		<div class="modal-content">
			<div class="modal-body">
				<img src="asset/img/loading-spinner-grey.gif" alt="" class="loading">
				<span> &nbsp;&nbsp;Loading... </span>
			</div>
		</div>
	<?php
}

function _modal_content($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$id = isset($args['id'])?'id="'.$args['id'].'"':'';
	
	?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title"><?php print($args['title']) ;?></h4>
			</div>
			
			<?php foreach($args['func'] as $key => $func){ ?>
				<div class="modal-body">
					<div <?php print($id) ;?> class="row">
						<?php
							if(is_callable($func)){
								$func($args['data'][$key]);
							}
						?>
					</div>
				</div>
			<?php }?>
			
			<div class="modal-footer">
				<?php
					if(is_callable($args['button'])){
						$args['button']($args['status']);
					}
				?>
			</div>
		</div>
	<?php
}

function _btn_modal_save($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$status = '';
	if(isset($args['status'])){
		$status = $args['status'];
	}

	$type = '';
	if(isset($args['type'])){
		$type = $args['type'];
	}
	
	?>
	<button data-sobad="<?php print($args['link']) ;?>" data-load="<?php print($args['load']) ;?>" data-type="<?php print($type) ;?>" type="button" class="btn blue" data-dismiss="modal" onclick="sobad_submit(this)" <?php print($status) ;?>>Save</button>
	<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
	<?php
}

function _btn_modal_import($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$status = '';
	if(isset($args['status'])){
		$status = $args['status'];
	}
	
	?>
	<button id="importFile" data-sobad="<?php print($args['link']) ;?>" data-load="<?php print($args['load']) ;?>" type="button" class="btn blue" data-dismiss="modal" <?php print($status) ;?> style="display:none;"></button>
	
	<script>
	$(document).ready(function (e) {
		$("#<?php print($args['id']) ;?>").submit(function(){
			sobad_load('<?php print($args['id']) ;?>');
			sobad_import(this);
			Metronic.unblockUI('<?php print($args['id']) ;?>');
			return false;
		});
	});
	</script>
	<?php
}

function _btn_modal_yes($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	?>
	<button data-sobad="<?php print($args['link']) ;?>" type="button" class="btn green" onclick="sobad_quest(this)">Ya</button>
	<button type="button" class="btn red" data-dismiss="modal">Tidak</button>
	<?php
}

function _theme_option(){
	include 'theme_option.php';
}

function _head_pagebar($link=array(),$date=false){	
	$check = array_filter($link);
	?>
			<div class="page-bar">
				<ul class="page-breadcrumb">
					<li>
						<i class="fa fa-home"></i>
						<a href="">Home</a>
						<i class="fa fa-angle-right"></i>
					</li>
					<?php 
						if(!empty($check)){
							foreach($link as $key => $val){
								$angle = '';
								if($key<count($link)-1){
									$angle = '<i class="fa fa-angle-right"></i>';
								}

								echo '
									<li>
										<i class="fa"></i>
										<a id="sobad_'.$val['func'].'" class="sobad_linkheader" href="javascript:void(0)" onclick="sobad_sidemenu(this)">
											'.$val['label'].'
										</a>
										'.$angle.'
									</li>';
							}
						} 
					?>
				</ul>
				
			<?php if($date): ?>
				<div class="page-toolbar">
					<div id="dashboard-report-range" class="pull-right tooltips btn btn-sm btn-default" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
						<i class="icon-calendar"></i>&nbsp; <span class="thin uppercase visible-lg-inline-block"></span>&nbsp; <i class="fa fa-angle-down"></i>
					</div>
				</div>
			<?php endif; ?>
			
			</div>
	<?php
}

// ---------------------------------------------
// Create Portlet Box --------------------------
// ---------------------------------------------
function sobad_portlet($args = array()){	
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$_id = "sobad_portlet";
	if(isset($args['ID'])){
		$_id = $args['ID'];
	}
	
	?>
	<div class="col-md-12" style="border:1px solid #fff">
		<div class="portlet box blue-madison">
			<div class="portlet-title">
				<div class="caption">
					<?php print($args['label']) ;?>
				</div>
				<div class="tools">
					<?php print($args['tool']) ;?>
				</div>
				<div class="actions">
					<?php
						if(is_callable($args['action'])){
							$dt = '';
							if(isset($args['in_act'])){
								$dt = $args['in_act'];
							}
							print($args['action']($dt));
						}
					?>
				</div>
			</div>
			<div class="portlet-body">
				<div id="<?php print($_id) ;?>" class="dataTables_wrapper no-footer">
					<?php
						if(is_callable($args['func'])){
							$args['func']($args['data']);
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// ---------------------------------------------
// Create Tabs ---------------------------------
// ---------------------------------------------
function sobad_tabs($args = array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	?>
	<div class="col-md-12">
		<div id="sobad_tabs" class="tabbable">
			<ul class="nav nav-tabs nav-tabs-lg">
				<?php
					$li_cls = array();
					foreach($args['tab'] as $key => $val){
						$li_cls[$key] = '';
						if(isset($val['active'])){
							if($val['active']==true){
								$li_cls[$key] = 'active';
							}
						}
					}
					
					$check = array_filter($li_cls);
					if(empty($check)){
						$li_cls[0] = 'active';
					}
					
					foreach($args['tab'] as $key => $val){
						echo '
							<li class="'.$li_cls[$key].'">
								<a onclick="sobad_tabs(this)" id="'.$val['key'].'" data-sobad="'.$val['func'].'" data-load="tab_malika" data-toggle="tab" href="#tab_malika'.$key.'" aria-expanded="true">
								'.$val['label'].' 
								<span class="badge '.$val['info'].'">'.$val['qty'].'</span>
								</a>
							</li>
						';
					}
				?>
			</ul>
			<div class="tab-content">
				<?php 
					$no_tab = 0;
					foreach($args['tab'] as $key => $val){
						?>
						<div class="tab-pane active" id="tab_malika">
							<div class="row">

						<?php
							if($no_tab < 1){
								if(is_callable($args['func'])){
									$args['func']($args['data']);
								}
							}
						?>
						
							</div>
						</div>
				<?php
					$no_tab += 1;
					}
				?>
			</div>
		<?php
}

// ---------------------------------------------
// Create Inline Menu --------------------------
// ---------------------------------------------

function sobad_inline_menu($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	?>
		<div class="col-md-3">
			<ul class="ver-inline-menu tabbable margin-bottom-10">
				<?php
					$li_cls = 'active';
					foreach($args['menu'] as $key => $val){
						echo '
							<li class="'.$li_cls.'">
								<a id="'.$val['key'].'" data-toggle="tab" href="#inline_malika'.$key.'" aria-expanded="true">
									<i class="'.$val['icon'].'"></i>
									'.$val['label'].' 
									<span class="after"></span>
								</a>
							</li>
						';
						$li_cls = '';
					}
				?>
			</ul>
		</div>
		<div class="col-md-9">
			<div class="tab-content">
				<?php
					$active = 'active';
					foreach($args['content'] as $key => $val){
						if(is_callable($val['func'])){
							echo '<div id="inline_malika'.$key.'" class="tab-pane '.$active.'">';
							$val['func']($val['data']);
							echo '</div>';
							
							$active = '';
						}
					}
				?>
			</div>
		</div>
	<?php
}

// ---------------------------------------------
// Create option dashboard ---------------------
// ---------------------------------------------
function sobad_dashboard($args = array()){
	$dash = new admin_dashboard();
	$dash->_dashboard($args);
}

// ---------------------------------------------
// Create Table --------------------------------
// ---------------------------------------------
function sobad_table($args = array()){
	$table = new create_table();
	$table->_table($args);
}

// ---------------------------------------------
// Create Form ---------------------------------
// ---------------------------------------------
function sobad_form($args = array()){
	$form = new create_form();
	$form->get_form($args);
}

// ---------------------------------------------
// Create Chart ---------------------------------
// ---------------------------------------------
function sobad_chart($args = array()){
	$chart = new create_chart();
	$chart->_layout($args);
}