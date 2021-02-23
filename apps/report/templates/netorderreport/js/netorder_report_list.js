$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
    "public/js/select2/select2.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css"],function(){
	util.setItem('orl','index.php?mod=report&con=NetOrderReport&act=search');
	util.setItem('formID','netorder_report_form');
	util.setItem('listDIV','netorder_report_list');
	
	var ShopCfgObj = function(){
		var initElements=function(){
			$('#netorder_report_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

			$('#netorder_report_form select[name="shop_type"]').change(function(e){
                $(this).valid();
                $('#netorder_report_form select[name="shop_id"]').empty();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=common&act=getShops',{shop_type:t_v},function(data){
		                $('#netorder_report_form select[name="shop_id"]').empty();
                        $('#netorder_report_form select[name="shop_id"]').append(data);
                    });
                }
                else
                {
                    $('#netorder_report_form select[name="shop_id"]').select2('val','').attr('readOnly',false).change();
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
			
			$('#netorder_report_form :reset').on('click',function(){
				$('#netorder_report_form select').select2("val","");
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

//导出
function downloadNetOrder(){
    var args=$("#netorder_report_form").serialize();
    url= "index.php?mod=report&con=NetOrderReport&act=downloads&"+args;
    window.open(url);
}
