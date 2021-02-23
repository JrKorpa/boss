function system_access_log_search_page(url){
	util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=management&con=SystemAccessLog&act=search');
	util.setItem('formID','system_access_log_search_form');
	util.setItem('listDIV','system_access_log_search_list');

	var SystemAccessLogObj = function(){
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
		}
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			system_access_log_search_page(util.getItem('orl'));
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