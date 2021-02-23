function department_search_page(url){
	util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=Department&act=search');
	util.setItem('listDIV','department_search_list');
	var obj = function(){
		var initElements=function(){};
		var handleForm=function(){};
		var initData=function(){
			department_search_page(util.getItem('orl'));
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