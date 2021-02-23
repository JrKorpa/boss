$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'app_order_address_info_2';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderAddress&act=';//基本提交路径
    var order_id = '<%$order_id%>';
    var member_id = '<%$member_id%>';
    var distribution_type='<%$distribution_type%>';
     var  dturl = info_form_base_url+"getDistributionType";
     var count_id = "<%$member_address.country_id|default:''%>";
    var province_id="<%$member_address.province_id|default:''%>";
    var city_id="<%$member_address.city_id|default:''%>";
    var regional_id="<%$member_address.regional_id|default:''%>";

   
    //debugger;
	var obj = function(){
		var initElements = function(){
            //美化各种特殊处理
            if (!jQuery().uniform) {
                return;
            }
            $('#'+info_form_id+' select[name=shop_id]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                $(this).valid();
                var url = info_form_base_url+"getShopInfo";
                     var shop_id =$(this).val();
             
                if(shop_id==''){
                    $('#shop_cfg_info').empty();
                    return false;
                }
                $.post(url,{shop_id:shop_id},function(data){
                    if(data.success==1){
                        $('#shop_cfg_info').val(data.error);
                    }
                });
            });
            $('#'+info_form_id+' select').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                $(this).valid();
            });
            
            $('#'+info_form_id+' select[name=distribution_type]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
               var  dturl = info_form_base_url+"selectaddress";
               var  distribution_type= $(this).val();
                if(distribution_type==2){
                    $('#'+info_form_id+' input[name=address]').attr('readOnly',false);
                    document.getElementById('address_mendian_info').style.display = "none" ;
                    document.getElementById('address_mendian_info1').style.display = "" ;
                  
                    return false;
                }
                else if(distribution_type==1){
                    $('#'+info_form_id+' input[name=address]').attr('readOnly',true);
                    document.getElementById('address_mendian_info1').style.display = "none" ;
                    document.getElementById('address_mendian_info').style.display = "" ;
                    return false;
                }
                
                
            });
            $('#'+info_form_id+' select[name=shop_type]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                $(this).valid();
                var url =info_form_base_url+"getShopList"
                var shop_type=$(this).val();
                $('#'+info_form_id+' select[name=shop_id]').select2('val','');
                $('#'+info_form_id+' input[name=address]').val('');
                $.post(url,{shop_type:shop_type}, function (data) {
                    $('#'+info_form_id+' select[name=shop_id]').empty().html(data);
                    if(shop_id){
                        $('#'+info_form_id+' select[name=shop_id]').select2('val',shop_id).change();
                    }
                });
                
                
            });
       

            
            
            $('#'+info_form_id+' select[name="country_id"]').change(function(e){

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
			var url = info_form_base_url+'insertorderadd';
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
					if(data.success == 1 )
					{
                        $('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(
							'添加收货地址成功',
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
                    consignee:{
                        required:true
                    },
                    tel:{
                        required:true,
                        is_Num:true
                    },
                    shop_type:{
                        required:true
                    },
                    country_id:{
                        required:true
                    },
                    province_id:{
                        required:true
                    },
                    city_id:{
                        required:true
                    },

                    address:{
                        required:true
                    },

					zipcode:{
						digits:true
					},
					email:{
						email:true
					},
					distribution_type:{
						required:true
					},
                    express_id:{
                        required:true
                    },
                    shop_id:{
                        required:true
                    }

				},
				messages: {
                    consignee:{
                        required:"收货人必填",
                    },
                    tel:{
                        required:"电话必填",
                        is_Num:"电话输入不合法"
                    },
                    shop_type:{
                        required:"体验店类型必选"
                    },
                    country_id:{
                        required:"国家必选",
                    },
                    province_id:{
                        required:"省必选",
                    },


                    city_id:{
                        required:"区必选",
                    },

                    address:{
                        required:"收货地址必填",
                    },

					zipcode:{
						digits:"只能输入数字"
					},
					email:{
						email:"email格式不正确"
					},
					distribution_type:{
						required:'请选择配送方式'
					},
                    express_id:{
						required:'请选择快递公司'
					},
                    shop_id:{
						required:'体验店必填'
					}

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
            if(count_id){
                $('#'+info_form_id+' select[name="country_id"]').select2("val",count_id).change();
            }
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


