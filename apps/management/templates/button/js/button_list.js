function button_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=Button&act=search');//设定刷新的初始url
	util.setItem('formID','button_search_form');//设定搜索表单id
	util.setItem('listDIV','button_search_list');//设定列表数据容器id
	var ButtonObj = function(){
		var initElements = function(){
			$('#button_search_form select[name="c_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#button_search_form :reset').on('click',function(){
				$('#button_search_form select[name="c_id"]').select2("val","");
			})
		};
		var handleForm = function(){
			util.search();
		}
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			button_search_page(util.getItem("orl"));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	ButtonObj.init();
});
