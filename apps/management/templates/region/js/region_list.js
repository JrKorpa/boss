function region_search_page(url){
	util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=Region&act=search');
	util.setItem('listDIV','region_search_list');
	util.setItem('formID','region_search_form');
	/*alert(util.getItem('formID'));*/
	var RegionObj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#region_search_form select[name="region_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			})

			$('#region_search_form :reset').on('click', function () {
				$('#region_search_form select[name="region_type"]').select2("val", '');
			})
		};
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			region_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	RegionObj.init();
});
