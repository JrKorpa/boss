//分页
function app_receipt_deposit_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=AppReceiptDeposit&act=search');//设定刷新的初始url
	util.setItem('formID','app_receipt_deposit_search_form');//设定搜索表单id
	util.setItem('listDIV','app_receipt_deposit_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉列表美化
            $('#app_receipt_deposit_search_form select').select2({
                placeholder: "全部",
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
            $('#app_receipt_deposit_search_form :reset').click(function(){
				$('#app_receipt_deposit_search_form select').select2('val','');

            })

//            $('#app_receipt_deposit_search_form select[name=type]').on('change',function(){
//
//                var url = 'index.php?mod=finance&con=AppReceiptDeposit&act=getTree';
//                var type = $(this).val();
//                $.post(url,{type:type},function(data){
//                    if(data!='0'){
//                        $('#app_receipt_deposit_search_form select[name=pay_department]').html('').html(data);
//                    }
//                    return false;
//                });
//            });
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_receipt_deposit_search_page(util.getItem("orl"));
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

function download() {
    var status = $("#app_receipt_deposit_search_form select[name='status']").val();
    var pay_department = $("#app_receipt_deposit_search_form select[name='pay_department']").val();
    var order_sn = $("#app_receipt_deposit_search_form input[name='order_sn']").val();
    var pay_start_time = $("#app_receipt_deposit_search_form input[name='pay_start_time']").val();
    var pay_end_time = $("#app_receipt_deposit_search_form input[name='pay_end_time']").val();
    var receipt_sn = $("#app_receipt_deposit_search_form input[name='receipt_sn']").val();
    var add_start_time = $("#app_receipt_deposit_search_form input[name='add_start_time']").val();
    var add_end_time = $("#app_receipt_deposit_search_form input[name='add_end_time']").val();
    var args = "&status="+status+"&pay_department="+pay_department+"&order_sn="+order_sn+"&pay_start_time="+pay_start_time+"&pay_end_time="+pay_end_time+"&receipt_sn="+receipt_sn+"&add_start_time="+add_start_time+"&add_end_time="+add_end_time;
    location.href = "index.php?mod=finance&con=AppReceiptDeposit&act=download"+args;
}