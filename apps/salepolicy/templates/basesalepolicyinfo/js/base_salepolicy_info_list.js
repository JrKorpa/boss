function base_salepolicy_info_search_page(url){
	util.page(url);
}

$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/jquery.validate.extends.js"],function(){
	util.setItem('orl','index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=search');
	util.setItem('formID','base_salepolicy_info_search_form');
	util.setItem('listDIV','base_salepolicy_info_search_list');
	var ControlObj = function(){
                
        var initElements = function(){
            $('#base_salepolicy_info_search_form select[name="policy_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
            $('#base_salepolicy_info_search_form select[name="is_delete"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			$('#base_salepolicy_info_search_form select[name="chanpinxian"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#base_salepolicy_info_search_form select[name="jintuo_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#base_salepolicy_info_search_form select[name="huopin_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#base_salepolicy_info_search_form select[name="cat_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			

			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
            //重置
            $('#base_salepolicy_info_search_form :reset').click(function(){
                $('#base_salepolicy_info_search_form select[name="policy_status"]').select2('val','');
                $('#base_salepolicy_info_search_form select[name="is_delete"]').select2('val','');
            });
        };
                
		var handleForm = function(){
			util.search();
		}

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			base_salepolicy_info_search_page(util.getItem("orl"));
		}

		return {
			init:function(){
                initElements();
				handleForm();
				initData();
			}
		}
	}();

	ControlObj.init();
});