<?php

if(!class_exists('dash_mail')){
	include 'dash_mail.php';
}

class daftar_mail extends _page{
	protected static $object = 'daftar_mail';

	protected static $table = 'kmi_mail';

	protected static $loc_view = 'Email.list';

	protected static $post = 'customer';

	protected static function _array(){
		$args = array(
			'ID',
			'name',
			'email',
			'note',
			'type',
			'meta_value',
			'mail_secure',
			'mail_host',
			'mail_pass'
		);

		return $args;
	}

	protected static function _array_table(){
		$args = array(
			'ID',
			'name',
			'email',
			'note'
		);

		return $args;
	}

	protected static function table(){
		$data = array();
		$args = self::_array_table();
		$type = self::$type;

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$tabs = str_replace('mail_', '', $type);
		$post = $tabs==4?'group':'email';

		$_search = '';$kata = '';

		$user = get_id_user();
		$where = "AND `email-list`.type='$tabs' AND `email-list`.user='$user'";
		if(self::$search){		
			$src = self::like_search($args,$where);
			$cari = $src[0];
            $where = $src[0];
            $kata = $src[1];
            $_search = $src[2];
		}else{
			$cari=$where;
		}
		
		$limit = 'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where,$post);
		$sum_data = $object::count("1=1 ".$cari);

		$data = array(
            'kata'      => $kata,
            'search'    => $_search,
            'type'      => $type,
            'data'      => $args,
            'start'     => $start,
            'sum_data'  => $sum_data,
            'nLimit'    => $nLimit,
        );

		return self::_loadView('table',$data);
	}

