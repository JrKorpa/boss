//分页
function warehouse_box_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js"], function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBox&act=search');//设定刷新的初始url
	util.setItem('formID','warehouse_box_search_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_box_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
			if (!jQuery().uniform){
				return;
			}
			//初始化下拉组件
			$('#warehouse_box_search_form select[name="warehouse_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_box_search_page(util.getItem("orl"));
			$('#warehouse_box_search_form :reset').on('click',function(){
				//下拉重置
				$('#warehouse_box_search_form select[name="warehouse_id"]').select2("val",'');
			})
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