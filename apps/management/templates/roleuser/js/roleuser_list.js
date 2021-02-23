var RoleuserObj = function(){

    var initElements = function(){};
    var handleForm = function(){
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
            url: "index.php?mod=management&con=RoleUser&act=roleList",
            dataType:"json",
            success: function(data){
                $.each(data,function(i,item){
                    zNodes.push({id:item.id,name:item.label});
                });
                $(function(){
                    $.fn.zTree.init($("#roleuser_role_list"), setting, zNodes);
                });
            }
        });

        function onClick(event, treeId, treeNode, clickFlag) {
            $('#roleuser_role_list input[name="id"]').val(treeNode.id);
            util.setItem('orl','index.php?mod=management&con=RoleUser&act=search'+"&role_id="+treeNode.id);
			role_user_search_page(util.getItem('orl'));
        }
    };
    var initData =function(){};
    return {
        init:function(){
            initElements();
            handleForm();
            initData();
        }
    }
}();

function role_user_search_page(url){
	util.page(url);
}

function roleuser_add(o){
    var obj = $.fn.zTree.getZTreeObj('roleuser_role_list').getSelectedNodes()[0];
    if (!obj)
    {
        $('.modal-scrollable').trigger('click');
        util.xalert('很抱歉，请先选择您要操作的角色！');
        return false;
    }
    util._pop($(o).attr('data-url'),{role_id:obj.id});
}

$import(["public/js/jquery-ztree/css/zTreeStyle.css","public/js/jquery-ztree/js/jquery.ztree.core-3.5.js"],function(){

    util.setItem('listDIV','role_user_search_list');

    RoleuserObj.init();
});