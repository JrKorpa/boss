$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/jquery.validate.extends.js"],function(){
//打印条码


	var base_salepolicy_info_info_id ='<%$view->get_policy_id()%>';
    
    

	var baseMemberInfoInfogObj = function(){
	
    	var initElements = function(){
    	   
             
            $("#base_salepolicy_info_info input[name='bill_type_select']").click(function(){
                var check =  $(this).attr('checked');
                var val= $(this).val();
                if(check=="checked"){
                    
                    $("#base_salepolicy_info_info input[name='bill_no']").removeAttr('readonly');
                    }
                else{
                    $("#base_salepolicy_info_info input[name='bill_no']").attr('readonly','readonly');
                }
            });
           
    	};
			if (!jQuery().uniform) {
				return;
			}

        var test = $("#base_salepolicy_info_info input[type='checkbox']:not(.toggle, .make-switch)");
        if (test.size() > 0) {
            test.each(function () {
                if ($(this).parents(".checker").size() == 0) {
                    $(this).show();
                    $(this).uniform();
                }
            });
        }
        
      
      
			$('#base_salepolicy_info_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});		
			
			
            var dateobj = new Date();
            var month  =dateobj.getMonth()+1;
            var mindata = dateobj.getFullYear()+'-'+month+'-'+dateobj.getDate();

			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
                    startDate: mindata,

				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

		var handleForm = function(){
			//表单验证和提交
			var url = base_salepolicy_info_info_id ? 'index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=update' : 'index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{					
					util.xalert(
							"请求超时，请检查链接!",
							function(){
								util.retrieveReload();								
							}
						);
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(
							base_salepolicy_info_info_id ? "修改成功!": "添加成功!  "+data.msg,
							function(){
								util.retrieveReload();								
							}
						);
						if (base_salepolicy_info_info_id)
						{//刷新当前页
							util.page(util.getItem('url'));
						}
						else
						{//刷新首页
							base_salepolicy_info_search_page(util.getItem("orl"));
						}
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						//alert(data.error ? data.error : (data ? data :'程序异常'));
						util.xalert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					util.xalert('数据加载失败');
				}
			};

			$('#base_salepolicy_info_info').validate({
				errorElement: 'span', //default input error base_salepolicy_info container
				errorClass: 'help-block', // default input error base_salepolicy_info class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    member_name: {
                        required: true,
                        checkName: true
                    },
                    member_type: {
                        required: true,
                    },
                    policy_name: {
                        required: true,
                        maxlength:60,
                    },
                    sta_value: {
                        required: true,
                        number:true
                    },
                    jiajialv: {
                        required: true,
                        number:true
                    },
				},
				messages: {
                    member_name: {
                        required: "会员名不能为空."
                    },
                    member_type: {
                        required: "会员类型不能为空."
                    },
                    policy_name: {
                        required: "销售策略名称最长是60."
                    },
                    sta_value: {
                        required: "固定值不能为空！",

                    },
                    jiajialv: {
                        required: "加价率不能为空！",
                    },
				},
                highlight: function(element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#base_salepolicy_info_info").ajaxSubmit(options1);
                }
			});
			//回车提交
			$('#base_salepolicy_info_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#base_salepolicy_info_info').validate().form()) {
						$('#base_salepolicy_info_info').submit();
					}
					else
					{
						return false;
					}
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
	baseMemberInfoInfogObj.init();
});

