//匿名回调
$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){

	//匿名函数+闭包
	var obj1 = function(){
		
		var initElements = function(){
			//下拉组件

		};
		
		var handleForm = function(){

		};
		
		var initData = function(){
			$('button[name="1"]').on('click', function(){
				var id=$(this).attr("list-id");
				var data=$(this).attr("val");
				if(id==1){
					var t="&cert="+data;
				}else if(id==2){
					var t="&from_ad="+data;
				}else if(id==3){
					var t="&shape="+data;
				}else if(id==4){
					var t="&warehouse="+data;
				}else{
					return false;
				}
				$('body').modalmanager('loading');//进度条和遮罩
				bootbox.confirm("是否确认删除？", function(result) {
				  if (result == true) {
					setTimeout(function(){
						$.post('index.php?mod=diamond&con=DiamondTongji&act=del'+t,function(data){
							$('.modal-scrollable').trigger('click');
							if(data.success == 1 ){
								bootbox.alert('删除成功');
								$('.modal-scrollable').trigger('click');
								util.retrieveReload();
								util.syncTab(0);
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
							}
						});
					},0);
				  }
				});
			});
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();
	obj1.init();
});