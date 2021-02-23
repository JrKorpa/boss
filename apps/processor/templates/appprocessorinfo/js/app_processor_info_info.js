$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'], function() {
    var app_processor_info_id = '<%$view->get_id()%>';
    var app_processor_info_pid = '<%$view->get_pid()%>';
    var app_processor_info_balance_type = '<%$view->get_balance_type()%>';
    var app_processor_info_is_invoice = '<%$view->get_is_invoice()%>';
    var app_processor_info_status = '<%$view->get_status()%>';
    var tax_invoice = '<%$view->get_tax_invoice()%>';
    var _province = '<%$view->get_area(0)%>';
    var _city = '<%$view->get_area(1)%>';
    var _district = '<%$view->get_area(2)%>';
    var pro_province = '<%$view->get_pro_area(0)%>';
    var pro_city = '<%$view->get_pro_area(1)%>';
    var pro_district = '<%$view->get_pro_area(2)%>';
    var business_scope = '<%$view->get_business_scope()%>';
    var AppProcessorInfoInfo = function() {

        var initElements = function() {
            //单选按钮美化
            var test = $("#app_processor_info_info input[name='is_invoice']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            var test = $("#app_processor_info_info input[name='status']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            //下拉列表按钮美化
            $('#app_processor_info_info select[name="business_scope[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#app_processor_info_info select[name="balance_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#app_processor_info_info select[name="pay_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#app_processor_info_info select[name="tax_invoice"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#app_processor_info_info select[name="business_license_region_1"]').select2({
                placeholder: "请选择",
                allowClear: true,
                value: _province
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_info_info select[name="business_license_region_2"]').empty();
                $('#app_processor_info_info select[name="business_license_region_3"]').empty();
                $('#app_processor_info_info select[name="business_license_region_2"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorInfo&act=getCity', {province: _t}, function(data) {
                        $('#app_processor_info_info select[name="business_license_region_2"]').append(data);
                        if (_t == _province) {
                            $('#app_processor_info_info select[name="business_license_region_2"]').select2("val", _city, true);
                        }
                        $('#app_processor_info_info select[name="business_license_region_2"]').change();
                    });
                }
                else {
                    $('#app_processor_info_info select[name="business_license_region_2"]').change();
                }
            });
            $('#app_processor_info_info select[name="business_license_region_2"]').select2({
                placeholder: "请选择",
                allowClear: true,
                value: _city
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_info_info select[name="business_license_region_3"]').empty();
                $('#app_processor_info_info select[name="business_license_region_3"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorInfo&act=getDistrict', {city: _t}, function (data) {
                        $('#app_processor_info_info select[name="business_license_region_3"]').append(data);
                        if (_t == _city) {
                            $('#app_processor_info_info select[name="business_license_region_3"]').select2("val", _district, true);
                        }
                        $('#app_processor_info_info select[name="business_license_region_3"]').change();
                    });
                }
                else {
                    $('#app_processor_info_info select[name="business_license_region_3"]').change();
                }
            });
            $('#app_processor_info_info select[name="business_license_region_3"]').select2({
                placeholder: "请选择",
                allowClear: true,
                value: _district
            }).change(function(e) {
                $(this).valid();
            });
            $('#app_processor_info_info select[name="pro_region_1"]').select2({
                placeholder: "请选择",
                allowClear: true,
                value: pro_province
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_info_info select[name="pro_region_2"]').empty();
                $('#app_processor_info_info select[name="pro_region_3"]').empty();
                $('#app_processor_info_info select[name="pro_region_2"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorInfo&act=getCity', {province: _t}, function(data) {
                        $('#app_processor_info_info select[name="pro_region_2"]').append(data);
                        if (_t == pro_province) {
                            $('#app_processor_info_info select[name="pro_region_2"]').select2("val", pro_city, true);
                        }
                        $('#app_processor_info_info select[name="pro_region_2"]').change();
                    });
                }
                else {
                    $('#app_processor_info_info select[name="pro_region_2"]').change();
                }
            });
            $('#app_processor_info_info select[name="pro_region_2"]').select2({
                placeholder: "请选择",
                allowClear: true,
                value: pro_city
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_info_info select[name="pro_region_3"]').empty();
                $('#app_processor_info_info select[name="pro_region_3"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorInfo&act=getDistrict', {city: _t}, function (data) {
                        $('#app_processor_info_info select[name="pro_region_3"]').append(data);
                        if (_t == pro_city) {
                            $('#app_processor_info_info select[name="pro_region_3"]').select2("val", pro_district, true);
                        }
                        $('#app_processor_info_info select[name="pro_region_3"]').change();
                    });
                }
                else {
                    $('#app_processor_info_info select[name="pro_region_3"]').change();
                }
            });
            $('#app_processor_info_info select[name="pro_region_3"]').select2({
                placeholder: "请选择",
                allowClear: true,
                value: pro_district
            }).change(function(e) {
                $(this).valid();
            });

        }

        var initData = function() {
            $('#app_processor_info_info :reset').on('click', function() {
                $('#app_processor_info_info select[name="business_license_region_1"]').select2("val", _province).change();
                $('#app_processor_info_info select[name="business_license_region_2"]').select2("val", _city).change();
                $('#app_processor_info_info select[name="business_license_region_3"]').select2("val", _district).change();
                $('#app_processor_info_info select[name="pro_region_1"]').select2("val", pro_province).change();
                $('#app_processor_info_info select[name="pro_region_2"]').select2("val", pro_city).change();
                $('#app_processor_info_info select[name="pro_region_3"]').select2("val", pro_district).change();
                $('#app_processor_info_info select[name="business_scope"]').select2("val", business_scope).change();
                $('#app_processor_info_info select[name="tax_invoice"]').select2("val", tax_invoice).change();
                $('#app_processor_info_info select[name="balance_type"]').select2("val", app_processor_info_balance_type).change();

                $("#app_processor_info_info input[name='is_invoice'][value='" + app_processor_info_is_invoice + "']").attr("checked", "checked");
                var test_is_invoice = $("#app_processor_info_info input[name='is_invoice']:not(.toggle, .star, .make-switch)");
                if (test_is_invoice.size() > 0) {
                    test_is_invoice.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }
                $("#app_processor_info_info input[name='status'][value='" + app_processor_info_status + "']").attr("checked", "checked");
                var test_status = $("#app_processor_info_info input[name='status']:not(.toggle, .star, .make-switch)");
                if (test_status.size() > 0) {
                    test_status.each(function() {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
                }
            })
            if (app_processor_info_id)
            {//修改
                $('#app_processor_info_info :reset').click();
            }

        }

        var handleForm = function() {
            var url = app_processor_info_id ? 'index.php?mod=processor&con=AppProcessorInfo&act=update' : 'index.php?mod=processor&con=AppProcessorInfo&act=insert';
            var options1 = {
                url: url,
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        alert(app_processor_info_id ? "修改成功!" : "添加成功!");
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        if (app_processor_info_id)
                        {//刷新当前页
                            util.page(util.getItem('url'));
                        }
                        else
                        {//刷新首页
                            app_processor_info_search_page(util.getItem("orl"));
                        }
                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        alert(data.error ? data.error : (data ? data : '程序异常'));
                    }
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };

            $('#app_processor_info_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    code: {
                        required: true,
                        maxlength: 30
                    },
                    name: {
                        required: true,
                        maxlength: 30
                    },
                    business_license: {
                        required: true
                    },
                    tax_registry_no: {
                        required: true
                    },
                    business_license_region_1: {
                        required: true
                    },
                    business_license_region_2: {
                        required: true
                    },
                    business_license_region_3: {
                        required: true
                    },
                    bank_name: {
                        required: true
                    },
                    account_name: {
                        required: true
                    },
                    business_scope: {
                        required: true
                    },
                    cycle: {
                        required: true
                    },
                    pay_type: {
                        required: true
                    },
                    tax_invoice: {
                        required: true
                    },
                    tax_point: {
                        required: true
                    },
                    balance_type: {
                        required: true
                    },
                    pro_contact: {
                        required: true
                    },
                    company: {
                        required: true,
                        maxlength: 50
                    },
                    contact: {
                        required: true,
                        maxlength: 10
                    },
                    pro_phone: {
                        required: true,
                        isMobile: true
                    },
                    kela_phone: {
                        required: true,
                        isMobile: true
                    },
                    pro_address: {
                        required: true,
                        maxlength: 30
                    },
                    pro_email: {
                        email: true
                    },
                    account: {
                        required: true,
                        rangelength: [15, 30]
                    },
                    purchase_amount:{
                        min:0,
                        number:true
                    }
                },
                messages: {
                    code: {
                        required: "供应商编码不能为空."
                    },
                    name: {
                        required: "供应商名称不能为空."
                    },
                    business_license: {
                        required: '营业执照号码不能为空'
                    },
                    tax_registry_no: {
                        required: '税务登记证号不能为空'
                    },
                    business_license_region_1: {
                        required: '营业执照地址-省不能为空'
                    },
                    business_license_region_2: {
                        required: '营业执照地址-市不能为空'
                    },
                    business_license_region_3: {
                        required: '营业执照地址-区不能为空'
                    },
                    bank_name: {
                        required: '开户银行不能为空'
                    },
                    account_name: {
                        required: '户名不能为空'
                    },
                    business_scope: {
                        required: '经营范围不能为空'
                    },
                    cycle: {
                        required: '出货周期不能为空'
                    },
                    pay_type: {
                        required: '结算方式不能为空'
                    },
                    tax_invoice: {
                        required: '增值税发票不能为空'
                    },
                    tax_point: {
                        required: '税点不能为空'
                    },
                    balance_type: {
                        required: '付款周期不能为空'
                    },
                    pro_contact: {
                        required: '公司联系人不能为空'
                    },
                    company: {
                        required: "加工商所属公司不能为空."
                    },
                    contact: {
                        required: "BDD紧急联系人不能为空."
                    },
                    pro_phone: {
                        required: "电话不能为空."
                    },
                    kela_phone: {
                        required: "BDD紧急联系电话."
                    },
                    pro_email: {
                        email: "邮箱格式不正确"
                    },
                    account: {
                        required: '银行账户不能为空'
                    },
                    purchase_amount:{
                        min:'采购额度必须是正数',
                        number:'采购额度必须是数字'
                    }
                },
                highlight: function(element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#app_processor_info_info").ajaxSubmit(options1);
                }
            });

            $('#app_processor_info_info input').keypress(function(e) {
                if (e.which == 13) {
                    if ($('#app_processor_info_info').validate().form()) {
                        $('#app_processor_info_info').submit();
                    }
                    else
                    {
                        return false;
                    }
                }
            });
        };

        var portlet = function(){
            var obj =  $('#app_processor_info_info .portlet-title');
            for(var i in obj)
            {
                if (parseInt(i))
                {
                    $(obj[i]).click();
                }
            }
        };

        return {
            init: function() {
                handleForm();
                initElements();
                initData();
                portlet();
            }
        }
    }();

    AppProcessorInfoInfo.init();


});