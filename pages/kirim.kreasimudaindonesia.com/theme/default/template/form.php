<?php

class create_form{
	public $col_label = 4;
	public $col_input = 7;
	
	private function option_form($args=array()){
		$inp = '';
		foreach($args as $key => $val){
			if($key === 'cols'){
				$this->col_label = $val[0];
				$this->col_input = $val[1];
			}else{
			
				$func = $val['func'];
				if(is_callable(array($this,$func))){
					$inp = self::$func($val);
				}
				
				if(isset($val['type'])){
					if($val['type']!='hidden'){
						$inp = '<div class="form-group">'.$inp.'</div>';
					}
				}else{
					$inp = '<div class="form-group">'.$inp.'</div>';
				}
				
				echo $inp;
				
			}
		}
	}
	
	public function get_option($opt,$args=array()){
		$func = 'opt_'.$opt;

		if(!is_callable(array($this,$func))){
			return false;
		}

		return $this->$func($args);
	}
	
	public function get_form($args){
		$check = array_filter($args);
		if(empty($check)){
			$args = array(
				0 => array(
					'key'			=> '',
					'label'			=> 'Info',
					'class'			=> '',
					'placeholder'	=> '',
					'value'			=> 'Tidak ada data yang ditemukan',
					'status'		=> 'readonly'
				),
				'cols'	=> array(4,7),
				'id'	=> ''
			);
		}
		
		$id = '';
		if(isset($args['id'])){
			$id = $args['id'];
			unset($args['id']);
		}
	
		?>
			<div class="col-lg-12">
				<form id="<?php print($id) ;?>" role="form" method="post" class="form-horizontal" enctype="multipart/form-data">
					<?php $this->option_form($args) ;?>
				</form>
			</div>
			<script>
				ComponentsDropdowns.init();
				ComponentsEditors.init();
			</script>
		<?php
	}
	
	private function opt_label($val){
		// label
		return '<label class="col-md-'. $this->col_label .' control-label">'.$val.'</label>';
	}
	
	private function opt_hidden($val=array()){
		$inp = '<input type="'.$val['type'].'" name="'.$val['key'].'" value="'.$val['value'].'">';
		return $inp;
	}
	
	private function opt_input($val=array()){	
		// id, type , class , key , value , *data
		// *data = placeholder , status , max , min , dll
		// label (optional)
		$inp = '';
		if(isset($val['label'])){
			$inp .= $this->opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. $this->col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$btn = '';
		$cols = $this->col_input;
		if(isset($val['button'])){
			$cols -= 1;
			$btn = '<div class="col-md-1">'.$val['button'].'</div>';
		}
		
		$inp .= '<div class="col-md-'. $cols .'">';
		$inp .= '<input '.$id.' type="'.$val['type'].'" class="form-control '.$val['class'].'" name="'.$val['key'].'" value="'.$val['value'].'" '.$val['data'].'>';
		
		$inp .= '</div>';
		return $inp.$btn;
	}
	
	private function opt_file($val=array()){	
		// id , class , key , value , *data
		// *data = placeholder , status , max , min , dll
		// label (optional)
		$inp = '';
		if(isset($val['label'])){
			$inp .= $this->opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. $this->col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}

		$txt = 'Import';
		if(isset($val['text'])){
			$txt = $val['text'];
		}
		
		$cols = $this->col_input;
		
		$inp .= '<div class="col-md-'. ($cols - 2) .'">';
		$inp .= '<input '.$id.' type="file" class="form-control" name="'.$val['key'].'" accept="'.$val['accept'].'" '.$val['data'].'>';
		$inp .= '</div>';
		
		$inp .= '<div class="col-md-2">';
		$inp .= '<input type="submit" name="import" value="'.$txt.'" class="btn green">';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_textarea($val=array()){
		// id, key , class , rows , value
		// label (optional)
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= $this->opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. $this->col_label .'"></div>';
		}
		
		$inp .= '<div class="col-md-'. $this->col_input .'">';
		$inp .= '<textarea '.$id.' name="'.$val['key'].'" class="form-control '.$val['class'].'" rows="'.$val['rows'].'">'.$val['value'].'</textarea>';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_wysihtml5($val=array()){
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= $this->opt_label($val['label']);
			$col_input = $this->col_input;
		}else{
			$col_input = $this->col_label + $this->col_input;
		}
		
		$inp .= '<div class="col-md-'. $col_input .'">';
		$inp .= '<div name="'.$val['key'].'" id="summernote_1" class="'.$val['class'].'" '.$val['data'].'>'.$val['value'].'</div>';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_button($val=array()){
		// id, key , class , label, text, click, data
		$inp = '';
		if(isset($val['label'])){
			$inp .= $this->opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. $this->col_label .'"></div>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}
		
		$inp .= '<div class="col-md-'. $this->col_input .'">';
		$inp .= '<button '.$id.' onclick="'.$val['click'].'" type="'.$val['key'].'" class="btn btn-default '.$val['class'].'" '.$val['data'].'>'.$val['text'].'</button>';
		$inp .= '</div>';
		return $inp;
	}
	
	private function opt_select($val=array()){
		// id, key , class , data , select
		// label (optional)
		
		$arr_select = $val['select'];
		
		$func = '';
		if(is_array($val['data'])){
			foreach($val['data'] as $key => $opt){
				$select = '';
				
				if(!is_array($arr_select)){
					if($key==$arr_select){
						$select = 'selected';
					}
				}else{
					if(in_array($key,$arr_select)){
						$select = 'selected';
					}
				}
				
				// Grouped
				if(isset($val['group']) && $val['group']==true){
					if(is_array($opt)){ 
						// start grouping
						$func .= '<optgroup label="'.$key.'">';
						foreach($opt as $ky => $grp){
							$select = '';
							if(is_array($arr_select)){
								if(in_array($ky,$arr_select)){
									$select = 'selected';
								}
							}else{
								if($ky==$arr_select){
									$select = 'selected';
								}
							}
							
							$func .= self::opt_value_select($ky,$select,$grp);
						}
						$func .= '</optgroup>';
					}else{
						$func .= self::opt_value_select($key,$select,$opt);
					}
				}else{
					$func .= self::opt_value_select($key,$select,$opt);
				}
			}
		}else{
			$func = '<option value="0"> Tidak ada </option>';
		}
		
		$id = '';
		if(isset($val['id'])){
			$id = 'id="'.$val['id'].'"';
		}
		
		$inp = '';
		if(isset($val['label'])){
			$inp .= $this->opt_label($val['label']);
		}else{
			$inp .= '<div class="col-md-'. $this->col_label .'"></div>';
		}
		
		$status = '';
		if(isset($val['status'])){
			$status = $val['status'];
		}

		$btn = '';
		$cols = $this->col_input;
		if(isset($val['button'])){
			$cols -= 1;
			$btn = '<div class="col-md-1">'.$val['button'].'</div>';
		}
		
		$inp .= '<div class="col-md-'. $cols .'">';
		$inp .= '<select '.$id.' name="'.$val['key'].'" class="form-control '.$val['class'].'" '.$status.' onchange="sobad_options(this)">'.$func.'</select>';
		$inp .= '</div>';
		
		return $inp.$btn;
	}
	
	private function opt_value_select($key,$select,$opt){
		$func = '<option value="'.$key.'" '.$select.'> '.$opt.' </option>';
		return $func;
	}
}