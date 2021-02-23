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
			   url: "index.php?mod=management&con=GroupUser&act=groupList",
			   dataType:"json",
			   success: function(data){
					$.each(data,function(i,item){
						zNodes.push({id:item.id,name:item.name,pId:item.parent_id,tid:item.id,open:true});
					});
					$(function(){
						$.fn.zTree.init($("#group_user_group_list"), setting, zNodes);
					});
				}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
			$('#group_user_search_form input[name="group_id"]').val(treeNode.id);
			util.setItem('orl','index.php?mod=management&con=GroupUser&act=search&group_id='+treeNode.id);
			util.page(util.getItem('orl'));

		}	
	}
	var initElements = function(){
	}
	var handleForm = function(){
		util.search();
	}
	var initData = function(){
		util.closeForm(util.getItem("formID"));
		util.setItem('orl','index.php?mod=management&con=GroupUser&act=search&group_id=0');
		//group_user_search_page(util.getItem('orl'));//显示全部
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

function group_list_sort(o){
	var obj = $.fn.zTree.getZTreeObj('group_user_group_list').getSelectedNodes()[0];
	//alert(obj.id);
	if (!obj)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	util._pop($(o).attr('data-url'),{group_id:obj.id});
}

function group_user_add(o){
	var obj = $.fn.zTree.getZTreeObj('group_user_group_list').getSelectedNodes()[0];
	//alert(obj.id);
	if (!obj)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	util._pop($(o).attr('data-url'),{group_id:obj.id});
}

function group_user_search_page(url){
	util.page(url);
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/select2/select2.min.js"],function(){
	util.setItem('listDIV','group_user_search_list');
	util.setItem('formID','group_user_search_form');

	group_userListObj.init();
});