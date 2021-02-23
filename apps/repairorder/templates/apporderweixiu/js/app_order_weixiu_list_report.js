//分页
function app_order_weixiu_search_page_report(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	
	var order_time_s=order_time_e=receiving_time_s=receiving_time_e='';
	var add_url='index.php?mod=repairorder&con=AppOrderWeixiu&act=search_report';
	add_url+="&time_type="+time_type+"&status="+status+"&repair_factory="+repair_factory+'&repair_act='+repair_act+'&re_type='+re_type+'&frequency='+frequency;
	if(time_type=='add'){
		 order_time_s=start_time;
		 order_time_e=end_time;
		 add_url+="&order_time_s="+order_time_s+"&order_time_e="+order_time_e;
	}
	else{
		receiving_time_s=start_time;
		receiving_time_e=end_time;
		add_url+="&receiving_time_s="+receiving_time_s+"&receiving_time_e="+receiving_time_e;
	}
	
	util.setItem('orl',add_url);//设定刷新的初始url
	util.setItem('formID','app_order_weixiu_search_form_report');//设定搜索表单id
	util.setItem('listDIV','app_order_weixiu_search_list_report');//设定列表数据容器id

	//匿名函数+闭包


	var AppOrderWeixiuListObj = function(){
		
		var initElements = function(){
			//初始化下拉组件
			$('#app_order_weixiu_search_form_report select').select2({
					placeholder: "请选择",
					allowClear: true
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
			$('#app_order_weixiu_search_form_report :reset').on('click',function(){
				$('#app_order_weixiu_search_form_report select').select2("val","");
			})

		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			//util.closeForm(util.getItem("formID"));
			//util.closeForm(util.getItem("listDIV"));
			app_order_weixiu_search_page_report(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	AppOrderWeixiuListObj.init();
});