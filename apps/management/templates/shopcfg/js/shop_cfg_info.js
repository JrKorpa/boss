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

                        var url = info_form_base_url + (info_id ? 'update' : 'insert');
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
                                                                        util.page(util.getItem('url'));
                                                                }
                                                                else
                                                                {//刷新首页
                                                                        shop_cfg_search_page(util.getItem("orl"));
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
                                        shop_name: {
                                                required: true,
                                                checkCN: true,
                                                maxlength: 128
                                        },
                                        short_name: {
                                                required: true,
                                                checkField: true,
                                                maxlength: 64
                                        },
                                        shop_address: {
                                                required: true,
                                                maxlength: 200
                                        },
                                        count_id: {
                                                required: true
                                        },
                                        province_id: {
                                                required: true
                                        },
                                        city_id: {
                                                required: true
                                        },
                                        regional_id: {
                                                required: true
                                        },
                                        shop_phone: {
                                                required: true,
                                                maxlength: 30
                                        },
                                        shop_time: {
                                                required: true,
                                                maxlength: 50
                                        },
                                        start_shop_time: {
                                                required: true
                                        },
                                        shop_traffic: {
                                                required: true
                                        },
                                        second_url: {
                                                required: true
                                        },
                                        baidu_maps: {
                                                required: true
                                        }

                                },
                                messages: {
                                        shop_name: {
                                                required: "体验店名字必需填写",
                                                maxlength: "体验店名字必需小于128"
                                        },
                                        short_name: {
                                                required: "体验店简称必需填写",
                                                maxlength: "体验店简称必需小于64"
                                        },
                                        count_id: {
                                                required: "国家必选",
                                        },
                                        province_id: {
                                                required: "省必选",
                                        },
                                        city_id: {
                                                required: "市必选",
                                        },
                                        regional_id: {
                                                required: "区必选",
                                        },
                                        shop_address: {
                                                required: "体验店地址必需填写",
                                                maxlength: "体验店地址必需小于200"
                                        },
                                        shop_phone: {
                                                required: "体验店电话必需填写",
                                                maxlength: "体验店电话必需小于30"
                                        },
                                        shop_time: {
                                                required: "体验店营业时间必需填写",
                                                maxlength: "体验店营业时间必需小于50"
                                        },
                                        start_shop_time: {
                                                required: "开店时间必需填写",
                                        },
                                        shop_traffic: {
                                                required: "体验店交通路线必需填写"
                                        },
                                        second_url: {
                                                required: "二级域名必需填写",
                                                //url:"二级域名填写不符合规定"
                                        },
                                        baidu_maps: {
                                                required: "百度地图坐标必需填写",
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