	private static function head_title(){
		$args = array(
			'title'	=> 'Contacts Email<small>contacts email</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'contacts'
				)
			),
			'date'	=> false
		);
		
		return $args;
	}

	protected static function get_box(){
		$type = self::$type;
		$data = self::table();
		
		$label = $type=='mail_1'?'Email':self::_conv_type($type);
		
		$box = array(
			'label'		=> 'Data '.$label,
			'tool'		=> '',
			'action'	=> self::action(),
			'func'		=> 'sobad_table',
			'data'		=> $data
		);
		
		return $box;
	}

	protected static function layout(){
		self::$type = 'mail_1';
		$box = self::get_box();

		$user = get_id_user();

		$qty1 = kmi_mail::count("type='1' AND user='$user'");
		$qty2 = kmi_mail::count("type='2' AND user='$user'");
		$qty3 = kmi_mail::count("type='4' AND user='$user'");

		$tabs = array(
			'tab'	=> array(
				0	=> array(
					'key'	=> 'mail_1',
					'label'	=> 'My Email',
					'qty'	=> $qty1
				),
				1	=> array(
					'key'	=> 'mail_2',
					'label'	=> 'Database',
					'qty'	=> $qty2
				),
				2	=> array(
					'key'	=> 'mail_4',
					'label'	=> 'Group',
					'qty'	=> $qty3
				)
			),
			'active'=> 'mail_1',
			'func'	=> '_portlet',
			'data'	=> $box
		);

		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(),
			'script'	=> array()
		);

		return tabs_admin($opt,$tabs);
	}

	protected static function action(){
		$type = self::$type;

		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah',
			'type'	=> $type
		);
		
		$add = edit_button($add);

		if($type=='mail_2'){
			$import = array(
				'ID'	=> 'import_0',
				'func'	=> '_import_customer',
				'color'	=> 'btn-default',
				'icon'	=> 'fa fa-file-excel-o',
				'label'	=> 'Import'
			);
		
			$imp = _modal_button($import);
		
			return $imp.$add;
		}

		return $add;
	}

	protected static function group_action(){
		$import = array(
			'ID'	=> 'import_0',
			'func'	=> '_import_group',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-file-excel-o',
			'label'	=> 'Import'
		);
		
		return _modal_button($import,2);
	}

	public static function _conv_type($type=0){
		$type = str_replace('mail_', '', $type);
		$args = kmi_mail::get_types();

		return isset($args[$type])?$args[$type]:'-';
	}

	// ----------------------------------------------------------
	// Form data category ---------------------------------------
	// ----------------------------------------------------------

	public static function add_form(){
		$type = str_replace('mail_', '', $_POST['type']);
		intval($type);

		$vals = array(0,'','','',$type,'',1,'','');
		$vals = array_combine(self::_array(), $vals);

		self::$post = self::_conv_type($_POST['type']);
		
		$config =self::_loadView('form',array(
			'data' 	=> $vals,
			'type'	=> self::$post
		));

		$data = array(
            'title' => 'Tambah Data Email',
            'link' 	=> '_add_db',
            'data'	=> $config,
            'type'	=> $_POST['type']
        );

        if($_POST['type']!='mail_4'){
	        return sobad_asset::_loadView('form',$data);
	    }

	    $data['ID'] = 'groupEmail_portlet';
	    $data['label'] = 'Data Group Email';
	    $data['action'] = '';
	    $data['table'] = array(
	    	'table'	=> array()
	    );

	    return sobad_asset::_loadView('form_table',$data);
	}

	public static function edit_form($id=0,$func='_update_db',$load='sobad_portlet',$type=0){
		self::$post = self::_conv_type($_POST['type']);
		$vals = self::_edit($id,false);
		
		$config =self::_loadView('form',array(
			'data' => $vals,
			'type' => self::$post
		));

		$data = array(
            'title' => 'Edit Data Email',
            'link' 	=> $func,
            'load'	=> $load,
            'data'	=> $config,
            'type'	=> empty($type)?$_POST['type']:$type
        );

        if($_POST['type']!='mail_4'){
	        return sobad_asset::_loadView('form',$data);
	    }

	    $data['ID'] = 'groupEmail_portlet';
	    $data['label'] = 'Data Group Email';
	    $data['action'] = self::group_action();
	    $data['table'] = self::group_detail_table($vals['ID']);

	    return sobad_asset::_loadView('form_table',$data);
	}

	protected static function group_detail_table($id=0){
		$data = array();
		$args = array('ID','name','email');

		$type = self::$type;

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);

		$clist = kmi_mail::get_group($id,array('ID','meta_value'));	
		$clist = empty($clist[0]['meta_value'])?'0':$clist[0]['meta_value'];
		
		$where = "AND `email-list`.ID IN($clist) ";
		
		$_search = '';$kata = '';
		if(self::$search){		
			$src = self::like_search($args,$where);
			$cari = $src[0];
            $where = $src[0];
            $kata = $src[1];
            $_search = $src[2];
		}else{
			$cari=$where;
		}
		
		$limit = 'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;
		
		$args = kmi_mail::get_all($args,$where,'customer');
		$sum_data = kmi_mail::count('1=1 '.$cari);
		
		$data['data'] = array(
			'func' 	=> '_search_group',
			'data'	=> $kata,
			'value'	=> $_search,
			'type'	=> $id,
			'load'	=> 'groupEmail_portlet'
		);
		
		$data['search'] = array('Semua','nama','email');
		$data['class'] = '';
		$data['table'] = array();
		$data['page'] = array(
			'func'	=> '_pagination',
			'data'	=> array(
				'start'		=> $start,
				'qty'		=> $sum_data,
				'limit'		=> $nLimit,
				'func'		=> '_pagination_group',
				'load'		=> 'groupEmail_portlet',
				'type'		=> $id
			)
		);
		
		foreach($args as $key => $val){
			$data['table'][$key]['tr'] = array();
			$data['table'][$key]['td'] = array(
				'nama'	=> array(
					'left',
					'auto',
					$val['name'],
					true
				),
				'email'	=> array(
					'left',
					'50%',
					$val['email'],
					true
				)
			);
		}
		
		return $data;
	}

	// ----------------------------------------------------------
	// Form import data -----------------------------------------
	// ----------------------------------------------------------	

	public static function _import_customer(){
		$data = array(
			'id'	=> 'importForm',
			'cols'	=> array(3,8),
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ajax',
				'value'			=> 'import_customerEmail'
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'object',
				'value'			=> 'email'
			),
			array(
				'id'			=> 'file_import',
				'func'			=> 'opt_file',
				'type'			=> 'file',
				'key'			=> 'data',
				'label'			=> 'Filename',
				'accept'		=> '.csv',
				'data'			=> ''
			)
		);
		
		$args = array(
			'title'		=> 'Import Daftar Customer',
			'button'	=> '_btn_modal_import',
			'status'	=> array(
				'id'		=> 'importForm',
				'link'		=> 'import_customerEmail',
				'load'		=> 'sobad_portlet'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function _import_group($id){
		$id = str_replace('import_','',$id);
		$data = array(
			'id'	=> 'importForm',
			'cols'	=> array(3,8),
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ajax',
				'value'			=> 'import_groupEmail'
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'object',
				'value'			=> 'email'
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $id
			),
			array(
				'id'			=> 'file_import',
				'func'			=> 'opt_file',
				'type'			=> 'file',
				'key'			=> 'data',
				'label'			=> 'Filename',
				'accept'		=> '.csv',
				'data'			=> ''
			)
		);
		
		$args = array(
			'title'		=> 'Import Daftar Customer',
			'button'	=> '_btn_modal_import',
			'status'	=> array(
				'id'		=> 'importForm',
				'link'		=> 'import_groupEmail',
				'load'		=> 'here_modal'
			)
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public static function import_customerEmail($args=array()){
		$fileName = $_FILES["data"]["tmp_name"];

		if ($_FILES["data"]["size"] > 0) {
	        $delimiter = _detectDelimiter($fileName);
	        $file = fopen($fileName, "r");
	        
	        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
	        	$email = str_replace('"', '', $column[1]);
	        	$email = preg_replace('/\s+/', '', $email);
				$email = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $email);

				$colm = explode(";",$email);
	        	foreach ($colm as $key => $val) {
					if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
						continue;
					}
				}
				
				$id_user = get_id_user();
				$whr = "user='$id_user' AND email='$email' AND type='2'";
				$q = kmi_mail::get_email_custom($whr,array('ID'));
				
				$check = array_filter($q);
				if(empty($check)){
					$args = array(
						'name'	=> _filter_string($column[0]),
						'email'	=> $email,
						'type'	=> 2
					);	
					$q = sobad_db::_insert_table('email-list',$args);
				}
	        }
			
			if($q!==0){
				self::$post = 'customer';
				$title = self::table();
				return table_admin($title);
			}
	    }
	}

	public static function import_groupEmail($args=array()){
		if(!isset($_POST['ID'])){
			return '';
		}
		
		$id_grp = $_POST['ID'];
		$fileName = $_FILES["data"]["tmp_name"];
		
		if ($_FILES["data"]["size"] > 0) {
			$clist = kmi_mail::get_group($id_grp);
			$clist = explode(',',$clist[0]['id_mail']);
			
	        $delimiter = _detectDelimiter($fileName);
	        $file = fopen($fileName, "r");
			
			$where = "meta_id='$id_grp' AND meta_key='mail_group'";
			$lst_grp = sobad_db::_select_table("WHERE ".$where,'email-group-meta',array('meta_value'));
			
			$list_group = array();
			if($lst_grp!==0){
				$r = $lst_grp->fetch_assoc();
				$list_group = explode(',',$r['meta_value']);
			}
			
	        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
	        	$email = str_replace('"', '', $column[1]);
	        	$email = preg_replace('/\s+/', '', $email);
				$email = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/','', $email);

				$colm = explode(";",$email);
	        	foreach ($colm as $key => $val) {
					if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
						continue;
					}
				}
				
				$id_user = get_id_user();
				$whr = "user='$id_user' AND email='$email' AND type='2'";
				$q = kmi_mail::get_email_custom($whr,array('ID'));
				
				$check = array_filter($q);
				if(empty($check)){
					$args = array(
						'name'	=> _filter_string($column[0]),
						'email'	=> $email,
						'type'	=> 2
					);
					$q = sobad_db::_insert_table('email-list',$args);
					$idx = $q;
				}else{
					$idx = $q[0]['ID'];
				}
				
				if(! in_array($idx,$list_group)){
					array_push($clist,$idx);
				}
	        }
		
			$arg = array(
				'meta_value'	=> implode(',',$clist)
			);
			
			$q = sobad_db::_update_multiple($where,'email-group-meta',$arg);
			
			if($q!==0){
				//return groupEdit_daftar($id_grp);
			}
	    }
	}

	// ----------------------------------------------------------
	// Function Pages to database -------------------------------
	// ----------------------------------------------------------
	protected static function _get_tableGroup($idx,$args=array()){
		if($idx==0){
			$idx = 1;
		}

		self::$page = $idx;
		self::$search = true;
		self::$type = isset($_POST['type'])?$_POST['type']:'';

		$args = isset($_POST['args'])?sobad_asset::ajax_conv_array_json($_POST['args']):$args;
		self::$data = array(
			'words'		=> $args['words'][0],
			'search'	=> $args['search'][0]
		);

		$table = static::group_detail_table(self::$type);
		return table_admin($table);
	}

	public static function _pagination_group($idx){
		return self::_get_tableGroup($idx);
	}

	public static function _search_group($args=array()){
		$args = sobad_asset::ajax_conv_array_json($args);
		return self::_get_tableGroup(1,$args);
	}

	// ----------------------------------------------------------
	// Simpan database ------------------------------------------
	// ----------------------------------------------------------

	public static function _callback($args=array()){
		if($args['type']==1){
			self::$post = 'produsen';
		}else if($args['type']==4){
			self::$post = 'group';
		}

		return $args;
	}

	// ------------------------------------------------------
	// -- Diagram Statistik ---------------------------------
	// ------------------------------------------------------

	public static function _view($id=0){
		$id = str_replace('view_', '', $id);
		intval($id);

		$args = array(
			'title'		=> 'Statistik Email Customer',
			'button'	=> '',
		);

		$args['func'] = array('_view_chart');
		$args['data'] = array($id);

		return modal_admin($args);;
	}

	public static function _view_chart($id=0){
		$dough = self::_customer_mail_dough($id);
		$tabel = self::_customer_mail_year($id);

		?>
			<div class="row">
				<?php theme_layout('sobad_chart',$dough) ;?>
				<?php theme_layout('sobad_chart',$tabel) ;?>
			</div>
		<?php
			dash_mail::_script();
	}

	protected static function _customer_mail_dough($id=0,$style=''){
		$sMail = kmi_send::count_log_metas("meta_mail='$id' AND status IN ('2','3','4','5')");

		$omset = 'Jumlah Pengiriman '.$sMail;

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'cust_mail_dough',
				'func'		=> 'dash_send_mail_dough',
				'status'	=> $style,
				'col'		=> 4,
				'label'		=> $omset,
				'type'		=> $id
			),
		);
		
		return $chart;
	}

	protected static function _customer_mail_year($id=0,$style=''){
		$omset = 'Grafik Tahun '.date('Y');

		$chart[] = array(
			'func'	=> '_site_load',
			'data'	=> array(
				'id'		=> 'cust_mail_year',
				'func'		=> 'dash_chart_omset_year',
				'status'	=> $style,
				'col'		=> 8,
				'label'		=> $omset,
				'type'		=> $id
			),
		);
		
		return $chart;
	}

	public static function dash_send_mail_dough($id=0){
		$id = isset($_POST['type'])?$_POST['type']:0;
		$year = date('Y');$month = date('m');$day = date('d');
		$days = array();

	// --------------- Create Data Chart
		$sMail = kmi_send::count_log_metas("meta_mail='$id' AND status IN ('3','4','5')");
		$fMail = kmi_send::count_log_metas("meta_mail='$id' AND status='2'");
		$rMail = kmi_send::count_log_metas("meta_mail='$id' AND status IN ('4','5')");
		$cMail = kmi_send::count_log_metas("meta_mail='$id' AND status='5'");

		$nMail = $sMail - $rMail;

		$label = array('Belum di Read','Gagal Terkirim','Email Terbaca','Email di Click Link');
		$type = array($nMail,$fMail,$rMail,$cMail);
		$color = array(6,2,4,5);
		$idx = 0;

		foreach($type as $key => $val){
			if($val>0){
				$data[0]['data'][$idx] = $val;
				$types[$idx] = $label[$key];

				$data[0]['bgColor'][$idx] = dash_mail::get_color($color[$key],0.8);
				$data[0]['brdColor'][$idx] = 'rgba(256,256,256,1)';
				$idx += 1;
			}
		}

		if(empty($sMail)){	
			$data[0]['data'][0] = 1;
			$types[0] = 'Unknown';

			$data[0]['bgColor'] = 'rgba(256,256,256,1)';
			$data[0]['brdColor'] = dash_mail::get_color();
		}	

		$data[0]['label'] = 'Type';
		$data[0]['type'] = 'doughnut';

		$args = array(
			'type'		=> 'doughnut',
			'label'		=> $types,
			'data'		=> $data,
			'option'	=> '_option_omset_doughnut'
		);
		
		return $args;
	}

	public static function dash_chart_omset_year($id=0){
		$year = date('Y');
		$id = isset($_POST['type'])?$_POST['type']:0;
		$months = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');

	// --------------- Create Data Chart

		$label = array('Email Terbaca','Email di Click Link','Belum di Read');
		$color = array(4,5,6,0);
		$idx = 0;
		for($i=0;$i<12;$i++) {
			$month = sprintf("%02d",$i+1);
			$tanggal = "AND YEAR(meta_date)='$year' AND MONTH(meta_date)='$month'";

			$sMail = kmi_send::count_log_metas("meta_mail='$id' AND status IN ('3','4','5') $tanggal");
			//$fMail = kmi_send::count_log_metas("meta_mail='$id' AND status='2' $tanggal");
			$rMail = kmi_send::count_log_metas("meta_mail='$id' AND status IN ('4','5') $tanggal");
			$cMail = kmi_send::count_log_metas("meta_mail='$id' AND status='5' $tanggal");		

			$nMail = $sMail - $rMail;

			$data[0]['data'][$i] = $rMail;
			$data[1]['data'][$i] = $cMail;
			$data[2]['data'][$i] = $nMail;
			$data[3]['data'][$i] = $sMail;
		}

		$data[0]['label'] = 'Read';
		$data[1]['label'] = 'Click';
		$data[2]['label'] = 'No Read';
		$data[3]['label'] = 'Send';

		$data[0]['type'] = 'bar';
		$data[1]['type'] = 'bar';
		$data[2]['type'] = 'bar';
		$data[3]['type'] = 'line';

		$jml = 4;
		for($i=0;$i<$jml;$i++){
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
}