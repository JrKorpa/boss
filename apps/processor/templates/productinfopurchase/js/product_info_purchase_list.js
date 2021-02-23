function product_info_purchase_search_page(url){
	util.page(url);
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=ProductInfoPurchase&act=search');
	util.setItem('listDIV','product_info_purchase_search_list');
	util.setItem('formID','product_info_purchase_search_form');

	var ProductInfoObj = function(){
		var initElements = function(){
			$('#product_info_purchase_search_form select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});
			//日期
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		};
		var handleForm = function(){
			util.search();
			util.closeForm(util.getItem("formID"));
		};
		var initData = function(){
			product_info_purchase_search_page(util.getItem('orl'));
			$('#product_info_purchase_search_form :reset').on('click',function(){
				$('#product_info_purchase_search_form select').select2('val','');
			});
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	ProductInfoObj.init();
});
