function group_search_page(url){
	util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=Group&act=search');//设定刷新的初始url
	util.setItem('listDIV','group_search_list');//设定列表数据容器id
	var obj = function(){
		var initElements=function(){};
		var handleForm=function(){};
		var initData=function(){
			group_search_page(util.getItem('orl'));
		};
	
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	obj.init();
});