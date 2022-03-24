<?php

class user_login{
	public function login($func,$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$data = 'placeholder="username" autofocus required';
			$args[0] = array(
				'option' 	=> 'input',
				'data'		=> array(
					'type'		=> 'text',
					'key'		=> 'user',
					'class'		=> '',
					'value'		=> '',
					'data'		=> $data
				)
			);
			
			$data = 'placeholder="password" required';
			$args[1] = array(
				'option' 	=> 'input',
				'data'		=> array(
					'type'		=> 'password',
					'key'		=> 'pass',
					'class'		=> '',
					'value'		=> '',
					'data'		=> $data
				)
			);
		}
		
		$option = new create_form();
		
		?>
		
		<!-- BEGIN LOGIN FORM -->
	<form class="login-form" action="javascript:void(0)" method="post">
		<h3 class="form-title">Login to your account</h3>
		<div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
		</div>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
			</div>
		</div>
		<div class="form-actions">
			<label class="checkbox">
			<input type="checkbox" name="remember" value="1"/> Remember me </label>
			<button id="btn_login_submit" type="submit" class="btn blue pull-right">
			Login <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	</form>
	<!-- END LOGIN FORM -->
		<?php
	}
}