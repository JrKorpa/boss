function menu_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=Menu&act=search');//设定刷新的初始url
	util.setItem('formID','menu_search_form');//设定搜索表单id
	util.setItem('listDIV','menu_search_list');//设定列表数据容器id
	var MenuListObj = function(){
		var initElements = function(){
			$('#menu_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});

			$('#menu_search_form :reset').on('click',function(){
				$('#menu_search_form select[name="group_id"]').select2("val","");
			})
		}

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			menu_search_page(util.getItem("orl"));
		}

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	MenuListObj.init();
});