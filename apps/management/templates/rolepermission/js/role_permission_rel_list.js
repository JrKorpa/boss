var role_permission_rel_list_role_id = '<%$role_id%>';
var RolePermissionRelListObj = function(){
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
			if (treeNode.tid)
			{
				$.post('index.php?mod=management&con=RolePermission&act=relList',{permission_id:treeNode.tid,role_id:role_id,rid:treeNode.rid},function(data){
					$('#role_permission_rel_info').html(data);
				});
			}
			else
			{
				$('#role_permission_rel_info').html('没有操作');
			}
		}

		var zNodes =[];

		$.ajax({
			async:true,
			type: "POST",
			url: "index.php?mod=management&con=RolePermission&act=menuListDetail&role_id="+role_id,
			dataType:"json",
			success: function(data){

				var i1=i2=true;

				$.each(data,function(i,item){
					if (item.code)
					{
						var json = {id:item.i,name:item.label,pId:item.parent_id,tid:item.id,rid:item.resource_id}
					}
					else
					{
						var json = {id:item.i,name:item.label,pId:item.parent_id,doCheck:false,nocheck:true}
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
					$("#rel_permission_rel_tree").addClass('ztree');
					$.fn.zTree.init($("#rel_permission_rel_tree"), setting, zNodes);
				}
				else
				{
					$("#rel_permission_rel_tree").html('没有明细！');
				}

			}
		});
	
	}

	return {
		init:function(){
			handleTreeList(role_permission_rel_list_role_id);
		}
	}
}();


$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	RolePermissionRelListObj.init();
});