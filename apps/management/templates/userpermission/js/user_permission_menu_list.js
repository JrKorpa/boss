var user_menu_list_user_id = '<%$user_id%>';
var UserPermissionMenuListObj =function(){
	var handleTreeList = function(user_id){
		var setting = {
			view: {
				fontCss: getFont,
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

		function getFont(treeId, node) {
			return node.font ? node.font : {};
		}

		function onClick(e,treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("user_menu_permission_tree");
			zTree.expandNode(treeNode);
		}

		function beforeCheck(treeId, treeNode) {
			return (treeNode.doCheck !== false);
		}

		var zNodes =[];
		$("#user_menu_permission_tree").html('');
                App.blockUI({target:$('#user_permission_menu_list'), iconOnly: true});
		$.ajax({
			   async:true,
			   type: "POST",
			   url: "index.php?mod=management&con=UserPermission&act=menuList&is_menu=1&user_id="+user_id,
			   dataType:"json",
			   success: function(data){
				   var i1=i2=true;
					$.each(data,function(i,item){
						if (item.code)
						{
							if (!!parseInt(item.chk_r))
							{
								var json = {id:item.i,name:item.label+'('+item.code+')',pId:item.parent_id,tid:item.id,nocheck:false,font:{'color':'red'}}
							}
							else
							{
								var json = {id:item.i,name:item.label+'('+item.code+')',pId:item.parent_id,tid:item.id,nocheck:false}
							}
							if (item.chk_u)
							{
								json.checked = parseInt(item.chk_u) ? true : false;
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
					$("#user_menu_permission_tree").addClass('ztree');
					$.fn.zTree.init($("#user_menu_permission_tree"), setting, zNodes);
                                        App.unblockUI($("#user_permission_menu_list"));
				}
		});
	
	}

	return {
		init:function(){
			handleTreeList(user_menu_list_user_id);
		}
	}

}();

function user_permission_menu_expandall(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
	var zTree = $.fn.zTree.getZTreeObj('user_menu_permission_tree');
	zTree.expandAll(true);
}

function user_permission_menu_collapseall(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        var zTree = $.fn.zTree.getZTreeObj('user_menu_permission_tree');
	zTree.expandAll(false);
}

function user_permission_menu_reload(o){
	$("#user_permission_search_list ul li a:eq(0)").click();
}

function user_permission_menu_selectall(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        var zTree = $.fn.zTree.getZTreeObj('user_menu_permission_tree');
	zTree.checkAllNodes(true);
	zTree.expandAll(true);
}

function user_permission_menu_selectnone(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        var zTree = $.fn.zTree.getZTreeObj('user_menu_permission_tree');
	zTree.checkAllNodes(false);
	zTree.expandAll(true);
}

function user_permission_menu_sync(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        var zTree = $.fn.zTree.getZTreeObj('user_menu_permission_tree');
	var nodeList = zTree.getCheckedNodes(false);
	for (var i in nodeList)
	{
		if (nodeList[i].font)
		{
			nodeList[i].checked = true;
			zTree.updateNode(nodeList[i]);
		}
	}
	zTree.expandAll(true);
}

function user_permission_menu_save(o){
    	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
 		util.xalert('很抱歉，请选择用户！');
		return false;
	}

	//role.id
	var nodes = $.fn.zTree.getZTreeObj('user_menu_permission_tree').getCheckedNodes();
	var ids = [];
	for (var x in nodes)
	{
		ids.push(nodes[x].tid);
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true}); 
	$.post('index.php?mod=management&con=UserPermission&act=saveMenu',{user_id:user_id,pids:ids},function(data){
		if(data.success==1)
		{
			util.xalert('授权成功',function(){   
                                App.unblockUI($('#user_permission_search_list'));
                        });
		}
		else
		{
			util.xalert('授权失败',function(){
                                App.unblockUI($('#user_permission_search_list'));
                        });
		}
	});
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/jquery-ztree/js/jquery.ztree.excheck-3.5.js"],function(){
	UserPermissionMenuListObj.init();
});