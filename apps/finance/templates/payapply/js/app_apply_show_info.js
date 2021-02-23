
function pay_apply_save(o){
	$('#pay_apply_show_form').submit();	
}

$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'pay_apply_show_form';//form表单id
	var info_form_base_url = 'index.php?mod=finance&con=PayApply&act=';//基本提交路径
	var id= '<%$info['apply_id']|default:0%>';

	var obj = function(){
		var initElements = function(){
			$('#pay_apply_show_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(id ? 'update' : 'insert');
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();//刷新查看页签
								util.syncTab(data.tab_id);
							}
						);
					}
					else
					{
						util.error(data.error);//错误处理
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					prc_id:{required:true},
					type:{required:true},
				},
				messages: {
					prc_id:{required:"请选择供应商"},
					type:{required:"请选择应付类型"},
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
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		};
		var initData = function(){
			var orl = 'index.php?mod=finance&con=PayApply&act=showDetaillist&id='+id+'&apply_status='+<%$info['status']%>+'&show='+<%$show%>;
			util.setItem('orl',orl);
			util.setItem('listDIV','pay_apply_show_list'+id);
			pay_apply_show_list_page(util.getItem("orl"));
		
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});
function pay_apply_show_list_page(url){
	util.page(url);
}