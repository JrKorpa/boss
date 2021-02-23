function user_channel_permission_menu_expandall(o){
	var zTree = $.fn.zTree.getZTreeObj('user_channel_permission_menu_tree');
	zTree.expandAll(true);
}

function user_channel_permission_menu_collapseall(o){
	var zTree = $.fn.zTree.getZTreeObj('user_channel_permission_menu_tree');
	zTree.expandAll(false);
}

function user_channel_permission_menu_reload(o){
	$("#user_channel_permission_search_list ul li:eq(0) a").click();
}

function user_channel_permission_menu_selectall(o){
	var zTree = $.fn.zTree.getZTreeObj('user_channel_permission_menu_tree');
	zTree.checkAllNodes(true);
	zTree.expandAll(true);
}

function user_channel_permission_menu_selectnone(o){
	var zTree = $.fn.zTree.getZTreeObj('user_channel_permission_menu_tree');
	zTree.checkAllNodes(false);
	zTree.expandAll(true);
}

function user_channel_permission_menu_save(o){
	//role.id
	var nodes = $.fn.zTree.getZTreeObj('user_channel_permission_menu_tree').getCheckedNodes();
	var ids = [];
	for (var x in nodes)
	{
		ids.push(nodes[x].tid);
	}
	$.post('index.php?mod=management&con=UserChannel&act=saveMenu',{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id'),pids:ids},function(data){
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
	var user_id = util.getItem('user_id');
	var channel_id = util.getItem('channel_id');
	var obj = function(){
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
				var zTree = $.fn.zTree.getZTreeObj("user_channel_permission_menu_tree");
				zTree.expandNode(treeNode);
			}

			function beforeCheck(treeId, treeNode) {
				return (treeNode.doCheck !== false);
			}

			var zNodes =[];

			$.ajax({
				   async:true,
				   type: "POST",
				   url: "index.php?mod=management&con=UserChannel&act=menuList&user_id="+user_id+"&channel_id="+channel_id,
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
						if (zNodes.length)
						{
							$("#user_channel_permission_menu_tree").addClass('ztree');
							$.fn.zTree.init($("#user_channel_permission_menu_tree"), setting, zNodes);
						}
						else
						{
							$("#user_channel_permission_menu_tree").html('没有菜单');
						}
					}
			});
		
		}

		return {
			init:function(){
				handleTreeList(user_id,channel_id);
			}
		}
	}();
	
	
	obj.init();
});