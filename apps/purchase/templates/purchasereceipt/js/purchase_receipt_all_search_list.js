function purchase_receipt_all_search_page(url){
	util.page(url);
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=purchase&con=PurchaseReceipt&act=search&is_all=1');
	util.setItem('listDIV','purchase_receipt_all_search_list');
	util.setItem('formID','purchase_receipt_all_search_form');
	var PurchaseInfoObj1 = function(){
		var initElements = function(){
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			$('#purchase_receipt_all_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#purchase_receipt_all_search_form :reset').on('click',function(){
				$('#purchase_receipt_all_search_form select[name="status"]').select2("val","");
				$('#purchase_receipt_all_search_form select[name="prc_id"]').select2("val","");
			});
			//$('#purchase_receipt_all_search_form input[name=is_all_new]').val(1);              
		};
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			purchase_receipt_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	PurchaseInfoObj1.init();
});

