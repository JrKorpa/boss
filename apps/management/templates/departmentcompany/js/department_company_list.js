var departmentCompanyListObj = function(){
	var handleTreeList = function(){
		var setting = {
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				onClick: onClick
			}
		};
		var zNodes =[];

		$.ajax({
			async:true,
			type: "POST",
			url: "index.php?mod=management&con=DepartmentCompany&act=companyList",
			dataType:"json",
			success: function(data){
				var zNodes = [];
				$.each(data,function(i,item){
					zNodes.push({id:item.id,name:item.name,pId:item.parent_id,tid:item.id,open:true});
				});
				$.fn.zTree.init($("#department_company_department_list"), setting, zNodes);
			}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
/*			$('#group_user_search_form input[name="group_id"]').val(treeNode.id);*/
			//alert(treeNode.id);
			util.setItem('orl','index.php?mod=management&con=DepartmentCompany&act=search&company_id='+treeNode.id);
			var url=util.getItem('orl');
			util.page(url);

		}
	}
	var initElements = function(){
/*		$('#group_user_search_form select[name="company_id"]').select2({
			placeholder: "请选择",
			allowClear: true
		});



		$('#group_user_search_form :reset').on('click',function(){
			$('#group_user_search_form select[name="user_id"]').select2("val","");

		})*/
	}
	var handleForm = function(){
		util.search();
	}
	var initData = function(){
		util.closeForm(util.getItem("formID"));
		util.setItem('orl','index.php?mod=management&con=DepartmentCompany&act=search&company_id=0');
		department_company_search_page(util.getItem('orl'));//显示全部
	}
	return {
		init:function(){
			handleTreeList();
			initElements();
			handleForm();
			initData();
		}
	}
}();

function company_department_add(o){
	var obj = $.fn.zTree.getZTreeObj('department_company_department_list').getSelectedNodes()[0];
	//alert(obj.id);
	if (!obj)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	util._pop($(o).attr('data-url'),{company_id:obj.id});

}

function department_company_search_page(url){
	util.page(url);
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/select2/select2.min.js"],function(){
	util.setItem('listDIV','department_company_search_list');

	departmentCompanyListObj.init();
});