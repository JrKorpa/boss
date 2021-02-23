function shopcount_search_page (url)
{
	util.page(url);
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=ShopXsgwcountReport&act=search');
	util.setItem('formID','shopcount_search_xsgw_form');
	util.setItem('listDIV','shopcount_search_xsgw_list');
	
	var ShopCfgObj = function(){
		var initElements=function(){
            $('#shopcount_search_xsgw_form select[name="shopname"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			$('#shopcount_search_xsgw_form select[name="create_user"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
			
			$('#shopcount_search_xsgw_form select[name="orderenter"]').select2({
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
					clearBtn: true
				});
				$('body').removeClass("modal-open");
			}
			
			$('#shopcount_search_xsgw_form :reset').on('click',function(){
				$('#shopcount_search_xsgw_form select').select2("val","");
			})
        };
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			util.closeForm(util.getItem("formID"));
			shopcount_search_page(util.getItem("orl"));
		};
	
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	ShopCfgObj.init();
});