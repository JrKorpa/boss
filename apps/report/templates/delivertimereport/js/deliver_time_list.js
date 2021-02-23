//导出csv
function deliver_time_export_csv(){
	//document.getElementById("export_content").innerHTML='正在导出中';
	var url="index.php?mod=report&con=DeliverTimeReport&act=export_csv";
	var start_time=$('#deliver_time_search_form #start_time').val();
	var end_time=$('#deliver_time_search_form #end_time').val();
	var time_type=$('#deliver_time_search_form #time_type').val();
	var buchan_type=$('#deliver_time_search_form #buchan_type').val();
	var order_department=$('#deliver_time_search_form #order_department').val();
	url+='&start_time='+start_time+'&end_time='+end_time+'&time_type='+time_type+'&buchan_type='+buchan_type+'&order_department='+order_department;
	window.open(url);
	return false;
}
function deliver_time_export_cxv_second(){
	var url="index.php?mod=report&con=DeliverTimeReport&act=downloadDetail";
	var start_time=$('#deliver_time_search_form #start_time').val();
	var end_time=$('#deliver_time_search_form #end_time').val();
	var time_type=$('#deliver_time_search_form #time_type').val();
	var buchan_type=$('#deliver_time_search_form #buchan_type').val();
	var department_id=$('#deliver_time_search_form #order_department').val();
	var channel_class=$('#deliver_time_search_form #order_type').val();
	if(start_time=='' || end_time==''){
		util.xalert("日期不能为空！");
		return ;
	}
	url+='&start_time='+start_time+'&end_time='+end_time+'&time_type='+time_type+'&buchan_type='+buchan_type+'&department_id='+department_id+'&channel_class='+channel_class;
	window.open(url);
	return false;
}
//分页
function deliver_time_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=DeliverTimeReport&act=search');//设定刷新的初始url
	util.setItem('formID','deliver_time_search_form');//设定搜索表单id
	util.setItem('listDIV','deliver_time_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#deliver_time_search_form select').select2({
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


			$('#deliver_time_search_form :reset').on('click',function(){
				$('#deliver_time_search_form select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			deliver_time_search_page(util.getItem("orl"));
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