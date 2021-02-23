var OrganizationListObj = function(){
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
			   url: "index.php?mod=management&con=Organization&act=deptList",
			   dataType:"json",
			   success: function(data){
					$.each(data,function(i,item){
						zNodes.push({id:item.id,name:item.name,pId:item.parent_id,tid:item.id,open:true});
					});
					$(function(){
						$.fn.zTree.init($("#organization_dept_list"), setting, zNodes);
					});
				}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
			$('#organization_search_form input[name="dept_id"]').val(treeNode.id);
			util.setItem('orl','index.php?mod=management&con=Organization&act=search&dept_id='+treeNode.id);
			organization_search_page(util.getItem('orl'));
		}	
	}
	var initElements = function(){
		$('#organization_search_form select').select2({
			placeholder: "请选择",
			allowClear: true
		});
		
		$('#organization_search_form :reset').on('click',function(){
			$('#organization_search_form select').select2("val","");
		})
	}
	var handleForm = function(){
		util.search();
	}
	var initData = function(){
		util.closeForm(util.getItem("formID"));
		util.setItem('orl','index.php?mod=management&con=Organization&act=search&dept_id=0');
		organization_search_page(util.getItem('orl'));//显示全部
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

function organization_add(o){
	var obj = $.fn.zTree.getZTreeObj('organization_dept_list').getSelectedNodes()[0];
	if (!obj)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	util._pop($(o).attr('data-url'),{dept_id:obj.id});
}

function organization_search_page(url){
	util.page(url);
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/select2/select2.min.js"],function(){
	util.setItem('listDIV','organization_search_list');
	util.setItem('formID','organization_search_form');

	OrganizationListObj.init();
});