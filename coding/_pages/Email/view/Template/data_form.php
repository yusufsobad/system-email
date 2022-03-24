<?php

abstract class template_mail extends _page{

	protected static $url = "../asset/template/";

	protected static $template = '';

	protected static $type_mail = 0;

	protected static $form = false;

	protected static $data_form = array();

	protected static function _template($data=array(),$type=0,$form=false){
		self::$type_mail = $type;
		self::$template = self::_conv_template($type);

		self::$form = $form;
		self::$data_form = $data;

		if($type==2){
			return self::layout_view();
		}

		return self::layout_form();
	}

	protected static function _conv_template($type=0){
		$args = array('content','signature','view');
		return isset($args[$type])?$args[$type]:'';
	}

	private static function template_title(){
		$form = self::$form?'edit':'tambah';

		if(self::$type_mail==2){
			$form = 'view';
		}

		$args = static::head_title();
		$args['link'][1] = array(
			'func'	=> static::$object,
			'label'	=> $form . ' template',
			'uri'	=> self::$template . '/' . $form
		);

		return $args;
	}

	protected static function box_template(){
		$data = self::_form();
		
		$box = array(
			'label'		=> 'Template ' . self::$template,
			'tool'		=> '',
			'action'	=> '',
			'object'	=> static::$object,
			'func'		=> 'template_form',
			'data'		=> $data
		);

		return $box;
	}

	protected static function layout_form(){
		$box = self::box_template();
		
		$opt = array(
			'title'		=> self::template_title(),
			'style'		=> array(''),
			'script'	=> array(static::$object,'_script_template')
		);
		
		return portlet_admin($opt,$box);
	}

	// ----------------------------------------------------------
	// View Template mail ---------------------------------------
	// ----------------------------------------------------------	

	protected static function viewBox_template(){
		$box = array(
			'label'		=> 'Template ' . self::$template,
			'tool'		=> '',
			'action'	=> '',
			'object'	=> static::$object,
			'func'		=> 'view_template',
			'data'		=> ''
		);

		return $box;
	}

	protected static function layout_view(){
		$box = self::viewBox_template();
		
		$opt = array(
			'title'		=> self::template_title(),
			'style'		=> array(''),
			'script'	=> array('')
		);
		
		return portlet_admin($opt,$box);
	}

	public static function view_template(){
		$q = self::$data_form;

		$html = '';
		if(!empty($q[0]['lokasi'])){
			$url = self::$url . $q[0]['lokasi'];
			
			ob_start();
			include $url;
			$html = ob_get_clean();
		}

		print($html);
	}

	public static function _script_template(){
		?>
			<script>
				var domain_url = 'vendor';
				CKEDITOR.replace( 'editor_text',{
					// Link dialog, "Browse Server" button
					filebrowserBrowseUrl : domain_url+'/ckfinder/ckfinder.html',
					// Image dialog, "Browse Server" button
					filebrowserImageBrowseUrl : domain_url+'/ckfinder/ckfinder.html?type=Images',
					// Flash dialog, "Browse Server" button
					filebrowserFlashBrowseUrl : domain_url+'/ckfinder/ckfinder.html?type=Flash',
					// Upload tab in the Link dialog
					filebrowserUploadUrl : domain_url+'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
					// Upload tab in the Image dialog
					filebrowserImageUploadUrl : domain_url+'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
					// Upload tab in the Flash dialog
					filebrowserFlashUploadUrl : domain_url+'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
				} );
			</script>
		<?php
	}

	// ----------------------------------------------------------
	// Form data send mail --------------------------------------
	// ----------------------------------------------------------

	public static function _form(){
		$vals = self::$data_form;

		$status = '';
		if($vals['locked']==1){
			$status = 'disabled';
		}
		
		$html = '';
		if(!empty($vals['lokasi'])){
			$url = self::$url . $vals['lokasi'];
			
			ob_start();
			include $url;
			$html = ob_get_clean();
		}

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals['ID']
			),
			1 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'lokasi',
				'value'			=> $vals['lokasi']
			),
			2 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'type',
				'value'			=> $vals['type']
			),
			3 => array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'title',
				'class'			=> 'input-circle',
				'value'			=> $vals['name'],
				'required'		=> true,
				'data'			=> 'placeholder="title"'
			),
			4 => array(
				'func'			=> 'opt_textarea',
				'id'			=> 'editor_text',
				'key'			=> 'html',
				'class'			=> '',
				'rows'			=> 60,
				'value'			=> $html
			)
		);

		return $data;
	}

	public static function template_form($data=array()){	
		$link = self::$form?'_update_template':'_add_template';
		$load = 'here_content';
		$type = self::$type_mail;

		?>
			<div class="row">
				<?php echo theme_layout('report_form',$data) ;?>
				<div id="button_save_content" class="col-md-12" style="text-align:right;">
					<button data-sobad="<?php print($link) ;?>" data-load="<?php print($load) ;?>" data-type="<?php print($type) ;?>" type="button" class="btn blue" data-dismiss="modal" data-index="" onclick="sobad_submitLoad(this)">Save</button>
					<button data-sobad="_layout" data-load="<?php print($load) ;?>" data-type="<?php print($type) ;?>" type="button" class="btn default" data-dismiss="modal" data-uri="<?php echo self::$template ;?>" onclick="sobad_sidemenu(this)">Cancel</button>
				</div>	
			</div>
		<?php
	}
}