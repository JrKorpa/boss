var role_menu_list_role_id_20141224 = '<%$role_id%>';
var RolePermissionMenuListObj = function(){
	var handleTreeList = function(role_id){
		var setting = {
			view: {
				dblClickExpand: false,
				selectedMulti: false
			},
			check: {
				enable: true
			},
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				onClick:onClick,
				beforeCheck: beforeCheck
			}
		};

		function onClick(e,treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("role_permission_menu_tree");
			zTree.expandNode(treeNode);
		}

		function beforeCheck(treeId, treeNode) {
			return (treeNode.doCheck !== false);
		}

		var zNodes =[];

		$.ajax({
			   async:true,
			   type: "POST",
			   url: "index.php?mod=management&con=RolePermission&act=menuList&is_menu=1&role_id="+role_id,
			   dataType:"json",
			   success: function(data){
				   var i1=i2=true;
					$.each(data,function(i,item){
						if (item.code)
						{
							var json = {id:item.i,name:item.label+'('+item.code+')',pId:item.parent_id,tid:item.id,nocheck:false}
							if (item.chk)
							{
								json.checked = parseInt(item.chk) ? true : false;
							}
						}
						else
						{
							var json = {id:item.i,name:item.label,pId:item.parent_id,tid:item.id,doCheck:false,nocheck:true}
							if (i1)
							{
								json.open=true;
								i1=false;
							}
							else if(i2)
							{
								json.open=true;
								i2=false;
							}
						}
						zNodes.push(json);
					});
					$("#role_permission_menu_tree").addClass('ztree');
					$.fn.zTree.init($("#role_permission_menu_tree"), setting, zNodes);
				}
		});
	
	}

	return {
		init:function(){
			handleTreeList(role_menu_list_role_id_20141224);
		}
	}
}();

function role_permission_menu_expandall(o){
	var zTree = $.fn.zTree.getZTreeObj('role_permission_menu_tree');
	zTree.expandAll(true);
}

function role_permission_menu_collapseall(o){
	var zTree = $.fn.zTree.getZTreeObj('role_permission_menu_tree');
	zTree.expandAll(false);
}

function role_permission_menu_reload(o){
	$("#role_permission_search_list ul li a:eq(0)").click();
}

function role_permission_menu_selectall(o){
	var zTree = $.fn.zTree.getZTreeObj('role_permission_menu_tree');
	zTree.checkAllNodes(true);
	zTree.expandAll(true);
}

function role_permission_menu_selectnone(o){
	var zTree = $.fn.zTree.getZTreeObj('role_permission_menu_tree');
	zTree.checkAllNodes(false);
	zTree.expandAll(true);
}

function role_permission_menu_save(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	//role.id
	var nodes = $.fn.zTree.getZTreeObj('role_permission_menu_tree').getCheckedNodes();
	var ids = [];
	for (var x in nodes)
	{
		ids.push(nodes[x].tid);
	}
	$.post('index.php?mod=management&con=RolePermission&act=saveMenu',{role_id:role.id,pids:ids},function(data){
		if(data.success==1)
		{
			util.xalert('授权成功');
		}
		else
		{
			util.xalert('授权失败');
		}
	});


}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/jquery-ztree/js/jquery.ztree.excheck-3.5.js"],function(){
	RolePermissionMenuListObj.init();
});