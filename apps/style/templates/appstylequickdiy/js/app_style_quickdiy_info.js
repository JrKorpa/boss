$import("public/js/select2/select2.min.js", function() {
    var app_style_quickdiy_id=0;
    var formID = "app_style_quickdiy_info";
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
			var url = "index.php?mod=style&con=AppStyleQuickdiy&act=";
            var url = app_style_quickdiy_id?url+'update' : url+'insert';
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
                        util.xalert(app_style_quickdiy_id ? "修改成功!" : "添加成功!",function(){
							if (app_style_quickdiy_id)
							{
								util.page(util.getItem('url'));
							}
							else
							{
								app_style_quickdiy_search_page(util.getItem("orl"));
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
					 url: "index.php?mod=style&con=AppStyleQuickdiy&act=getAttrHtmlAjax",
					 data: {style_sn:style_sn},
					 dataType: "JSON",
					 success: function(res){
						 if(res.success==0){
							  util.xalert(res.data);
						 }else{				 
						     $("#"+formID+" input[name='style_name']").val(res.data.style_name);
						     $("#"+formID+" select[name='caizhi']").html(res.data.caizhi);
							 $("#"+formID+" select[name='caizhiyanse']").html(res.data.caizhiyanse);
							 $("#"+formID+" select[name='xiangkou']").html(res.data.xiangkou);
							 //$("#"+formID+" #zhiquan_tip").html(res.data.zhiquan)
						 }
					 }
			   });
			 																  
		   });
            $('#'+formID).validate({
                errorElement: 'span',
                errorClass: 'help-block',
                focusInvalid: false,
                rules: {
                    style_sn: {
                        required: true,
                    },
                    caizhi: {
                       required: true,
                    },
                    caizhiyanse: {
                       required: true,
                    },
                    xiangkou: {
                       required:true,
                    },
                    zhiquan: {
                        required: true,
						number:true,
                    }
				},
                messages: {
                    style_sn: {
                        required: "款号不能为空."
                    },
                    caizhi: {
                        required:"材质不能为空",
                    },
                    caizhiyanse: {
                        required:"材质颜色不能为空",
                    },
                    xiangkou: {
                        required:"镶口不能为空",
                    },
                    zhiquan: {
                        required: '指圈不能为空',
						number:'指圈只能填写数字'
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
                    $('#'+formID).ajaxSubmit(options1);
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