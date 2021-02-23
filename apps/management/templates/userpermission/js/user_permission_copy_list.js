function user_permission_check_copy_user(o){
    if(o.value==''){
       $("#user_permission_opruser_search_form input[name='copy_id']").val(0); 
    }
}

$import(['public/js/jquery-autocomplete/jquery.autocomplete.css','public/js/jquery-autocomplete/jquery.autocomplete.js'],function(){
    $("#user_permission_opruser_search_form input[name='name']").autocomplete('index.php?mod=management&con=userPermission&act=search', {
        matchContains: true,
        formatItem: function(row){return row[0]},
        formatResult: function(row) {return row[0].replace(/(<.+?>)/gi, '')}
    }).result(function(event,item) {

        $("#user_permission_opruser_search_form input[name='copy_id']").val(item[1]);
    });

    $('#permission_copy').click(function() {
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_copy_list').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        var url = 'index.php?mod=management&con=userPermission&act=saveCopy';
        var data = {
            'user_id':user_id,
            'copy_id':$("#user_permission_opruser_search_form input[name='copy_id']").val()
        };
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
        $.post(url,data,function(res){
            util.xalert(res.error,function(){
                    App.unblockUI($('#user_permission_search_list'));
            });
        });
    });

    $('#permission_cancel').click(function() {
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_copy_list').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        var url = 'index.php?mod=management&con=userPermission&act=delPerminssions';
        var data = {
            'user_id':user_id
        };
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
        $.post(url,data,function(res){
                util.xalert(res?'权限已取消　[操作成功]':'操作失败');
                App.unblockUI($('#user_permission_search_list'));
        });
    });



});




