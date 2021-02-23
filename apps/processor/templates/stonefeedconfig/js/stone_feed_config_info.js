$import("public/js/select2/select2.min.js", function() {
    var id='<%$view->get_id()%>';
    var formID = "stone_feed_config_info";
    var Obj = function() {
        var initElements = function() {
			$('#'+formID+' select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

			$('#'+formID+' button[type="reset"]').on('click',function(){
               $('#'+formID+' select').select2('val','').change();
            });

        };

        //表单验证和提交
        var handleForm = function() {
			var url = "index.php?mod=processor&con=StoneFeedConfig&act=";
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
							if (id)
							{
								util.page(util.getItem('url'));
							}
							else
							{
								stone_feed_config_search_page(util.getItem("orl"));
							}														   
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
                    factory_id: {
                        required: true,
                    },
                    carat_min: {
                       required:true,
					   number:true
                    },
                    carat_max: {
                        required: true,
						number:true
                    },
                    prority_sort: {
						required:true,
						number:true
                    },
					cert: {
                        required: true,
                    },
					feed_type: {
                        required: true,
                    },
				},
                messages: {
                    factory_id: {
                        required: "工厂不能为空."
                    },
					carat_min: {
                        required: '石重下限不能为空',
						number:'石重下限只能填写数字'
                    },
                    carat_max: {
                        required: '石重上限不能为空',
						number:'石重上限只能填写数字'
                    },
                    prority_sort: {
						required: '优先级不能为空',
						number:"优先级排序必须为数字"
                    },
					cert: {
                        required: "证书类型不能为空",
                    },
					feed_type: {
                        required: "供料类型不能为空",
                    },
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
                    $("#stone_feed_config_info").ajaxSubmit(options1);
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