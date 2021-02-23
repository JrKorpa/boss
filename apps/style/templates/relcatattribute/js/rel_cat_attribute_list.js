//分页
function rel_cat_attribute_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=style&con=RelCatAttribute&act=search');//设定刷新的初始url
	util.setItem('formID','rel_cat_attribute_search_form');//设定搜索表单id
	util.setItem('listDIV','rel_cat_attribute_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var Obj = function(){
		
		var initElements = function(){
			
			//产品线
			$('#rel_cat_attribute_search_form select[name="product_type_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件	

			//分类
			$('#rel_cat_attribute_search_form select[name="cat_type_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件	

			//属性
			$('#rel_cat_attribute_search_form select[name="attribute_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				escapeMarkup: function(m) { return m; }
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件	
			
			
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			rel_cat_attribute_search_page(util.getItem("orl"));

			$('#rel_cat_attribute_search_form :reset').on('click',function(){
				$('#rel_cat_attribute_search_form select[name="cat_type_id"]').select2("val","");
				$('#rel_cat_attribute_search_form select[name="attribute_id"]').select2("val","");
				$('#rel_cat_attribute_search_form select[name="product_type_id"]').select2("val","");
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

	Obj.init();
});