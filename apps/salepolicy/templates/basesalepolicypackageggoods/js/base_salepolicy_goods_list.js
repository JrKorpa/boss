//分页
function base_salepolicy_packageg_goods_search_page(url){
    util.page(url);
}

//匿名回调
$import('public/js/select2/select2.min.js',function(){
    util.setItem('orl','index.php?mod=salepolicy&con=BaseSalepolicyPackagegGoods&act=search');//设定刷新的初始url
    util.setItem('formID','base_salepolicy_packageg_goods_search_form');//设定搜索表单id
    util.setItem('listDIV','base_salepolicy_packageg_goods_search_list');//设定列表数据容器id

    //匿名函数+闭包
    var obj = function(){

        var initElements = function(){
            var test = $("#base_salepolicy_packageg_goods_search_form input[name='isXianhuo']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {                             //开通系统
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }

            $('#base_salepolicy_packageg_goods_search_form select').select2({
                placeholder: "请选择货品类型",
                allowClear: true
            });

            //下拉组件重置
            $('#base_salepolicy_packageg_goods_search_form :reset').on('click',function(){
                $('#base_salepolicy_packageg_goods_search_form select').select2("val",'');
            })
        };

        var handleForm = function(){
            util.search();
        };

        var initData = function(){
            util.closeForm(util.getItem("formID"));
            base_salepolicy_packageg_goods_search_page(util.getItem("orl"));
        }
        return {
            init:function(){
                initElements();//处理搜索表单元素和重置
                handleForm();//处理表单验证和提交
                initData();//处理默认数据
            }
        }
    }();

    obj.init();
});