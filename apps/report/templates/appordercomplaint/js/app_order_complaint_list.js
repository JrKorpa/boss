//分页
function app_order_complaint_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"], function(){
	util.setItem('orl','index.php?mod=report&con=AppOrderComplaint&act=search');//设定刷新的初始url
	util.setItem('formID','app_order_complaint_search_form');//设定搜索表单id
	util.setItem('listDIV','app_order_complaint_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            
            $('#app_order_complaint_search_form select[name="cl_feedback_id"]').select2({
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

            // 点击图片弹出大图
            $(".fancyboximg").fancybox({
                wrapCSS    : 'fancybox-custom',
                closeClick : true,
                openEffect : 'none',
                helpers : {
                    title : {
                        type : 'inside'
                    },
                    overlay : {
                        css : {
                            'background' : 'rgba(0,0,0,0.6)'
                        }
                    }
                }
            });
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_order_complaint_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

function downloads(){
    var down_infos = 'downs';
    var order_sn = $("#app_order_complaint_search_form [name='order_sn']").val();
    var cl_user = $("#app_order_complaint_search_form [name='cl_user']").val();
    var cl_feedback_id = $("#app_order_complaint_search_form [name='cl_feedback_id']").val();
    var cl_time_start = $("#app_order_complaint_search_form [name='cl_time_start']").val();
    var cl_time_end = $("#app_order_complaint_search_form [name='cl_time_end']").val();
    var cl_other = $("#app_order_complaint_search_form [name='cl_other']").val();
    var param = "&down_infos="+down_infos+"&order_sn="+order_sn+"&cl_user="+cl_user+"&cl_feedback_id="+cl_feedback_id+"&cl_time_start="+cl_time_start+"&cl_time_end="+cl_time_end+"&cl_other="+cl_other;
    url = "index.php?mod=report&con=AppOrderComplaint&act=search"+param;
    window.open(url);
}