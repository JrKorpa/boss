
//点击搜索后关闭搜索框
function closeSearchForm() {
    $("#searchform").trigger('click');
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=OrderOnlyProduct&act=search');
	util.setItem('listDIV','order_only_product_search_list');
	util.setItem('formID','order_only_product_search_form');
	var WarehouseGoodsObj = function(){
		
		var handleForm = function(){
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			//warehouse_goods_search_page('index.php?mod=warehouse&con=WarehouseGoods&act=search');
		};
		return {
			init:function(){				
				handleForm();
				initData();
			}
		}
	}();

	WarehouseGoodsObj.init();
});

//导出OrderOnlyProduct
function download(){
	var down_info = 'down_info';
    var ids = $("#order_only_product_search_form [name='ids']").val().replace(/\n/g,',') ;		
    var args = "&down_info="+down_info+"&ids="+ids;	
   location.href = "index.php?mod=warehouse&con=OrderOnlyProduct&act=search"+args;
	

}