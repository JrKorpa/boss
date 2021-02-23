//分页
function base_order_info_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/select2/select2.min.js",
    "public/js/fancyapps-fancyBox/jquery.fancybox.css"],function(){
	util.setItem('orl','index.php?mod=report&con=PerformanceReport&act=search');//设定刷新的初始url
	util.setItem('formID','performance_report_search_form');//设定搜索表单id
	util.setItem('listDIV','performance_report_search_list');//设定列表数据容器id
	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
            $('#performance_report_search_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

			$('#performance_report_search_form select[name="shop_type"]').change(function(e){
                $(this).valid();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=PerformanceReport&act=getShops',{shop_type:t_v},function(data){
		                $('#performance_report_search_form select[name="shop_id[]"]').empty();
                        $('#performance_report_search_form select[name="shop_id[]"]').append(data);
                    });
                } else {
                    $('#performance_report_search_form select[name="shop_id[]"]').select2('val','').attr('readOnly',false).change();
                }
            });

            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }

			$('#performance_report_search_form :reset').on('click',function(){
				$('#performance_report_search_form select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			base_order_info_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

//导出
function download(){
	var args=$("#performance_report_search_form").serialize();
    location.href = "index.php?mod=report&con=PerformanceReport&act=search&sel_excel=excel&"+args;

}

