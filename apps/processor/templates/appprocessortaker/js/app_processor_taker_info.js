$import(function(){
	var info_form_id = 'app_processor_taker_info';//form表单id
	var info_form_base_url = 'index.php?mod=processor&con=AppProcessorTaker&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	var supplier_id= '<%$view->get_supplier_id()%>';

	var obj = function(){
		var initElements = function(){
			//下拉框美化
			$('#app_processor_taker_info select[name="kela_user"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e) {
				var user = $(this).val();
				if(!user){return}
				var ids = [];
				$('#app_processor_taker_info input[name="data[]"]').each(function(){
					ids.push($(this).val());
				});
				//判断是否已存在
				var flag = false;
				for(var x in ids){
					if(ids[x] == user){
						flag = true;
						break;
					}
				}
				if(flag){
					$('#processor_taker_select').empty().append("<b>该用户已存在</b>");
				}else{
					$('#processor_taker_select').empty();
					var _t = $(this).find("option:selected");
					$('#app_processor_taker_info input[name="real_name"]').val(_t.attr("data-name"));
					$('#app_processor_taker_info input[name="account"]').val(_t.attr("data-account"));
					$('#app_processor_taker_info input[name="mobile"]').val(_t.attr("data-mobile"));
					$('#app_processor_taker_info input[name="buyer_id"]').val(_t.attr("data-id"));
					$('#app_processor_taker_info input[name="user_papers"]').val(_t.attr("data-icd"));

					var str = "<tr><td>"+_t.attr("data-name")+"</td><td>"+_t.attr("data-account")+"</td><td>"+_t.attr("data-mobile")+"</td><td>"+_t.attr("data-icd")+"</td><td><span class='btn btn-xs red' onclick='del_buyer(this)'><i class='fa fa-times'>删除</i></span></td>";
					str += "<input type='hidden' name='data[]' value='"+_t.attr("data-id")+"' from='app_processor_buyer_info' /></tr>";
					$('#processor_taker_info_tbody').append(str);
				}
				$(this).valid();
			});

			$('#app_processor_taker_info .close-btn').click(function(){
				$('.modal-scrollable').trigger('click');
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
						util.xalert("操作成功!");
						var url=util.getItem('orl');
						url+="&supplier_id="+supplier_id;
						util.page(url);
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