//分页
function material_inventory_search_page(url){
	util.page(url);
}
var formID = 'material_inventory_search_form';
//匿名回调
$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=MaterialInventory&act=search');//设定刷新的初始url
	util.setItem('formID',formID);//设定搜索表单id
	util.setItem('listDIV','material_inventory_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#'+formID+' select[name="warehouse_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
            $('#'+formID+' select[name="number_index"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
            $('#'+formID+' select[name="supplier_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			//util.closeForm(util.getItem("formID"));
			material_inventory_search_page(util.getItem("orl"));
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

/**
 * 库存下载
 */
function download(){
	var form = $("#material_inventory_search_form").serializeArray();
	var url ="index.php?mod=warehouse&con=MaterialInventory&act=search";
	$.each(form, function(){
		if(this.value!=''){
			url += "&"+this.name+"="+this.value;
		}
	});
	location.href=url+"&dow_info=dow_info";
}