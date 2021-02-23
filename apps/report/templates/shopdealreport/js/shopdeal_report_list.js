function shopcount_search_page (url)
{
	util.page(url);
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
    "public/js/select2/select2.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css"],function(){
	util.setItem('orl','index.php?mod=report&con=ShopDealReport&act=search');
	util.setItem('formID','shopdeal_report_form');
	util.setItem('listDIV','shopdeal_report_list');
	
	var ShopCfgObj = function(){
		var initElements=function(){
			$('#shopdeal_report_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

			$('#shopdeal_report_form select[name="shop_type"]').change(function(e){
                $(this).valid();
                $('#shopdeal_report_form select[name="shop_id"]').empty();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=common&act=getShops',{shop_type:t_v},function(data){
		                $('#shopdeal_report_form select[name="shop_id"]').empty();
                        $('#shopdeal_report_form select[name="shop_id"]').append(data);
                    });
                }
                else
                {
                    $('#shopdeal_report_form select[name="shop_id"]').select2('val','').attr('readOnly',false).change();
                }
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
			
			$('#shopdeal_report_form :reset').on('click',function(){
				$('#shopdeal_report_form select').select2("val","");
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
				//initData();
			}
		}
	}();
	ShopCfgObj.init();
});