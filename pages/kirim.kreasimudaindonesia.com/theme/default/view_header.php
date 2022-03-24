<?php

class metronic_header{
	public function _create($args=array()){
		?>
			<!-- BEGIN HEADER INNER -->
			<div class="page-header-inner">
				<?php
					self::_logo();
					self::_menu_toggle();
					self::_top_menu($args);
				?>
			</div>
			<!-- END HEADER INNER -->
		<?php
	}
	
	public function _logo(){
		?>
			<!-- BEGIN LOGO -->
			<div class="page-logo">
				<a href="">
					<img src="asset/img/logo.png" alt="logo" class="logo-default"/>
				</a>
				<div class="menu-toggler sidebar-toggler">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
				</div>
			</div>
			<!-- END LOGO -->
		<?php
	}
	
	public function _menu_toggle(){
		?>
			<!-- BEGIN RESPONSIVE MENU TOGGLER -->
			<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"></a>
			<!-- END RESPONSIVE MENU TOGGLER -->
		<?php
	}
	
	public function _top_menu($args=array()){
		?>
			<!-- BEGIN TOP NAVIGATION MENU -->
			<div class="top-menu">
				<ul class="nav navbar-nav pull-right">
					<?php
						$check = array_filter($args);
						if(!empty($check)){
							foreach($args as $key => $val){
								if(is_callable(array($this,$val['menu']))){
								    $func = $val['menu'];
									self::{$func}($val['data']);
								}
							}
						}
					?>
				</ul>
			</div>
			<!-- END TOP NAVIGATION MENU -->
		<?php
	}
	
	public function menu_notif($args=array()){
		?>
			<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
					<i class="icon-bell"></i>
					<span class="badge badge-default">1</span>
				</a>
				<ul class="dropdown-menu">
					<li class="external">
						<h3><span class="bold">1 pending</span> notifications</h3>
						<a href="extra_profile.html">view all</a>
					</li>
						<li>
							<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;">
								<ul class="dropdown-menu-list scroller" style="height: 250px; overflow: hidden; width: auto;" data-handle-color="#637283" data-initialized="1">
									<li>
										<a href="javascript:;">
											<span class="time">just now</span>
											<span class="details">
												<span class="label label-sm label-icon label-success">
													<i class="fa fa-plus"></i>
												</span>
												New user registered. 
											</span>
										</a>
									</li>
								<?php self::_scroll_bar(); ?>
							</div>
						</li>
					</ul>
				</li>
		<?php
	}
	
	public function menu_inbox($args=array()){
		?>
			<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-envelope-open"></i>
					<span class="badge badge-default">
					4 </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3>You have <span class="bold">7 New</span> Messages</h3>
							<a href="page_inbox.html">view all</a>
						</li>
						<li>
							<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 275px;">
								<ul class="dropdown-menu-list scroller" style="height: 275px; overflow: hidden; width: auto;" data-handle-color="#637283" data-initialized="1">
									<li>
										<a href="inbox.html?a=view">
											<span class="photo">
												<img src="../../assets/admin/layout3/img/avatar2.jpg" class="img-circle" alt="">
											</span>
											<span class="subject">
												<span class="from"> Lisa Wong </span>
												<span class="time">Just Now </span>
											</span>
											<span class="message">
												Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... 
											</span>
										</a>
									</li>
								</ul>
								<?php self::_scroll_bar(); ?>
							</div>
						</li>
					</ul>
				</li>
		<?php
	}
	
	public function menu_task($args=array()){
		?>
			<li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-calendar"></i>
					<span class="badge badge-default">
					3 </span>
					</a>
					<ul class="dropdown-menu extended tasks">
						<li class="external">
							<h3>You have <span class="bold">12 pending</span> tasks</h3>
							<a href="page_todo.html">view all</a>
						</li>
						<li>
							<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 275px;">
								<ul class="dropdown-menu-list scroller" style="height: 275px; overflow: hidden; width: auto;" data-handle-color="#637283" data-initialized="1">
									<li>
										<a href="javascript:;">
											<span class="task">
												<span class="desc">New release v1.2 </span>
												<span class="percent">30%</span>
											</span>
											<span class="progress">
												<span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
													<span class="sr-only">40% Complete</span>
												</span>
											</span>
										</a>
									</li>
								</ul>
								<?php self::_scroll_bar(); ?>
							</div>
						</li>
					</ul>
				</li>
		<?php
	}
	
	public function menu_user($args=''){
		?>
			<li class="dropdown dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" class="img-circle" src="asset/img/user/avatar2_small.jpg">
					<span class="username username-hide-on-mobile">
					<?php print($_SESSION['kmi_user']) ;?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="javascrip:void(0)">
							<i class="icon-user"></i> My Profile </a>
						</li>
						<li>
							<a href="javascrip:void(0)">
							<i class="icon-calendar"></i> My Calendar </a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="javascrip:void(0)">
							<i class="icon-lock"></i> Lock Screen </a>
						</li>
						<li>
							<a class="sobad_logout" data-toggle="modal" href="#myModal">
							<i class="icon-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
		<?php
	}
	
	public function side_toggle($args=''){
		?>
			<li class="dropdown dropdown-quick-sidebar-toggler">
				<a href="javascript:;" class="dropdown-toggle">
					<i class="icon-logout"></i>
				</a>
			</li>
		<?php
	}
	
	private function _scroll_bar(){
		?>
			<div class="slimScrollBar" style="background: rgb(99, 114, 131); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 147.994px;"></div>
			<div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px;"></div>
		<?php
	}
}