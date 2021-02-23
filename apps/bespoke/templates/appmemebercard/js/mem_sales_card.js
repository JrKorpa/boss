$import(function(){
    $('#card_sales').click(function () {
        var url = 'index.php?mod=bespoke&con=AppMemeberCard&act=giveMember';
        var data = {
            'id':$("input[name='id']").val(),
            'member_name':$("input[name='member_name']").val(),
            'member_tel':$("input[name='member_tel']").val(),
        };
        $.post(url,data,function(res){
            if(res.success==1){
                alert('操作成功!');
                $('.modal-scrollable').trigger('click');//关闭遮罩
            }else{
                alert(res.error);
            }
        },'json');
    });

});