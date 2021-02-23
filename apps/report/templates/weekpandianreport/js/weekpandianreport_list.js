//导出csv
function week_pan_export_cxv_index(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['time_start']=$("#weekpandianreport_search_form #time_start").val();
	param['time_end']=$("#weekpandianreport_search_form #time_end").val();
	param['type']=$("#weekpandianreport_search_form #type").val();
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
//分页
function warehouse_pandian_plan_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=report&con=WeekPandianReport&act=search');//设定刷新的初始url
	util.setItem('formID','weekpandianreport_search_form');//设定搜索表单id
	util.setItem('listDIV','weekpandianreport_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
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
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_pandian_plan_search_page(util.getItem("orl"));
			
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