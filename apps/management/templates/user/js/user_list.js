function user_search_page(url){
	util.page(url);
}


$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=User&act=search');//设定刷新的初始url
	util.setItem('formID','user_search_form');//设定搜索表单id
	util.setItem('listDIV','user_search_list');//设定列表数据容器id
	var UserListObj = function(){
		var initElements = function(){
			$('#user_search_form select[name="user_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#user_search_form select[name="role_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});

			$('#user_search_form select[name="is_on_work"]').select2({
				placeholder: "请选择",
				allowClear: true
			});

			$('#user_search_form :reset').on('click',function(){
				$('#user_search_form select[name="user_type"]').select2("val","");
				$('#user_search_form select[name="is_on_work"]').select2("val","");
			})
		}
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			user_search_page(util.getItem("orl"));
		}

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}	
	}();

	UserListObj.init();
});