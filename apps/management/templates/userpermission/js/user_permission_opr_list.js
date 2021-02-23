var user_opr_list_user_id = '<%$user_id%>';
var UserPermissionOprListObj = function(){
	var handleTreeList = function(user_id){
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
                        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
                        if (!parseInt(user_id))
                        {
                                $('.modal-scrollable').trigger('click');
                                $('#user_permission_opr_list').html('');
                                util.xalert('很抱歉，请选择用户！');
                                return false;
                        }
			if (treeNode.tid)
			{
                                App.blockUI({target: $('#user_permission_opr_list'), iconOnly: true});
				$.post('index.php?mod=management&con=UserPermission&act=oprList',{permission_id:treeNode.tid,user_id:user_id,rid:treeNode.rid},function(data){
					$('#user_permission_opr_info').html(data);
                                        App.unblockUI($('#user_permission_opr_list'));
				});
			}
			else
			{
				$('#user_permission_opr_info').html('没有操作');
			}
		}

		var zNodes =[];
                App.blockUI({target: $('#user_permission_opr_list'), iconOnly: true});
		$.ajax({
			   async:true,
			   type: "POST",
			   url: "index.php?mod=management&con=UserPermission&act=menuList&user_id="+user_id,
			   dataType:"json",
			   success: function(data){
				   var i1=i2=true;
					$.each(data,function(i,item){
						if (item.code)
						{
							if (!!parseInt(item.chk_u))
							{
								var json = {id:item.i,name:item.label,pId:item.parent_id,tid:item.id,rid:item.resource_id}
							}
							else
							{
								return true;
							}
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
						$("#user_opr_permission_tree").addClass('ztree');
						$.fn.zTree.init($("#user_opr_permission_tree"), setting, zNodes);
                                                App.unblockUI($('#user_permission_opr_list'));
					}
					else
					{
						$("#user_opr_permission_tree").html('没有菜单');
                                                App.unblockUI($('#user_permission_opr_list'));
					}
				}
		});
	
	}

	return {
		init:function(){
			handleTreeList(user_opr_list_user_id);
		}
	}
}();


$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	UserPermissionOprListObj.init();
});