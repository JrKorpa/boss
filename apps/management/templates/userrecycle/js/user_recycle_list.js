function user_recycle_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=UserRecycle&act=search');
	util.setItem('formID','user_recycle_search_form');
	util.setItem('listDIV','user_recycle_search_list');
	var UserRecycleObj = function(){
		var initElements = function(){
			$('#user_recycle_search_form select[name="user_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			});

			$('#user_recycle_search_form select[name="is_on_work"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#user_recycle_search_form :reset').on('click',function(){
				$('#user_recycle_search_form select[name="user_type"]').select2("val","");
				$('#user_recycle_search_form select[name="is_on_work"]').select2("val","");
			})
		}
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			user_recycle_search_page(util.getItem("orl"));
		}

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}	
	}();
	UserRecycleObj.init();
});