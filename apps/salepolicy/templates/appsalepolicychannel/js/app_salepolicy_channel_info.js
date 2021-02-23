function app_salepolicy_channel_search_page(url){
	util.page(url);
}
$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var app_salepolicy_channel_info_id ='<%$view->get_id()%>';
	var AppSalepolicyChannelObj = function(){
		var initElements = function(){};
			if (!jQuery().uniform) {
				return;
			}
			$('#app_salepolicy_channel_info select[name="channel"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			$('#app_salepolicy_channel_info select[name="shop_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				//自动过滤
				getinfolist(_t);
			});
			$('#app_salepolicy_channel_info select[name="channel_class"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				//自动过滤
				getinfolist(_t);
			});
			$('#app_salepolicy_channel_info select[name="channel_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				//自动过滤
				getinfolist(_t);
			});
			
			$('#app_salepolicy_channel_info select[name="channel_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			
			
			var getinfolist = function(id)
			{
				/*uploda bu liulinyan 增加过滤*/
				$('#region_info select[name="channel_id[]"]').empty();
				$('#region_info select[name="channel_id[]"]').append('<option value=""></option>');
				//获取类型
				var typeid = $('#app_salepolicy_channel_info select[name="shop_type"]').val();
				//获取一级分类
				var _onelev = $('#app_salepolicy_channel_info select[name="channel_class"]').val();
				//获取二级分类
				var _twolev = $('#app_salepolicy_channel_info select[name="channel_type"]').val();
				//发起请求
				if (id) {
				$.post(
					'index.php?mod=salepolicy&con=AppSalepolicyChannel&act=getqdlist',
					{shop_type: id,channel_class:_onelev,channel_type:_twolev},
					function (data)
					{
						$('#app_salepolicy_channel_info select[name="channel_id[]"]').html(data);
					});
				}
			}
			
			
			
			var handleForm = function(){
			//表单验证和提交
			var url = app_salepolicy_channel_info_id ? 'index.php?mod=salepolicy&con=AppSalepolicyChannel&act=update' : 'index.php?mod=salepolicy&con=AppSalepolicyChannel&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert(app_salepolicy_channel_info_id ? "修改成功!": "添加成功!");
						if (app_salepolicy_channel_info_id)
						{//刷新当前页
							util.retrieveReload(this);
						}
						else
						{//刷新首页
                            util.retrieveReload(this);
						}
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");
				}
			};

			$('#app_salepolicy_channel_info').validate({
				errorElement: 'span', //default input error app_salepolicy_channel container
				errorClass: 'help-block', // default input error app_salepolicy_channel class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
				},
				messages: {
				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
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
					$("#app_salepolicy_channel_info").ajaxSubmit(options1);
				}
			});
		}
		var initData = function(){}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	AppSalepolicyChannelObj.init();
});