$import(function(){
			$('button[type="button"]').on('click',function(){
				var tto=$("input").serialize();
				var text=$("textarea").serialize();
				var data=text+"&"+tto;

				$.post('index.php?mod=diamond&con=AppDiamondTiaojia&act=up',data,function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('提交成功');
						$('.modal-scrollable').trigger('click');
					}
					else
					{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});			
			})

			$(':reset').on('click',function(){
				$('#app_diamond_tiaojia_search_list input').val('');
				$('#app_diamond_tiaojia_search_list textarea').val('');
			})

});