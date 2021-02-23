var user_permission_rel_button_list_user_id = '<%$user_id%>';
var UserPermissionRelButtonListObj = function(){
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
                                $('#user_permission_rel_button').html('');
                                util.xalert('很抱歉，请选择用户！');
                                return false;
                        }
//			if (treeNode.pId)
//			{
App.blockUI({target: $('#user_permission_rel_button'), iconOnly: true});
				$.post('index.php?mod=management&con=UserPermission&act=relButtonList',{permission_id:treeNode.id,user_id:user_permission_rel_button_list_user_id,rid:treeNode.rid},function(data){
					$('#user_permission_rel_button_info').html(data);
                                        App.unblockUI($('#user_permission_rel_button'));
				});
//			}
//			else
//			{
//				$('#user_permission_rel_button_info').html('没有按钮');
//			}
		}

		var zNodes =[];
App.blockUI({target: $('#user_permission_rel_button'), iconOnly: true});
		$.ajax({
			   async:true,
			   type: "POST",
			   url: "index.php?mod=management&con=UserPermission&act=menuDetail&user_id="+user_id,
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
						$("#user_permission_rel_button_tree").addClass('ztree');
						$.fn.zTree.init($("#user_permission_rel_button_tree"), setting, zNodes);
                                                App.unblockUI($('#user_permission_rel_button'));
					}
					else
					{
						$("#user_permission_rel_button_tree").html('没有明细对象');
                                                App.unblockUI($('#user_permission_rel_button'));
					}
				}
		});
	
	}

	return {
		init:function(){
			handleTreeList(user_permission_rel_button_list_user_id);
		}
	}
}();


$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){
	UserPermissionRelButtonListObj.init();
});