//匿名回调
$import(function(){
    //匿名函数+闭包
    var obj = function(){

        var initElements = function(){};

        var handleForm = function(){
            $('#product_send_drill_search_from button').on('click',function(){
                var bc_sn = $('#product_send_drill_search_from textarea[name="bc_sn_arr"]').val();
                if(!bc_sn){
                    util.xalert('请输入布产单号');return;
                }
                bc_sn = bc_sn.split("\n");
                var sreach_url = 'index.php?mod=processor&con=ProductSendDrill&act=search';
                $.post(sreach_url,{'bc_sn_arr':bc_sn},function(e){
                    if(e == 0){
                        util.xalert('请输入有效布产单号');return;
                    }else{
                        var down_url = 'index.php?mod=processor&con=ProductSendDrill&act=downSendDrillCSV&bc_sn_arr='+bc_sn;
                        window.open(down_url)
                    }
                });

            })
        };

        var initData = function(){
            $('#product_send_search_from button').on('click',function(){
                var zhengshuhao = $('#product_send_search_from input[name="zhengshuhao"]').val();
                if(!zhengshuhao){
                    util.xalert('请输入证书单号');return;
                }
                var sreach_url = 'index.php?mod=processor&con=ProductSendDrill&act=proofSearch';
                $.post(sreach_url,{'zhengshuhao':zhengshuhao},function(e) {
                    $('#product_zhengshu_search_list').empty().append(e);
                })

            })
            $('#product_zhengshuhao_search_form input').keypress(function (e) {
                if (e.which == 13) {
                    return false;
                }
            });
        }
        return {
            init:function(){
                initElements();//处理搜索表单元素和重置
                handleForm();
                initData();//处理默认数据
            }
        }
    }();

    obj.init();
});
