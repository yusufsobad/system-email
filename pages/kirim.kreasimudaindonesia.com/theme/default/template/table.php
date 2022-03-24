<?php

class create_table{
	
	public function _table ($args=array()){
		
		$class = "table table-striped table-bordered table-hover dataTable no-footer";
		if(isset($args['class'])){
			$class .= $args['class'];
		}

		$id = '';
		if(isset($args['id'])){
			$id = $args['id'];
		}
		
		if(isset($args['search'])){
			$this->_search($args['search'],$args['data']);
		}
		
		// table-scrollable
		?>
		<div class="table_flexible">
			<table id="<?php print($id) ;?>" class="<?php print($class) ;?>">
				<?php
				$check = array_filter($args['table']);
				if(empty($check)){
					$args['table'][0]['tr'] = array('');
					$args['table'][0]['td'] = array(
						'Keterangan'	=> array(
							'center',
							'auto',
							'Tidak ada data yang ditemukan',
							false
						)
					);
				}
				
				$this->thead($args['table']);
				$this->tbody($args['table']) ;?>
			</table>
		</div>
		<?php
		
		if(isset($args['page'])){
			$func = $args['page']['func'];
			if(is_callable(array($this,$func))){
				self::{$func}($args['page']['data']);
			}
		}
	}
	
	public function _search($args = array(),$data=array()){
		$search = '';
		$check = array_filter($args);
	
		$func = isset($data[0])?$data[0]:'';
		$value = isset($data[1])?$data[1]:'';
		$type = isset($data[2])?$data[2]:'';
		$load = isset($data[3])?$data[3]:'sobad_portlet';	
		?>
			<div class="row search-form-default">
				<div class="col-md-12">
					<form id="sobad-search" class="sobad_form" action="javascript:;">
						<div class="input-group">
							<div class="input-cont">
								<input type="text" name="words" placeholder="Search..." class="form-control" data-sobad="<?php print($func) ;?>" data-load="<?php print($load) ;?>" data-type="<?php print($type) ;?>" value="<?php print($value) ;?>">
							</div>
							<span class="input-group-btn">
								<?php
									if(!empty($check)){
										$this->_dropdown($args);
									}
								?>
								<button data-sobad="<?php print($func) ;?>" type="button" class="btn green-haze" data-load="<?php print($load) ;?>" data-type="<?php print($type) ;?>" onclick="sobad_search(this)">
									Search &nbsp; <i class="m-icon-swapright m-icon-white"></i>
								</button>
							</span>
						</div>
					</form>
				</div>
			</div>
			<script>
				$('#sobad-search input').keypress(function(event){
				var keycode = (event.keyCode ? event.keyCode : event.which);
					
					if(keycode == '13'){
						sobad_search(this);	
					}
				});
			</script>
		<?php
	}
	
	private function _dropdown($args = array()){
		$check = 'checked';
		?>
			<div class="btn-group">
				<button type="button" class="btn blue dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-cogs"></i></button>
				<div class="dropdown-menu hold-on-click dropdown-radiobuttons" role="menu">
					<?php 
						foreach($args as $key => $val){ 
						if(empty($val)){
							continue;
						}
						
						$idx = 'inp_src'.$key;
					?>
						<label for="<?php print($idx) ;?>" class="mouse_click">
							<div class="radio">
								<span class="<?php print($check) ;?>">
									<input id="<?php print($idx) ;?>" type="radio" name="search" value="<?php print($key) ;?>" <?php print($check) ;?>>
								</span>
							</div> 
							<?php print($val) ;?>
						</label>
					<?php
						$check = '';
						} 
					?>
				</div>
			</div>
			<script>
				$(".mouse_click").click(function(){
					$(".mouse_click .radio span").removeClass("checked");
					$(this).children('div.radio').children('span').addClass("checked");
				});
			</script>
		<?php
	}
	
	private function thead($args=array()){
		?>
			<thead>
				<tr role="row">
					<?php
						$thead = isset($args[0]['td'])?$args[0]['td']:$args[0];
						foreach($thead as $key => $val){
							$att = '';
							if($val[3]==true && isset($val[3])){
								$att = 'class="sorting"';
							}
							
							print('<th '.$att.' style="text-align:center;width:'.$val[1].';">'.$key.'</th>');
						}
					?>
				</tr>
			</thead>
		<?php
	}
	
	private function tbody($args=array()){
		$len = count($args);
	?>
		<tbody>
			<?php
				for($i=0;$i<$len;$i++){
					$hsl = $i % 2;
					$cls = 'odd';
					if($hsl == 1){
						$cls = 'even';
					}
					
					$cls1 = isset($args[$i]['tr'])?$args[$i]['tr']:'';
					$cls1 = isset($cls1[0])?$cls1[0]:'';
					
					echo '<tr role="row" class="'.$cls.' '.$cls1.'">';
					
						$tbody = isset($args[0]['td'])?$args[0]['td']:$args[0];
						foreach($args[$i]['td'] as $key => $val){
							echo '<td style="text-align:'.$val[0].'">'.$val[2].'</td>';
						}
					echo '</tr>';
				}
			?>
		</tbody>
	<?php
	}
	
	private function _pagination($args=array()){
		$prev = '';$next = '';$page=0;
		$number = intval($args['start']);
		$load = isset($args['load'])?$args['load']:'sobad_portlet';

		$type = '';
		if(isset($args['type'])){
			$type = $args['type'];
		}
		
		if($args['qty']!=0){
			$page = ceil($args['qty'] / $args['limit']);
		}
		
		if($page>5){
			$start = (ceil($number / 5) - 1)  * 5 + 1;
			$finish = $start + 4;
			if($finish>$page){
				$finish = $page;
			}

			if($start-1>=1){
				$prev = '<li>
							<a class="page_malika" data-sobad="'.$args['func'].'" data-qty="'.intval($start-1).'" data-load="'.$load.'" data-type="'.$type.'" href="javascript:;">
								<i class="fa fa-angle-left"></i>
							</a>
						</li>';
			}
		}else{
			$start = 1;
			$finish = $page;
		}
		
		if($finish<$page){
			$next = '<li>
						<a class="page_malika" data-sobad="'.$args['func'].'" data-qty="'.intval($finish+1).'" data-load="'.$load.'" data-type="'.$type.'" href="javascript:;">
							<i class="fa fa-angle-right"></i>
						</a>
					</li>';
		}
		?>
			<div class="margin-top-20">
				<ul id="dash_pagination" class="pagination">
					<?php
						print($prev);
					
						for($i=$start;$i<=$finish;$i++){
							$opt = '';
							$pg_malika = 'page_malika';
							if($i==$number){
								$opt = 'disabled';
								$pg_malika = '';
							}
							
							echo '<li class="'.$opt.'"><a class="'.$pg_malika.'" data-sobad="'.$args['func'].'" data-qty="'.$i.'" data-load="'.$load.'" data-type="'.$type.'" href="javascript:;"> '.$i.' </a></li>';
						}
						
						print($next);
					?>
				</ul>
			</div>
			<script>
				$("a.page_malika").click(function(){
					sobad_pagination(this);
				});
			</script>
		<?php
	}
	
	private function _scrolldown(){
		?>
			
		<?php
	}
}