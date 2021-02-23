$import(function(){
	util.setItem('orl','index.php?mod=management&con=LargeAreaRecycle&act=search');
	util.setItem('listDIV','large_area_recycle_search_list');
	
	var large_area_recycle_obj = function(){
		var initElements=function(){};
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
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
	large_area_recycle_obj.init();
});