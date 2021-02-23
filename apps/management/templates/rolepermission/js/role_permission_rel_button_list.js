var role_permission_rel_button_list_role_id = '<%$role_id%>';
var RolePermissionRelButtonListObj = function(){
	var handleTreeList = function(role_id){
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

		function onClick(e,treeId, treeNode) {
			if (treeNode.pId)
			{
				$.post('index.php?mod=management&con=RolePermission&act=relButtonList',{permission_id:treeNode.id,role_id:role_permission_rel_button_list_role_id,rid:treeNode.rid},function(data){
					$('#role_permission_rel_button_info').html(data);
				});
			}
			else
			{
				$('#role_permission_rel_button_info').html('没有按钮');
			}
		}

		var zNodes =[];

		$.ajax({
			   async:true,
			   type: "POST",
			   url: "index.php?mod=management&con=RolePermission&act=menuDetail&role_id="+role_id,
			   dataType:"json",
			   success: function(data){
				   var i1=i2=true;
					$.each(data,function(i,item){
						var json = {id:item.id,name:item.label,pId:item.parent_id,rid:item.resource_id}
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
						zNodes.push(json);
					});
					if (zNodes.length)
					{
						$("#role_permission_rel_button_tree").addClass('ztree');
						$.fn.zTree.init($("#role_permission_rel_button_tree"), setting, zNodes);
					}
					else
					{
						$("#role_permission_rel_button_tree").html('没有明细对象');
					}
				}
		});
	
	}

	return {
		init:function(){
			handleTreeList(role_permission_rel_button_list_role_id);
		}
	}
}();


$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	RolePermissionRelButtonListObj.init();
});