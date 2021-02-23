//分页
function app_member_address_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=bespoke&con=AppMemberAddress&act=search');
	util.setItem('formID','app_member_address_search_form');//设定搜索表单id
	util.setItem('listDIV','app_member_address_search_list');//设定列表数据容器id

	var AppMemberAddressObj = function(){
		var initElements = function(){
        };
		var handleForm = function(){
			util.search();
        };
		var initData = function(){
			util.closeForm(util.getItem("formID"));
            $('#app_member_address_search_form :reset').on('click',function(){
                $('#app_member_address_search_form input[name="member_id"]').val("");
            });
			app_member_address_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	AppMemberAddressObj.init();
});