var group_userListObj = function(){
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
			url: "index.php?mod=management&con=CompanyDepartment&act=departmentList",
			dataType:"json",
			success: function(data){
				$.each(data,function(i,item){
					zNodes.push({id:item.id,name:item.name,pId:item.parent_id,tid:item.id,open:true});
				});
				$(function(){
					$.fn.zTree.init($("#company_department_department_list"), setting, zNodes);
				});
			}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
			$('#group_user_search_form input[name="group_id"]').val(treeNode.id);
			//alert(treeNode.id);
			util.setItem('orl','index.php?mod=management&con=CompanyDepartment&act=search&department_id='+treeNode.id);
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
		util.setItem('orl','index.php?mod=management&con=CompanyDepartment&act=search&department_id=0');
		company_department_search_page(util.getItem('orl'));//显示全部
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

function department_compay_add(o){
	var obj = $.fn.zTree.getZTreeObj('company_department_department_list').getSelectedNodes()[0];
	//alert(obj.id);
	if (!obj)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	util._pop($(o).attr('data-url'),{department_id:obj.id});
/*alert(obj.id);*/
}

function company_department_search_page(url){
	util.page(url);
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/select2/select2.min.js"],function(){
	util.setItem('listDIV','company_department_search_list');

	group_userListObj.init();
});