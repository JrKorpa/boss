$import(function(){
	util.setItem('orl','index.php?mod=management&con=LargeArea&act=search');
	util.setItem('listDIV','large_area_search_list');
	
	var obj = function(){
		var initElements=function(){};
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			util.closeForm(util.getItem("formID"));
			util.page(util.getItem("orl"));
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