$import("public/js/select2/select2.min.js", function() {
    var product_info_id='<%$view->get_id()%>';
    var formID = "quick_diy_edit_form";
    var Obj = function() {
        var initElements = function() {
			$('#'+formID+' select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });			
        };

        //表单验证和提交
        var handleForm = function() {
			var url = "index.php?mod=processor&con=ProductInfo&act=updateQuickDiy";
            var options1 = {
                url: url,
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        util.xalert("修改成功!",function(){
							util.retrieveReload();																	   
				       });
                        
                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
    					 util.xalert(data.error ? data.error : (data ? data : '程序异常'));
                    }
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };
           
            $("#"+formID).validate({
                errorElement: 'span',
                errorClass: 'help-block',
                focusInvalid: false,
                rules: {
                    is_quick_diy: {
                        required: true,
                    },
                    remark: {
                       required: true,
                    }
				},
                messages: {
                    is_quick_diy: {
                        required: "是否快速定制不能为空"
                    },
                    remark: {
                        required:"修改原因不能为空",
                    }
                },
                highlight: function(element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#"+formID).ajaxSubmit(options1);
                }
            });            
        };
        var initData = function() {


        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    Obj.init();
});