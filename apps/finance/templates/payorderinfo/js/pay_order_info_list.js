//分页
function pay_order_info_search_page(url){
	util.page(url);
}

function download(){
	var down_info ="down_info";
    var kela_sn = $("#pay_order_info_search_form [name='kela_sn']").val();
    var pay_name = $("#pay_order_info_search_form [name='pay_name']").val();
    var department = $("#pay_order_info_search_form [name='department']").val();
    var from_ad = $("#pay_order_info_search_form [name='from_ad']").val();
    var external_id = $("#pay_order_info_search_form [name='external_id']").val();
    var apply_number = $("#pay_order_info_search_form [name='apply_number']").val();
    var status = $("#pay_order_info_search_form [name='status']").val();
    var order_time_start = $("#pay_order_info_search_form [name='order_time_start']").val();
    var order_time_end = $("#pay_order_info_search_form [name='order_time_end']").val();
    var shipping_time_start = $("#pay_order_info_search_form [name='shipping_time_start']").val();
    var shipping_time_end = $("#pay_order_info_search_form [name='shipping_time_end']").val();
    var storage_mode = $("#storage_mode").val();
    
    var args = "&down_info="+down_info+"&kela_sn="+kela_sn+"&pay_name="+pay_name+"&department="+department+"&from_ad="+from_ad+"&external_id="+external_id+"&apply_number="+apply_number+"&status="+status+"&order_time_start="+order_time_start+"&order_time_end="+order_time_end+"&shipping_time_start="+shipping_time_start+"&shipping_time_end="+shipping_time_end+"&storage_mode="+storage_mode;
    location.href = "index.php?mod=finance&con=PayOrderInfo&act=search"+args;
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function(){
	util.setItem('orl','index.php?mod=finance&con=PayOrderInfo&act=search');//设定刷新的初始url
	util.setItem('formID','pay_order_info_search_form');//设定搜索表单id
	util.setItem('listDIV','pay_order_info_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉列表美化
            $('#pay_order_info_search_form select').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            //时间控件
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }

            //重置
            $('#pay_order_info_search_form :reset').on('click',function(){
                $('#pay_order_info_search_form select[name="pay_name"]').select2('val','');
                $('#pay_order_info_search_form select[name="department"]').select2('val','');
                $('#pay_order_info_search_form select[name="storage_mode[]"]').select2('val','');
                $('#pay_order_info_search_form select[name="status"]').select2('val','');
                $('#pay_order_info_search_form select[name="from_ad"]').select2('val','');
            })
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			pay_order_info_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});