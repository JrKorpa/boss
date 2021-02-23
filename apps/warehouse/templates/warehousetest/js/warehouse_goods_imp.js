//匿名回调
$import(function(){

	var obj = function(){
		//表单验证和提交
		var handleForm = function(){
			$('#warehouse_goods_search_form_edit').validate({
				submitHandler: function (form) {
					$("#warehouse_goods_search_form_edit").ajaxSubmit(opt);
				}
			});
			var opt = {
				url: 'index.php?mod=warehouse&con=WarehouseTest&act=import_goods',
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('导入成功');
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					bootbox.alert("数据加载失败");  
				}
			}
		
		};
		
		var initData = function(){
			$('#imp_goods_size').on('click', function(){
				var page_size = $("input[name='page_size']").val();
				if(page_size == "")
				{
					bootbox.alert('导入数量不能为空');	
				}else{
					$('body').modalmanager('loading');//进度条和遮罩
					$.post('index.php?mod=warehouse&con=WarehouseTest&act=import_goods',{page_size:page_size},function(data){
						if(data.success == 1 ){
							$('.modal-scrollable').trigger('click');//关闭遮罩
							bootbox.alert('导入成功');
						}else{
							$('body').modalmanager('removeLoading');//关闭进度条
							bootbox.alert('导入失败');
						}
					});
				}
			});
			
			$('#imp_company').on('click', function(){
												   
				$('body').modalmanager('loading');//进度条和遮罩
				$.post('index.php?mod=warehouse&con=WarehouseTest&act=import_company',function(data){
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('导入成功');
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert('导入失败');
					}				
				});
			});
			
			
			$('#imp_warehouse').on('click', function(){
				$('body').modalmanager('loading');//进度条和遮罩
				$.post('index.php?mod=warehouse&con=WarehouseTest&act=import_warehouse',function(data){
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('导入成功');
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert('导入失败');
					}				
				});
			});
		};
		
		return {
			init:function(){
				handleForm();//搜索提交
				initData();
			}
		}
	}();

	obj.init();

});
