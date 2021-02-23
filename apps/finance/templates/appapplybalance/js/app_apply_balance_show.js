$import(function(){
    $(function(){
        var url = 'index.php?mod=finance&con=AppApplyBalance&act=showBalance';
        var apply_array = '<%$view->get_apply_array()%>';
        $.post(url,{"apply_arr":apply_array},function(e){
            $('#balance_show_detail_list').append(e);
        });
    })
});