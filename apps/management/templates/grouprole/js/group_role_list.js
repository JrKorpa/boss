var GroupRoleListObj = function(){
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
			   url: "index.php?mod=management&con=GroupRole&act=groupList",
			   dataType:"json",
			   success: function(data){
					$.each(data,function(i,item){
						zNodes.push({id:item.id,name:item.name,pId:item.parent_id,tid:item.id,open:true});
					});
					$(function(){
						$.fn.zTree.init($("#group_role_list"), setting, zNodes);
					});
				}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
                    	util.setItem('orl','index.php?mod=management&con=GroupRole&act=search&group_id='+treeNode.id);
			group_role_search_page(util.getItem('orl'));
		}	
	}
	var initElements = function(){}
	var handleForm = function(){
		util.search();
	}
	var initData = function(){
            	util.setItem('orl','index.php?mod=management&con=GroupRole&act=search&group_id=0');
		group_role_search_page(util.getItem('orl'));//显示全部
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

function group_role_add(o){
	var obj = $.fn.zTree.getZTreeObj('group_role_list').getSelectedNodes()[0];
	if (!obj)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	util._pop($(o).attr('data-url'),{group_id:obj.id});
}


function group_role_search_page(url){
	util.page(url);
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	util.setItem('listDIV','group_role_search_list');

	GroupRoleListObj.init();
});