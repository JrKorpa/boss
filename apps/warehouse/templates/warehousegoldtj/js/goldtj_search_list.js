function gold_search_page(url){
    util.page(url);
}

function execHandler() { 
   var down_info = 'down_info';
    var start_time = $("#warehousegoldtj_search_form [name='start_time']").val();
    var end_time = $("#warehousegoldtj_search_form [name='end_time']").val();
    var zdj =  $("input[type='radio']:checked").val();

    var args = "&down_info="+down_info+"&start_time="+start_time+"&end_time="+end_time+"&zdj="+zdj;
	  			
    location.href = "index.php?mod=warehouse&con=WarehouseGoldTj&act=search"+args;
}


$('input[name="zdj"]').on('click',function(){
	util.setItem('orl', util.getItem('orl').replace(/(zdj=)([^&]*)/gi,"zdj=" + $(this).val()));
})


//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
    util.setItem('orl','index.php?mod=warehouse&con=WarehouseGoldTj&act=search&zdj=real_all');//设定刷新的初始url  /index.php?mod=warehouse&con=WarehouseGoldTj&act=index
    util.setItem('formID','warehousegoldtj_search_form');//设定搜索表单id
    util.setItem('listDIV','warehousegoldtj_search_list');//设定列表数据容器id

    //匿名函数+闭包
    var obj = function(){

        var initElements = function(){

            $('#warehousegoldtj_search_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#warehousegoldtj_search_form select[name="order_pay_status"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });


            $('#warehousegoldtj_search_form :reset').on('click',function(){
                $('#warehousegoldtj_search_form select').select2("val","");
            })
        };

		
		 if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
			
        var handleForm = function(){
            util.search();
        };

        var initData = function(){
            util.closeForm(util.getItem("formID"));
         //   unckeckorder_search_page(util.getItem("orl"));
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