//导出csv
function avg_deliver_export_cxv_index(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['start_time']=$('#avg_deliver_time_search_form #start_time').val();
	param['end_time']=$('#avg_deliver_time_search_form #end_time').val();
	param['time_type']=$('#avg_deliver_time_search_form #time_type').val();
	param['buchan_type']=$('#avg_deliver_time_search_form #buchan_type').val();
	param['order_type']=$('#avg_deliver_time_search_form #order_type').val();
	param['order_department']=$('#avg_deliver_time_search_form #order_department').val();
    param['buchan_status']=$("#avg_deliver_time_search_form select[name='buchan_status[]']").val();
    param['dia_type']=$('#avg_deliver_time_search_form #dia_type').val();
    param['qiban_type']=$('#avg_deliver_time_search_form #qiban_type').val();
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
function avg_deliver_time_export_cxv_second(){
	var url="index.php?mod=report&con=AvgDeliverTimeReport&act=avgdownloadDetail";
	var start_time=$('#avg_deliver_time_search_form #start_time').val();
	var end_time=$('#avg_deliver_time_search_form #end_time').val();
	var time_type=$('#avg_deliver_time_search_form #time_type').val();
	var buchan_type=$('#avg_deliver_time_search_form #buchan_type').val();
	var department_id=$('#avg_deliver_time_search_form #order_department').val();
	var channel_class=$('#avg_deliver_time_search_form #order_type').val();
    var buchan_status=$("#avg_deliver_time_search_form select[name='buchan_status[]']").val();
    var dia_type=$('#avg_deliver_time_search_form #dia_type').val();
    var qiban_type=$('#avg_deliver_time_search_form #qiban_type').val();
	if(start_time=='' || end_time==''){
		util.xalert("日期不能为空！");
		return ;
	}
	url+='&start_time='+start_time+'&end_time='+end_time+'&time_type='+time_type+'&buchan_type='+buchan_type+'&department_id='+department_id+'&channel_class='+channel_class+'&buchan_status='+buchan_status+'&dia_type='+dia_type+'&qiban_type='+qiban_type;
	window.open(url);
	return false;
}
//分页
function avg_deliver_time_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=AvgDeliverTimeReport&act=search');//设定刷新的初始url
	util.setItem('formID','avg_deliver_time_search_form');//设定搜索表单id
	util.setItem('listDIV','avg_deliver_time_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#avg_deliver_time_search_form select').select2({
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


			$('#avg_deliver_time_search_form :reset').on('click',function(){
				$('#avg_deliver_time_search_form select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			avg_deliver_time_search_page(util.getItem("orl"));
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