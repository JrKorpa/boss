$import("public/js/select2/select2.min.js", function() {
    var id='<%$view->get_id()%>';
    var formID = "base_cpdz_code_info_form";
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
			var url = "index.php?mod=sales&con=BaseCpdzCode&act=";
            var url = id?url+'update' : url+'insert';
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
                        util.xalert(id ? "修改成功!" : "添加成功!",function(){
							base_cpdz_code_search_page(util.getItem("orl"));											   
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

            $('#'+formID).validate({
                errorElement: 'span',
                errorClass: 'help-block',
                focusInvalid: false,
                rules: {
                    style_channel: {
                        required: true,
                    },
                    price: {
                       required:true,
					   number:true
                    }
				},
                messages: {
                    style_channel: {
                        required: "请选择款式来源渠道"
                    },
                    price: {
                        required:"成交价不能为空",
						number:'成交价必须为数字'
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

			$('#'+formID+' button[type="reset"]').on('click',function(){
				$('#'+formID+' select').select2('val','').change();
            });
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