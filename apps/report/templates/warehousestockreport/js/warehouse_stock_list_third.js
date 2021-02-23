//分页
function warehouse_stock_third_search_page(url){
	util.page(url);
}


//匿名回调
$import("public/js/select2/select2.min.js",function(){
	var url='index.php?mod=report&con=WarehouseStockReport&act=search_third';
	util.setItem('orl',url);//设定刷新的初始url
	util.setItem('formID','warehouse_stock_search_form_third');//设定搜索表单id
	util.setItem('listDIV','warehouse_stock_search_list_third');//设定列表数据容器id

	//匿名函数+闭包
	var ApplicationListObj = function(){

		var initElements = function(){
			//初始化下拉组件
			$('#warehouse_stock_search_form_third select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });
			$('#warehouse_stock_search_form_third :reset').on('click',function(){
				$('#warehouse_stock_search_form_third select').select2("val","");
			})

		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_stock_third_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}
	}();

	ApplicationListObj.init();
});