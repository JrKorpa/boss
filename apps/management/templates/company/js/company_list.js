function company_search_page (url)
{
	util.page(url);
}

$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=management&con=company&act=search');
	util.setItem('formID','company_search_form');
	util.setItem('listDIV','company_search_list');
	
	var obj = function(){
		var initElements=function(){
			$('#company_search_form select').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
			});
			
		};
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			util.closeForm(util.getItem("formID"));
			company_search_page(util.getItem("orl"));
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