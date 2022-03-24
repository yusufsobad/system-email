// -------------------- Chart JS
			function load_chart_dash(data,id){
				$('#chart_loading_'+id).hide();
				$('#chart_content_'+id).show();

				var dtsheet = [];var fl;var brd;
				for(var i = 0;i<data['data'].length;i++){
					fl = true;brd = 1;
					if(data['data'][i]['type']=='line'){
						fl = false;brd = 2;
					}

					dtsheet[i] = { 
						type : data['data'][i]['type'],
						label : data['data'][i]['label'],
						backgroundColor : data['data'][i]['bgColor'],
						borderColor : data['data'][i]['brdColor'],
						borderWidth : brd,
						data : data['data'][i]['data'],
						fill : fl
					}
				};

				var ctx = document.getElementById(id).getContext('2d');
				var opts = typeof(window[data['option']])=='function'?window[data['option']]():data['option'];

				window[id] = new Chart(ctx, {
					type: data['type'],
					data: {
						labels:data['label'],
						datasets:dtsheet
					},
					options: opts
				});	
			}