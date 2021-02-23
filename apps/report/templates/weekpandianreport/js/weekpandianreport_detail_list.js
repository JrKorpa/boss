//分页
function weekpandianreport_detail_search_page(url){
	util.page(url);
}
var id="<%$smarty.get.id%>";
//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=report&con=WeekPandianReport&act=detail_list_ajax&id='+id);//设定刷新的初始url
	util.setItem('formID','weekpandianreport_detail_search_form');//设定搜索表单id
	util.setItem('listDIV','weekpandianreport_detail_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
			
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			weekpandianreport_detail_search_page(util.getItem("orl"));
			
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