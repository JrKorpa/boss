$import(function(){
    var obj = function(){
        var initElements = function(){
            $('#goods_batch_pushold_btn .close-btn').click(function(){
                $('.modal-scrollable').trigger('click');
            });
        };
        //表单验证和提交
        var handleForm = function(){
            $('#goods_batch_pushold_btn button').on('click',function(){
                var goods_arr = $('#goods_batch_pushold textarea[name="goods_arr"]').val();
                if(!goods_arr){
                    util.xalert('请输入货号');return false;
                }
                goods_arr = goods_arr.split("\n");
                //alert(goods_arr);return false;
                var url = 'index.php?mod=warehouse&con=WarehouseGoods&act=batchPushOldsys';
                $.post(url,{'goods_arr':goods_arr},function(e){
                    //alert(e);return false;
                    if(e.success == 1){
                    util.xalert('操作成功');
                    }else{
                    util.xalert(e.error);
                    }
                })

            })
        };
        return {
            init:function(){
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交

            }
        }
    }();
    obj.init();
});