function change_pro()
{
	var v = $("#problem_type").val();
	if (v == 0)
	{
		$("#problem").html("<option value='0'>请选择</option>");
		return false;
	}
	//var id =$('#problem_type').val();
	$.post("index.php?mod=warehouse&con=OrderFqc&act=get_protype",{id:v},function(data){
		$("#problem").html(data);
	})

}

$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
		],function(){
	var info_form_id = 'order_fqc_info';//form表单id
	var info_form_base_url = 'index.php?mod=warehouse&con=OrderFqc&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#order_fqc_info select[name="problem_type"], #order_fqc_info select[name="problem"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
	
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
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
						bootbox.alert("操作成功");
						$("#order_sn").focus();
						var order_sn = $("#fqc_order_sn1").val();
						var url = 'index.php?mod=warehouse&con=OrderFqc&act=search1';
						var data = {'order_sn':order_sn};
						$.post(url,data,function(e){
							$('#order_fqc_search_list').empty().append("操作成功");
						});
					}
					else
					{
						util.error(data);//错误处理
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
						problem_type: {
						required: true,
						},
						problem: {
						required: true,
						},
				},
				messages: {
					
					problem_type: {
						required: "请选择订单问题类型."
					},
					problem: {
						required: "请选择订单问题"
					},
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