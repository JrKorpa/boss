function user_operation_log_search_page(url){
	util.page(url);
}
var formId='user_operation_log_search_form';
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=management&con=UserOperationLog&act=search');
	util.setItem('formID','user_operation_log_search_form');
	util.setItem('listDIV','user_operation_log_search_list');

	var SystemAccessLogObj = function(){
		var initElements = function(){
			$('#'+formId+' select').select2({
				placeholder: "请选择",
				allowClear: true,
			})
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		}
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			user_operation_log_search_page(util.getItem('orl'));
		}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	SystemAccessLogObj.init();
});