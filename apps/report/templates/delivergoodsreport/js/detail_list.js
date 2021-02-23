//分页
function delivergoodsreport_detail_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	var add_url="&start_time="+start_time+"&end_time="+end_time+"&bill_type="+bill_type+"&zt_type="+zt_type+"&department_name="+department_name;
	var add_url=add_url+"&department_ids="+department_ids;
	util.setItem('orl','index.php?mod=report&con=DeliverGoodsReport&act=detail_list_ajax'+add_url);//设定刷新的初始url
	util.setItem('formID','delivergoodsreport_detail_list_search_form');//设定搜索表单id
	util.setItem('listDIV','delivergoodsreport_detail_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
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
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			delivergoodsreport_detail_search_page(util.getItem("orl"));
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