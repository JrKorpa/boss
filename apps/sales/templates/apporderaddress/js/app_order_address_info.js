$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'app_order_address_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderAddress&act=';//基本提交路径
	var info_id= '<%$data.mem_address_id%>';//记录主键
    var count_id = '<%$data.mem_country_id%>';
    var province_id= '<%$data.mem_province_id%>';
    var city_id= '<%$data.mem_city_id%>';
    var regional_id= '<%$data.mem_district_id%>';

    //debugger;

	var obj = function(){
		var initElements = function(){
            if(count_id){
                $('#'+info_form_id+' select[name="country_id"]').trigger('change');
            }
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true,

			}).change(function(e){
				$(this).valid();
			});


            //点国家出现省的列表
            $('#'+info_form_id+' select[name="country_id"]').change(function(e){
                //debugger;
                $(this).valid();
                $('#'+info_form_id+' select[name="province_id"]').attr('readOnly',false).append('<option value=""></option>');
                $('#'+info_form_id+' select[name="city_id"]').empty().append('<option value=""></option>');
                $('#'+info_form_id+' select[name="regional_id"]').empty().append('<option value=""></option>');
                var t_v = $(this).val();
                if(t_v){
                    $.post(info_form_base_url+'getProvince',{count:t_v},function(data){
                        $('#'+info_form_id+' select[name="province_id"]').append(data);
                        if(province_id){
                            $('#'+info_form_id+' select[name="province_id"]').select2('val',province_id).change();
                        }
                        else
                        {
                            $('#'+info_form_id+' select[name="province_id"]').select2('val','').change();
                        }
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="province_id"]').select2('val','').attr('readOnly',false).change();
                }
            });

            //点省出现市的列表
            $('#'+info_form_id+' select[name="province_id"]').change(function(e){
                $(this).valid();
                $('#'+info_form_id+' select[name="city_id"]').attr('readOnly',false).html('<option value=""></option>');
                $('#'+info_form_id+' select[name="regional_id"]').html('<option value=""></option>');


                var t_v = $(this).val();
                if(t_v){
                    $.post(info_form_base_url+'getProvince',{count:t_v},function(data){
                        $('#'+info_form_id+' select[name="city_id"]').append(data);
                        if(city_id){
                            $('#'+info_form_id+' select[name="city_id"]').select2('val',city_id).change();
                        }
                        else
                        {
                            $('#'+info_form_id+' select[name="city_id"]').select2('val','').change();
                        }
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="city_id"]').select2('val','').attr('readOnly',false).change();
                }
            });
            //点市出现区的列表
            $('#'+info_form_id+' select[name="city_id"]').change(function(e){
                $(this).valid();
                $('#'+info_form_id+' select[name="regional_id"]').attr('readOnly',false).html('<option value=""></option>');
                var t_v = $(this).val();
                if(t_v){
                    $.post(info_form_base_url+'getProvince',{count:t_v},function(data){
                        $('#'+info_form_id+' select[name="regional_id"]').append(data);
                        if(regional_id){
                            $('#'+info_form_id+' select[name="regional_id"]').select2('val',regional_id).change();
                        }
                        else
                        {
                            $('#'+info_form_id+' select[name="regional_id"]').select2('val','').change();
                        }
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="regional_id"]').select2('val','').attr('readOnly',false).change();
                }
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success == 1 )
					{
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
 
					}
					else
					{
						util.error(data);
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
                rules: {
                    consignee:{required:true,maxlength:10},
                    tel:{required:true,maxlength:11},
                    country_id:{required:true},
                    province_id:{required:true},
                    city_id:{required:true},
                    regional_id:{required:true},
                    address:{required:true,maxlength:60},
                },
                messages: {
                    consignee:{required:"收货人必填",maxlength:"收货人不能超过10位"},
                    tel:{required:"电话必填",maxlength:"电话不能超过11位"},
                    country_id:{required:"国家必填"},
                    province_id:{required:"省必填"},
                    city_id:{required:"市必填"},
                    regional_id:{required:"区必填"},
                    address:{required:"收货地址必填",maxlength:"收货地址不能超过60位"}
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form()
				}
			});
		};
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="country_id"]').select2("val",count_id).change();
                $('#'+info_form_id+' select[name="country_id"] option[value='+count_id+']').attr('selected',true);
			});
            //debugger;
            $('#'+info_form_id+' :reset').click();
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


