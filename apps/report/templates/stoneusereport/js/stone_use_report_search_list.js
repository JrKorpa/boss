//分页
function stone_use_report_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
																																														    var formID = 'stone_use_report_search_form';
	util.setItem('orl','index.php?mod=report&con=StoneUseReport&act=search');//设定刷新的初始url
	util.setItem('formID','stone_use_report_search_form');//设定搜索表单id
	util.setItem('listDIV','stone_use_report_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#'+formID+' select').select2({
                placeholder: "全部",
                allowClear: true
            });
			$('#'+formID+' button[type="reset"]').on('click',function(){
               $('#stone_use_report_search_form select').select2('val','').change();
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
	    };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			$('#'+formID).submit();
			//stone_use_report_search_page(util.getItem("orl"));
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