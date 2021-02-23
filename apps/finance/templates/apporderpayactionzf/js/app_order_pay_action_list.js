//分页
function app_order_pay_action_search_page_a(url) {
    util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function() {
    util.setItem('orl', 'index.php?mod=finance&con=AppOrderPayActionZf&act=search');//设定刷新的初始url
    util.setItem('formID', 'app_order_pay_action_search_form');//设定搜索表单id
    util.setItem('listDIV', 'app_order_pay_action_search_list');//设定列表数据容器id

    //匿名函数+闭包
    var obj = function() {

        var initElements = function() {

            //下拉组件
            $('#app_order_pay_action_search_form select').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });

            //时间控件
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }

            //重置
            $('#app_order_pay_action_search_form :reset').on('click', function() {
                //下拉重置
                $('#app_order_pay_action_search_form select[name="order_department"]').select2("val", '').change();
                $('#app_order_pay_action_search_form select[name="order_status"]').select2("val", '').change();
                $('#app_order_pay_action_search_form select[name="order_pay_status"]').select2("val", '').change();
            });
        };

        var handleForm = function() {
            util.search();
        };

        var initData = function() {
            util.closeForm(util.getItem("formID"));
            app_order_pay_action_search_page_a(util.getItem("orl"));
        }
        return {
            init: function() {
                initElements();//处理搜索表单元素和重置
                handleForm();//处理表单验证和提交
                initData();//处理默认数据
            }
        }
    }();

    obj.init();
});






