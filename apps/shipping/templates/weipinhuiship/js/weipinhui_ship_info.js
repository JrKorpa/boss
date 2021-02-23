//匿名回调
$import(function(){

	var obj = function(){
		//表单验证和提交
		var handleForm = function(){
			$('#weipinhui_ship_search_form_edit').validate({
				submitHandler: function (form) {
					$("#weipinhui_ship_search_form_edit").ajaxSubmit(opt);
				}
			});
			var opt = {
				url: 'index.php?mod=shipping&con=WeipinhuiShip&act=batch_ship',
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('批量发货成功');
						$('#order_batch_fqc_search_form_edit textarea').val('');

					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
						$('#order_batch_fqc_search_form_edit textarea').val('');
						//var tmpa =$('#wph_order_sn').val();
						//var tmpb = tmpa.replace(data.order_sn,'');
						//var tmpc = tmpb.replace("\n",'');
						//$('#wph_order_sn').val(tmpc);

					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					bootbox.alert("数据加载失败");  
				}
			}
		
		};
		return {
			init:function(){
				handleForm();//搜索提交
			}
		}
	}();

	obj.init();
	

});
