$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js"], function () {
        var info_form_id = 'shop_cfg_info';//form表单id
        var info_form_base_url = 'index.php?mod=management&con=ShopCfg&act=';//基本提交路径
        var info_id = '<%$view->get_id()%>';//记录主键
        var count_id = '<%$view->get_country_id()%>';
        var province_id = '<%$view->get_province_id()%>';
        var city_id = '<%$view->get_city_id()%>';
        var regional_id = '<%$view->get_regional_id()%>';


        var obj = function () {
                var initElements = function () {
                        $('#' + info_form_id + ' select').select2({
                                placeholder: "请选择",
                                allowClear: true,
                        }).change(function (e) {
                                $(this).valid();
                        });
                        var test = $("#" + info_form_id + " input[name='shop_type']:not(.toggle, .star, .make-switch)");
                        if (test.size() > 0) {
                                test.each(function () {
                                        if ($(this).parents(".checker").size() == 0) {
                                                $(this).show();
                                                $(this).uniform();
                                        }
                                });
                        }

						if ($.datepicker) {
							$('.date-picker').datepicker({
								format: 'yyyy-mm-dd',
								rtl: App.isRTL(),
								autoclose: true,
								clearBtn: true
							});
							$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
						}

                        //点国家出现省的列表
                        $('#' + info_form_id + ' select[name="country_id"]').change(function (e) {
                                //debugger;
                                $(this).valid();
                                $('#' + info_form_id + ' select[name="province_id"]').empty().attr('readOnly', false).append('<option value=""></option>');
                                var t_v = $(this).val();
                                if (t_v) {
                                        $.post(info_form_base_url + 'getProvince', {count: t_v}, function (data) {
                                                $('#' + info_form_id + ' select[name="province_id"]').append(data);
                                                if (province_id) {
                                                        $('#' + info_form_id + ' select[name="province_id"]').select2('val', province_id).change();
                                                }
                                                else
                                                {
                                                        $('#' + info_form_id + ' select[name="province_id"]').select2('val', '').change();
                                                }
                                        });
                                }
                                else
                                {
                                        $('#' + info_form_id + ' select[name="province_id"]').select2('val', '').attr('readOnly', false).change();
                                }
                        });

                        //点省出现市的列表
                        $('#' + info_form_id + ' select[name="province_id"]').change(function (e) {
                                $(this).valid();
                                $('#' + info_form_id + ' select[name="city_id"]').attr('readOnly', false).html('<option value=""></option>');
                                $('#' + info_form_id + ' select[name="regional_id"]').html('<option value=""></option>');


                                var t_v = $(this).val();
                                if (t_v) {
                                        $.post(info_form_base_url + 'getProvince', {count: t_v}, function (data) {
                                                $('#' + info_form_id + ' select[name="city_id"]').append(data);
                                                if (city_id) {
                                                        $('#' + info_form_id + ' select[name="city_id"]').select2('val', city_id).change();
                                                }
                                                else
                                                {
                                                        $('#' + info_form_id + ' select[name="city_id"]').select2('val', '').change();
                                                }
                                        });
                                }
                                else
                                {
                                        $('#' + info_form_id + ' select[name="city_id"]').select2('val', '').attr('readOnly', false).change();
                                }
                        });
                        //点市出现区的列表
                        $('#' + info_form_id + ' select[name="city_id"]').change(function (e) {
                                $(this).valid();
                                $('#' + info_form_id + ' select[name="regional_id"]').attr('readOnly', false).html('<option value=""></option>');
                                var t_v = $(this).val();
                                if (t_v) {
                                        $.post(info_form_base_url + 'getProvince', {count: t_v}, function (data) {
                                                $('#' + info_form_id + ' select[name="regional_id"]').append(data);
                                                if (regional_id) {
                                                        $('#' + info_form_id + ' select[name="regional_id"]').select2('val', regional_id).change();
                                                }
                                                else
                                                {
                                                        $('#' + info_form_id + ' select[name="regional_id"]').select2('val', '').change();
                                                }
                                        });
                                }
                                else
                                {
                                        $('#' + info_form_id + ' select[name="regional_id"]').select2('val', '').attr('readOnly', false).change();
                                }
                        });

                };
                //表单验证和提交
                var handleForm = function () {
                        //console.log($('#shop_cfg_info'));

                        var url = info_form_base_url + (info_id ? 'c_update' : 'c_insert');
                        var options1 = {
                                url: url,
                                error: function ()
                                {
                                        util.timeout(info_form_id);
                                },
                                beforeSubmit: function (frm, jq, op) {
                                        return util.lock(info_form_id);
                                },
                                success: function (data) {
                                        $('#' + info_form_id + ' :submit').removeAttr('disabled');//解锁
                                        if (data.success == 1) {
                                                $('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
                                                util.xalert(
                                                        info_id ? "修改成功!" : "添加成功!",
                                                        function () {

                                                                if (info_id)
                                                                {//刷新当前页
                                                                        util.retrieveReload();
                                                                }
                                                                else
                                                                {//刷新首页
                                                                        util.retrieveReload();
                                                                }

                                                        }
                                                );
                                        }
                                        else
                                        {
                                                util.error(data);//错误处理
                                        }
                                }
                        };

                        $('#' + info_form_id).validate({
                                errorElement: 'span', //default input error largearea container
                                errorClass: 'help-block', // default input error largearea class
                                focusInvalid: false, // do not focus the last invalid input
                                rules: {
                                        dealer_name: {
                                                //required: true
                                        },
                                        join_type: {
                                                //required: true
                                        },
                                        shop_responsible_name: {
                                                //required: true
                                        },
                                        shop_responsible_tel: {
                                                //required: true
                                        },
                                        shop_responsible_mail: {
                                                //required: true
                                        },
                                        contract_status: {
                                                //required: true
                                        },
                                        contract_start_time: {
                                                //required: true
                                        },
                                        contract_end_time: {
                                                //required: true
                                        },
                                        trademark_use_fee: {
                                                //required: true
                                        },
                                        credit_guarantee_fee: {
                                                //required: true
                                        },
                                        security_user: {
                                                //required: true
                                        },
                                        diamond_gem_fee: {
                                                //required: true
                                        },
                                        su_jin_fee: {
                                                //required: true
                                        },
                                        gia_diamond_fee: {
                                                //required: true
                                        },
                                        other_diamond_fee: {
                                                //required: true
                                        },
                                        stock_index: {
                                                //required: true
                                        },
                                        development_index: {
                                                //required: true
                                        },
                                        start_shop_time: {
                                                //required: true
                                        },
                                        regional_manager: {
                                                //required: true
                                        },
                                        remarks: {
                                                //required: true
                                        }

                                },
                                messages: {
                                        dealer_name: {
                                                required: "经销商公司名称必需填写",
                                        },
                                        join_type: {
                                                required: "加盟类型必需填写",
                                        },
                                        shop_responsible_name: {
                                                required: "店铺负责人必需填写",
                                        },
                                        shop_responsible_tel: {
                                                required: "负责人电话必需填写",
                                        },
                                        shop_responsible_mail: {
                                                required: "负责人邮箱必选",
                                        },
                                        contract_status: {
                                                required: "合同状态必选",
                                        },
                                        contract_start_time: {
                                                required: "合同开始日期必需填写"
                                        },
                                        contract_end_time: {
                                                required: "合同结束日期必需填写"
                                        },
                                        trademark_use_fee: {
                                                required: "商标使用费必需填写"
                                        },
                                        credit_guarantee_fee: {
                                                required: "授信及担保额度必需填写",
                                        },
                                        security_user: {
                                                required: "担保人必需填写"
                                        },
                                        diamond_gem_fee: {
                                                required: "钻石及宝石管理费必需填写",
                                                //url:"二级域名填写不符合规定"
                                        },
                                        su_jin_fee: {
                                                required: "素金类管理费必需填写",
                                        },
                                        gia_diamond_fee: {
                                                required: "GIA裸钻管理费必需填写",
                                        },
                                        other_diamond_fee: {
                                                required: "其他裸钻管理费必需填写",
                                        },
                                        stock_index: {
                                                required: "进货指标必需填写",
                                        },
                                        development_index: {
                                                required: "拓展指标必需填写",
                                        },
                                        start_shop_time: {
                                                required: "开店时间必需填写",
                                        },
                                        regional_manager: {
                                                required: "区域经理必需填写",
                                        },
                                        remarks: {
                                                required: "备注必需填写",
                                        },
                                },
                                highlight: function (element) { // hightlight error inputs
                                        $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                                        ////$(element).focus();
                                },
                                success: function (label) {
                                        label.closest('.form-group').removeClass('has-error');
                                        label.remove();
                                },
                                errorPlacement: function (error, element) {
                                        error.insertAfter(element.closest('.form-control'));
                                },
                                submitHandler: function (form) {
                                        $("#" + info_form_id).ajaxSubmit(options1);
                                }
                        });
                        //回车提交
                        $('#' + info_form_id + ' input').keypress(function (e) {
                                if (e.which == 13) {
                                        $('#' + info_form_id).validate().form();
                                }
                        });
                }
                var initData = function () {
                        $('#' + info_form_id + ' :reset').on('click', function () {
                                $('#' + info_form_id + ' select[name="country_id"]').select2("val", count_id).change();
                                $('#' + info_form_id + ' select[name="country_id"] option[value=' + count_id + ']').attr('selected', true);
                        });
                        if (info_id) {
                                $('#' + info_form_id + ' :reset').click();
                        }

                }
                return {
                        init: function () {
                                initElements();
                                handleForm();
                                initData();
                        }
                }
        }();
        obj.init();
});