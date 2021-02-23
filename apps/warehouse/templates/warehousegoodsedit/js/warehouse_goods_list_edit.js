//显示搜索货品结果
function warehouse_goods_search_edit_page(url){
	//util.page(url);
}

//匿名回调
$import(function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseGoodsEdit&act=search');//设定刷新的初始url
	//util.setItem('formID','warehouse_goods_search_form_edit');//设定搜索表单id
	//util.setItem('listDIV','warehouse_goods_search_list_edit');//设定列表数据容器id
	//util.setItem('logDIV','loglist');

	//匿名函数+闭包
	var obj = function(){
		//表单验证和提交
		var handleForm = function(){
			$('#warehouse_goods_search_form_edit').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					goods_id: {
						required: true
					}
				},
				messages: {
					goods_id: {
						required: "货号不能为空."
					}
				},

				highlight: function (element) { // hightlight error inputs
					$(element).closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#warehouse_goods_search_form_edit").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=warehouse&con=WarehouseGoodsEdit&act=search';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						$('#goods_detail').html(data.content);
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

			//回车提交
			/*$('#warehouse_goods_search_form_edit input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#warehouse_goods_search_form_edit').validate().form()) {
						$('#warehouse_goods_search_form_edit').submit();
					}
					else
					{
						return false;
					}
				}
			});*/
		
		};
		var initData = function(){
		}

		return {
			init:function(){
				handleForm();//搜索提交
				initData();//处理默认数据
			}
		}
	}();

	obj.init();

});
