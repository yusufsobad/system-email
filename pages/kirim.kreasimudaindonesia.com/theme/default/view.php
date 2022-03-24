<?php
require dirname(__FILE__).'/scripts.php';
require dirname(__FILE__).'/view_header.php';
require dirname(__FILE__).'/quick_sidebar.php';

function sobad_meta_html(){ 
?>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta content="" name="description"/>
	<meta content="" name="author"/>

<?php
}

function sobad_search(){
	?>
	<form class="sidebar-search " action="javascript:;" method="POST">
		<a href="javascript:;" class="remove">
			<i class="icon-close"></i>
		</a>
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Search...">
			<span class="input-group-btn">
				<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
			</span>
		</div>
	</form>
	<?php
}

function sobad_here(){
	sobad_header();
	sobad_clearfix();
	sobad_container();
	sobad_footer();
}

function sobad_header(){
	$head = new metronic_header();
	$args = array(
		0	=> array(
			'menu'	=> 'menu_notif',
			'data'	=> array()
		),
		1	=> array(
			'menu'	=> 'menu_user',
			'data'	=> ''
		),
		2	=> array(
			'menu'	=> 'side_toggle',
			'data'	=> ''
		),
	);
	
	?>
		<!-- BEGIN HEADER -->
		<div class="page-header -i navbar navbar-fixed-top">
			<?php
				$head->_create($args);
			?>
		</div>
		<!-- END HEADER -->
	<?php
}

function sobad_clearfix(){
	?>
		<div class="clearfix"></div>
	<?php
}

function sobad_container(){
	?>
	<div class="page-container">
		<?php
			$request = sobad_sidebar();
		?>
		<div class="page-content-wrapper">
			<div id="here_content" class="page-content">
		
		<?php
		    $check = array_filter($request);
			if(!empty($check)){
				$func = $request['func'];
				$data = $request['label'];
				if(is_callable($func)){	
					echo $func($data);
				}
			}
		?>
		
			</div>
		</div>
		<?php	
			sobad_quick_side();
		?>
	</div>	
	<?php
}

function sobad_footer(){
	?>
	<div class="page-footer">
		<div class="page-footer-inner">
			2019 © Kreasi Muda Indonesia
		</div>
		<div class="scroll-to-top" style="display: none;">
			<i class="icon-arrow-up"></i>
		</div>
	</div>
	<?php
}

function sobad_sidebar(){
	global $reg_sidebar;
	
	?>
		<div class="page-sidebar-wrapper">
			<div class="page-sidebar navbar-collapse collapse">
				<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 410px;">
					<ul class="page-sidebar-menu " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" data-height="410" data-initialized="1" style="overflow: hidden; width: auto; height: 410px;">
						<?php
							$request = sobad_side_multiple($reg_sidebar);
						?>
					</ul>
				</div>
			</div>
		</div>
	<?php
	
	return $request;
}

function sobad_quick_side(){
	$quick = new metronic_quick_sidebar();
	?>
		<a href="javascript:;" class="page-quick-sidebar-toggler">
			<i class="icon-close"></i>
		</a>
	<?php
		$quick->_create();
}

function sobad_side_multiple($args=array()){
	$req = array();
	$check = array_filter($args);
	if(!empty($check)){
		foreach($args as $key => $val){
			// Check active Sidemenu
			if($val['status']=='active'){
				$req['func'] = $val['func'];
				$req['label'] = $val['label'];
				$status = 'start active';
				$select = '<span class="selected"></span>';
			}else{
				$status = '';
				$select = '';
			}
			
			echo '<li class="'.$status.'">';
			
			$parent = '';
			$target = '';
			if(empty($val['func'])){
				$parent = 'disabled-link';
				$target = 'disable-target';
			}
			
			$side = '<a id="sobad_'.$val['func'].'" class="sobad_sidemenu '.$parent.'" href="javascript:void(0)">
				<i class="'.$val['icon'].' fa-fw"></i>
				<span class="title '.$target.'">'.$val['label'].'</span>';
			$side .= $select;
			
			// Check child sidemenu
			if($val['child']!=null){
				echo $side.'<span class="arrow"></span></a>';
				echo '<ul class="sub-menu">';
				sobad_side_multiple($val['child']);
				echo '</ul>';
			}else{
				echo $side.'</a>';
			}
			
			echo '</li>';
		}
		
		return $req;
	}
}

function sobad_head_content($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return 'Not Available';
	}
	
	_modal_form();
	_theme_option();
	?>
		<h3 class="page-title">
			<?php print($args['title']) ;?>
		</h3>
	<?php
	_head_pagebar($args['link'],$args['date']);
}

function sobad_content($func,$args = array()){
	if(is_callable($func)){
		// get content
		$func($args);
		
	}else{
		?><div style="text-align:center;"> Tidak ada data yang di Load </div><?php
	}
}