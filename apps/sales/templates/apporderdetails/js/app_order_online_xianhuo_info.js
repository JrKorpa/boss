
// 测试本地解析
function order_detil_kezi_out_goods_edit() {
	var inputText = $('.emotion').val();
	$('#app_order_xianhuo_info').html(AnalyticEmotion(inputText));
}

$import(['public/js/jquery.sinaEmotion.css','public/js/jquery.sinaEmotion.js','public/js/select2/select2.min.js'],function(){
	var info_form_id = 'app_order_online_xianhuo_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderDetails&act=';//基本提交路径
    

	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select').select2({
					placeholder: "请选择",
				}).change(function(e){
					$(this).valid();
				});	
			$('#app_order_xianhuo_info').SinaEmotion($('.emotion'));
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'on_linezhuanxianhuo';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success == 1 )
					{
						util.xalert(
							"转现成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
 
					}
					else
					{
						util.error(data);
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form()
				}
			});
		};
		var initData = function(){
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				//initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});


