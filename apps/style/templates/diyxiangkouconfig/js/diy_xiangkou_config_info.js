$import("public/js/select2/select2.min.js", function() {
    var diy_xiangkou_config_id='<%$view->get_id()%>';
	var diy_xiangkou_config_xiangkou='<%$view->get_xiangkou()%>';
    var formID = "diy_xiangkou_config_info";
    var Obj = function() {
        var initElements = function() {
			$('#diy_xiangkou_config_info select').select2({
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
			var url = "index.php?mod=style&con=DiyXiangkouConfig&act=";
            var url = diy_xiangkou_config_id?url+'update' : url+'insert';
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
                        util.xalert(diy_xiangkou_config_id ? "修改成功!" : "添加成功!",function(){
							if (diy_xiangkou_config_id)
							{
								util.page(util.getItem('url'));
							}
							else
							{
								diy_xiangkou_config_search_page(util.getItem("orl"));
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
			
           $("#"+formID+" input[name='style_sn']").blur(function(){
			    var style_sn = $.trim($(this).val());
				if(style_sn==""){
				    return false;
				}
				$.ajax({
					 type: "POST",
					 url: "index.php?mod=style&con=DiyXiangkouConfig&act=getAttrHtmlAjax",
					 data: {style_sn:style_sn},
					 dataType: "JSON",
					 success: function(res){
						 if(res.success==0){
							  util.xalert(res.data);
						 }else{				 
							 $("#"+formID+" select[name='xiangkou']").html(res.data.xiangkou);
							 if(diy_xiangkou_config_id){
								 $("#"+formID+" select[name='xiangkou']").select2('val',diy_xiangkou_config_xiangkou).change(); 
							 }
						 }
					 }
			   });
			 																  
		   });
		   if(diy_xiangkou_config_id){
			   $("#"+formID+" input[name='style_sn']").blur(); 
			   
		   }
            $('#'+formID).validate({
                errorElement: 'span',
                errorClass: 'help-block',
                focusInvalid: false,
                rules: {
                    style_sn: {
                        required: true,
                    },
                    xiangkou: {
                       required:true,
                    },
                    carat_lower_limit: {
                        required: true,
						number:true
                    },
                    carat_upper_limit: {
                        required: true,
						number:true
                    }
				},
                messages: {
                    style_sn: {
                        required: "款号不能为空."
                    },
                    xiangkou: {
                        required:"镶口不能为空",
                    },
					carat_lower_limit: {
                        required: '石重下限不能为空',
						number:'石重下限只能填写数字'
                    },
                    carat_upper_limit: {
                        required: '石重上限不能为空',
						number:'石重上限只能填写数字'
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
                    $("#diy_xiangkou_config_info").ajaxSubmit(options1);
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