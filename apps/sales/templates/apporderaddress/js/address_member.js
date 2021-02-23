$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'address_member_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderAddress&act=';
    var count_id = "<%$member_address.mem_country_id|default:''%>";
    var province_id="<%$member_address.mem_province_id|default:''%>";
    var city_id="<%$member_address.mem_city_id|default:''%>";
    var regional_id="<%$member_address.mem_district_id|default:''%>";

	var obj = function(){
		var initElements = function(){
            //美化各种特殊处理
            if (!jQuery().uniform) {
                return;
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
		var handleForm = function(){};
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


