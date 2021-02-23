var RolePermissionListObj = function(){
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
			   url: "index.php?mod=management&con=RolePermission&act=roleList",
			   dataType:"json",
			   success: function(data){
					$.each(data,function(i,item){
						zNodes.push({id:item.id,name:item.label});
					});
					$(function(){
						$.fn.zTree.init($("#role_permission_role_list"), setting, zNodes);
					});
				}
		});

		function onClick(event, treeId, treeNode, clickFlag) {
			$('#role_permission_menu_list').html('');
			$("#role_permission_search_list ul li:eq(0)").addClass('active').siblings().removeClass('active');
			$.post($("#role_permission_search_list ul li a:eq(0)").attr('data-url'),{role_id:treeNode.id},function(data){
				$('#role_permission_menu_list').html(data);
				$('#role_permission_menu_list').addClass('active');
			});
		}
	}

	var initElements = function(){}
	var handleForm = function(){}
	var initData = function(){}

	return {
		init:function(){
			handleTreeList();
			initElements();
			handleForm();
			initData();
		},
		menuList:function(id){
			handleMenuList(id);
		}
	}
}();

//菜单授权页签
function role_permission_menu_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_menu_list').html(data);
	})
}

//列表按钮页签
function role_permission_button_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_button_list').html(data);
	})
}

//操作授权页签
function role_permission_opr_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_opr_list').html(data);
	})
}

//查看页按钮页签
function role_permission_button_view_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_view_button_list').html(data);
	})
}

//明细对象页签
function role_permission_rel_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_rel_list').html(data);
	})
}

//明细按钮
function role_permission_rel_button_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_rel_button').html(data);
	})
}

//明细操作
function role_permission_rel_opr_add(o){
	var role = $.fn.zTree.getZTreeObj('role_permission_role_list').getSelectedNodes()[0];
	if (!role)
	{
		util.xalert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	$.post($(o).attr('data-url'),{role_id:role.id},function(data){
		$('#role_permission_rel_opr').html(data);
	})
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js","public/js/jquery-ztree/js/jquery.ztree.excheck-3.5.js"],function(){
	util.setItem('listDIV','role_permission_search_list');
	RolePermissionListObj.init();
});