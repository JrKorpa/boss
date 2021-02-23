$import("public/js/select2/select2.min.js", function() {
    var price_by_style_id = '<%$view->get_id()%>';
    var price_by_style_style_id = '<%$view->get_style_id()%>';
    var Obj = function() {
        var initElements = function() {
            //初始化下拉按钮组
            $('#price_by_style_info select[name="cert"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件

            $('#price_by_style_info select[name="zuan_shape"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
             
			$('#price_by_style_info select[name="sell_type"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            
			$('#price_by_style_info select[name="zuan_yanse_max"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			$('#price_by_style_info select[name="zuan_yanse_min"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			$('#price_by_style_info select[name="zuan_jindu_max"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			$('#price_by_style_info select[name="zuan_jindu_min"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            
			$('#price_by_style_info select[name="tuo_type"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			$('#price_by_style_info select[name="caizhi"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			$('#price_by_style_info select[name="stone_cat"]').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
        };

        //表单验证和提交
        var handleForm = function() {
            var url = price_by_style_id ? 'index.php?mod=style&con=PriceByStyle&act=update' : 'index.php?mod=style&con=PriceByStyle&act=insert';
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
					$('#price_by_style_info :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							price_by_style_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();//刷新查看页签
								util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
							}
						);
					}
					else	
					{
						util.error(data);//错误处理
					}
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };

            $('#price_by_style_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    price: {
                        required: true
                    },
                },
                messages: {
                    price: {
                        required: "定价不能为0."
                    },
                },
                highlight: function(element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
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
                    $("#price_by_style_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#price_by_style_info input').keypress(function(e) {
                if (e.which == 13) {
                    if ($('#price_by_style_info').validate().form()) {
                        $('#price_by_style_info').submit();
                    }
                    else
                    {
                        return false;
                    }
                }
            });
        };
        var initData = function() {
            //下拉组件重置
            $('#price_by_style_info :reset').on('click', function() {
                //$('#price_by_style_info select[name="tuo_type"]').select2("val", tuo_type).change();

            })
            if (price_by_style_id)
            {//修改
                $('#price_by_style_info :reset').click();
            }
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