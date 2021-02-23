//分页
function dealer_customer_manage_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
         "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=dealer&con=DealerCustomerManage&act=search');//设定刷新的初始url
	util.setItem('formID','dealer_customer_manage_search_form');//设定搜索表单id
	util.setItem('listDIV','dealer_customer_manage_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			
			$('#dealer_customer_manage_search_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
			
			if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			dealer_customer_manage_search_page(util.getItem("orl"));

            $('#dealer_customer_manage_search_form button[type="reset"]').on('click',function(){
                $('#dealer_customer_manage_search_form select[name="status"]').select2('val','').change();
                $('#dealer_customer_manage_search_form select[name="spread_id[]"]').select2('val',[]).change();
                $('#dealer_customer_manage_search_form select[name="source_channel[]"]').select2('val',[]).change();
                $('#dealer_customer_manage_search_form select[name="source[]"]').select2('val',[]).change();
            });
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

function open_sub_table(obj){
    if($(obj).find('i').hasClass('fa-plus')){
        $(obj).find('i').removeClass('fa-plus').addClass('fa-minus');
        $(obj).parent().parent().next().show();
    }else{
        $(obj).find('i').removeClass('fa-minus').addClass('fa-plus');
        $(obj).parent().parent().next().hide();
    }
}

function downloads(){
    var down_infos = 'downs';
    var customer_name = $("#dealer_customer_manage_search_form [name='customer_name']").val();
    var tel = $("#dealer_customer_manage_search_form [name='tel']").val();
    var email = $("#dealer_customer_manage_search_form [name='email']").val();
    var district = $("#dealer_customer_manage_search_form [name='district']").val();
    var follower = $("#dealer_customer_manage_search_form [name='follower']").val();
    var status = $("#dealer_customer_manage_search_form [name='status']").val();
    //var source_channel = $("#dealer_customer_manage_search_form [name='source_channel']").val();
    var source_channel = $('#dealer_customer_manage_search_form select[name="source_channel[]"]').val();
    var source = $("#dealer_customer_manage_search_form select[name='source[]']").val();
    var spread_id = $("#dealer_customer_manage_search_form [name='spread_id[]']").val();
    var text_item = $("#dealer_customer_manage_search_form [name='text_item[]']").val();
    var start_time = $("#dealer_customer_manage_search_form [name='start_time']").val();
    var end_time = $("#dealer_customer_manage_search_form [name='end_time']").val();
    var param = "&down_infos="+down_infos+"&customer_name="+customer_name+"&tel="+tel+"&email="+email+"&district="+district+"&follower="+follower+"&status="+status+"&source_channel="+source_channel+"&source="+source+"&spread_id="+spread_id+"&start_time="+start_time+"&end_time="+end_time+"&text_item="+text_item;
    url = "index.php?mod=dealer&con=DealerCustomerManage&act=search"+param;
    window.open(url);
}