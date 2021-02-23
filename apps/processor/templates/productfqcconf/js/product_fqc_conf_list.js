//分页
function product_fqc_conf_search_page(url){
	util.page(url);
}

//匿名回调
$import(function(){
	util.setItem('orl','index.php?mod=processor&con=ProductFqcConf&act=search');//设定刷新的初始url
	util.setItem('listDIV','product_fqc_conf_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){};
		
		var handleForm = function(){
		};
		
		var initData = function(){
			product_fqc_conf_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});