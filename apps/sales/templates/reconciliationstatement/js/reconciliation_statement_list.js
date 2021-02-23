//分页
function reconciliation_statement_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=sales&con=ReconciliationStatement&act=search');//设定刷新的初始url
	util.setItem('formID','reconciliation_statement_search_form');//设定搜索表单id
	util.setItem('listDIV','reconciliation_statement_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){

			//初始化下拉组件
			$('#reconciliation_statement_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件		
		};
		
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			reconciliation_statement_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	ListObj.init();
});
function ReconciliationStatementDownload(obj)
{
	$.get('index.php?mod=sales&con=ReconciliationStatement&act=checkFile',function(data){
		if(data.success==1)
		{
			location.href=$(obj).attr('data-url');
		}
		else
		{
			util.xalert(data.error);
		}
	});
	
}