function defective_product_search_page(url){
	util.page(url);
}

$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=purchase&con=DefectiveProduct&act=search');
	util.setItem('listDIV','defective_product_search_list');
	util.setItem('formID','defective_product_search_form');

	var PurchaseInfoObj = function(){
		var initElements = function(){
			$('#defective_product_search_form select[name="status"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			
			$('#defective_product_search_form select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});

		};
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			defective_product_search_page(util.getItem('orl'));
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

$("#dowload").on('click',function(){
	$("#is_dowload").attr('value',1);
	var form = $("#defective_product_search_form").serializeArray();
	var url ="index.php?mod=purchase&con=DefectiveProduct&act=search";
	$.each(form, function(){
		if(this.value!=''){
			url += "&"+this.name+"="+this.value;
		}
	});
	location.href=url;
});