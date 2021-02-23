$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'base_order_info_gift';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BaseOrderInfo&act=';//基本提交路径
    var remark = "<%$giftremark|default:''%>";
	var obj = function(){
		var initElements = function(){
			var test = $("#"+info_form_id+" input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
            $("#"+info_form_id+" input[type='checkbox']").click(function(){

               var check =  $(this).attr('checked');
                var val= $(this).val();
                if(check=="checked"){
                    $('#'+info_form_id+' input[name="gift_num['+val+']"]').css('display','inline');
                }else{
                    $('#'+info_form_id+' input[name="gift_num['+val+']"]').css('display','none');
                }

            });


		};

		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+'UpdateGift';
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
                        $('#batch_res').empty().html(data.content);
						util.xalert(
							 "修改成功!",
							function(){
                                util.retrieveReload();
                            }
						);
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
//					'user_id[]':{
//						required:true
//					},
//					abc:{
//						maxlength:20
//					}
				},
				messages: {
//					'user_id[]':{
//						required:'请选择用户'
//					},
//					abc:{
//						maxlength:'最多输入20个字符'
//					}

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
			$('#'+info_form_id+' :reset').on('click',function(){
                var htmlstr = ' <label class="control-label" style="display: block;">可选赠品：</label> <table><%foreach from=$gifts key=key item=val%><label class="radio-inline"> <input type="checkbox" value=<%$key%> <%if in_array($key,$gifta)%>checked =checked<%/if%> name="gift_id[]"> <%$val%> </label> <input name="gift_num[<%$key%>]" type="text" value="<%if array_key_exists ($key,$giftt)%><%$giftt.$key%><%else%>1<%/if%>" <%if in_array($key,$gifta)%>style="display:inline;"<%else%>style="display: none;" <%/if%> size="3" /> <%/foreach%></table>';
                $('.gift_goods_list').empty().html(htmlstr);
                $('#'+info_form_id+' textarea').val(remark);
                initElements();
			});
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