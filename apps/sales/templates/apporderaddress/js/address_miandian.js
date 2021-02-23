$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'address_mendian_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=AppOrderAddress&act=';
    var shop_type = "<%$shop.shop_type|default:''%>";
    var shop_id = "<%$shop.id|default:''%>";
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
                        $('#shop_cfg_info').empty().html(data.error);
                    }
                });
            });

            $('#'+info_form_id+' select[name=shop_type]').select2({
                placeholder: "请选择",
                allowClear: true,

            }).change(function(e){
                $(this).valid();
                var url =info_form_base_url+"getShopList"
                var shop_type=$(this).val();
                $('#'+info_form_id+' select[name=shop_id]').select2('val','');
                $.post(url,{shop_type:shop_type}, function (data) {
                    $('#'+info_form_id+' select[name=shop_id]').empty().html(data);
                    if(shop_id){
                        $('#'+info_form_id+' select[name=shop_id]').select2('val',shop_id).change();
                    }
                });
            })
            if(shop_type){
                $('#'+info_form_id+' select[name=shop_type]').select2('val',shop_type).change();
            }
        };


		
		//表单验证和提交
		var handleForm = function(){};
		var initData = function(){};
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


