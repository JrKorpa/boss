//分页
function repair_time_search_page_second(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	
	var post_url='index.php?mod=report&con=RepairTimeReport&act=search_second';
	post_url+='&start_time='+start_time+'&end_time='+end_time+'&repair_factory='+repair_factory+'&frequency='+frequency+'&re_type='+re_type+'&repair_act='+repair_act+'&time_type='+time_type;
	util.setItem('orl',post_url);//设定刷新的初始url
	util.setItem('formID','repair_time_search_form_second');//设定搜索表单id
	util.setItem('listDIV','repair_time_search_list_second');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#repair_time_search_form_second select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
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


			$('#repair_time_search_form_second :reset').on('click',function(){
				$('#repair_time_search_form_second select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			repair_time_search_page_second(util.getItem("orl"));
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