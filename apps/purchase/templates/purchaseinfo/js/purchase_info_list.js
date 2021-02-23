function purchase_info_search_page(url){
	util.page(url);
}

$import('public/js/select2/select2.min.js',function(){
	util.setItem('orl','index.php?mod=purchase&con=PurchaseInfo&act=search');
	util.setItem('listDIV','purchase_info_search_list');
	util.setItem('formID','purchase_info_search_form');
		var PurchaseInfoObj = function(){
		var initElements = function(){
			$('#purchase_info_search_form select[name="t_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			
			$('#purchase_info_search_form select[name="p_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#purchase_info_search_form select[name="put_in_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
                            
		};
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			 $('#purchase_info_search_form :reset').on('click',function(){
				$('#purchase_info_search_form select').select2("val",'');
			})
			util.closeForm(util.getItem("formID"));
			purchase_info_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	PurchaseInfoObj.init();
});
