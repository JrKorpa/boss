//导出csv
function deliver_goods_export_cxv_index(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['start_time']=document.getElementById("start_time").value;
	param['end_time']=document.getElementById("end_time").value;
	param['search_type']=document.getElementById("search_type").value;
	param['department_name']=document.getElementById("department_name").value;
	param['zt_type']=document.getElementById("zt_type").value;
	if(!param['search_type']){
		util.xalert('请选择订单类型');
		return;
	}
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
//分页
function app_old_order_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=DeliverGoodsReport&act=search');//设定刷新的初始url
	util.setItem('formID','app_old_order_search_form');//设定搜索表单id
	util.setItem('listDIV','app_old_order_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#base_order_info_search_form select').select2({
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


			$('#app_old_order_search_form :reset').on('click',function(){
				$('#app_old_order_search_form select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_old_order_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});