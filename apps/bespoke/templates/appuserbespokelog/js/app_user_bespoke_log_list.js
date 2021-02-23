//分页
var id = <%$id%>;
function app_user_bespoke_log_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=bespoke&con=AppUserBespokeLog&act=search&id='+id);
	util.setItem('formID','app_user_bespoke_log_search_form');//设定搜索表单id
	util.setItem('listDIV','app_user_bespoke_log_search_list');//设定列表数据容器id

	var baseMemberInfoObj = function(){
		var initElements = function(){
			$('#app_user_bespoke_log_search_form select[name="member_name"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
        };
		var handleForm = function(){
			util.search();
        };
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_user_bespoke_log_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	baseMemberInfoObj.init();
});