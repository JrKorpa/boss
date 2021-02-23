//分页
function app_order_pay_action_list_search_page(url) {
    util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function() {
    util.setItem('orl', 'index.php?mod=finance&con=AppOrderPayActionList&act=search');//设定刷新的初始url
    util.setItem('formID', 'app_order_pay_action_list_search_form');//设定搜索表单id
    util.setItem('listDIV', 'app_order_pay_action_list_search_list');//设定列表数据容器id

    //匿名函数+闭包
    var obj = function() {

        var initElements = function() {
            $('#app_order_pay_action_list_search_form select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
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

			$('#app_order_pay_action_list_search_form :reset').on('click',function(){
				$('#app_order_pay_action_list_search_form select').select2("val","");
			})
        };

        var handleForm = function() {
            util.search();
        };

        var initData = function() {
            util.closeForm(util.getItem("formID"));
            app_order_pay_action_list_search_page(util.getItem("orl"));
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


function saveDeposit() {
    var obj = $('#app_order_pay_action_list_search_list input[name="_ids[]"]');
    var length = obj.length;
    var num = [];
    for (var j = 0; j < length; j++) {
        if (obj[j].checked == true) {
            num.push(obj.eq(j).val());
        }
    }
    if (num.length == 0) {
        alert('至少要选中一个序号！')
        return false;
    }
    $.post('index.php?mod=finance&con=AppOrderPayActionList&act=saveDeposit', {'ids': num}, function(data) {
        if (data.success == 1) {
            bootbox.alert({
                message: "收银提报成功!",
                buttons: {
                    ok: {
                        label: '确定'
                    }
                },
                animate: true,
                closeButton: false,
                title: "提示信息",
                callback: function() {
                    util.retrieveReload();
                }
            });
        }else{
            bootbox.alert({
                message: "收银提报失败!",
                buttons: {
                    ok: {
                        label: '确定'
                    }
                },
                animate: true,
                closeButton: false,
                title: "提示信息",
                callback: function() {
                }
            });
        }
    }, 'json');
}


function print_pay_action_list(){
    var formdata = $("#app_order_pay_action_list_search_form").serialize();
    window.open("index.php?mod=finance&con=AppOrderPayActionList&act=printInfo&"+formdata);
}