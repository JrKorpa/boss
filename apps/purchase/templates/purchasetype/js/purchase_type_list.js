function purchase_type_search_page(url){
	util.page(url);
}
$import('public/js/select2/select2.min.js',function(){
	util.setItem('orl','index.php?mod=purchase&con=PurchaseType&act=search');
	util.setItem('listDIV','purchase_type_search_list');
	util.setItem('formID','purchase_type_search_form');
    var if_is_enabled = '<%$view->get_is_enabled()%>';
	var PurchaseTypeObj = function(){
		var initElements = function(){
			$('#purchase_type_search_form select[name="is_enabled"]').select2({
				placeholder: "��ѡ��",
				allowClear: true
			
			});
			$('#purchase_type_search_form :reset').on('click',function(){
				$('#purchase_type_search_form select[name="is_enabled"]').select2("val",if_is_enabled);
			})
		};
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			purchase_type_search_page(util.getItem('orl'));
			
		};
		
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	PurchaseTypeObj.init();
});
