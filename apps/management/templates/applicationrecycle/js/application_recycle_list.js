//分页
function application_recycle_search_page(url){
	util.page(url);
}

//匿名回调
$import(function(){
	util.setItem('orl','index.php?mod=management&con=ApplicationRecycle&act=search');//设定刷新的初始url
	util.setItem('listDIV','application_recycle_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var ApplicationListObj = function(){

		var initElements = function(){};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			application_recycle_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}
	}();

	ApplicationListObj.init();
});