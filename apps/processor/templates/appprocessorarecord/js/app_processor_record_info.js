$import("public/js/select2/select2.min.js",function() {
    var info_id = '<%$view->get_id()%>';
    var _province = '<%$view->get_area(0)%>';
    var _city = '<%$view->get_area(1)%>';
    var _district = '<%$view->get_area(2)%>';
    var pro_province = '<%$view->get_pro_area(0)%>';
    var pro_city = '<%$view->get_pro_area(1)%>';
    var pro_district = '<%$view->get_pro_area(2)%>';
    var business_scope = '<%$view->get_business_scope()%>';
    var pay_type = '<%$view->get_pay_type_old()%>';
    var sup_id = '<%$view->get_info_id()%>';
    var balance_type = '<%$view->get_balance_type()%>';
    var tax_point = '<%$view->get_tax_point()%>';

    var obj = function() {
        var initElements = function() {
            //下拉列表美化
            if (!jQuery().uniform) {
                return;
            }
            $('#app_processor_record_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#app_processor_record_info input[name="name"]').blur(function(){
                var url = 'index.php?mod=processor&con=AppProcessorARecord&act=autoCode';
                var value = /^([0-9a-zA-Z]+)$/.test($(this).val())?'':$(this).val();
                if (value)
                {
                    var data = {'name':value};
                    $.post(url,data,function(e){
                        $('#app_processor_record_info input[name="code"]').val($.trim(e)).change();
                    });
                }
            });

            var test = $("#app_processor_record_info input[type='radio']");
            if (test.size() > 0) {                             //开通系统
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            $("#app_processor_record_info input[name='is_open']").change(function(){
                if($(this).val()==1){
                    $('#app_processor_record_password').fadeIn("slow");
                }else{
                    $('#app_processor_record_password').fadeOut("slow");
                }
            });

            $('#app_processor_record_info select[name="balance_type"]').change(function(){
                if($(this).val()==2){
                    $('#app_processor_record_balance_day').fadeIn("slow");
                }else{
                    $('#app_processor_record_balance_day').fadeOut("slow");
                }
            })

            //营业地址
            $('#app_processor_record_info select[name="business_license_region_1"]').select2({
                placeholder: "请选择省",                //营业执照地址
                allowClear: true,
                value: _province
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_record_info select[name="business_license_region_2"]').empty();
                $('#app_processor_record_info select[name="business_license_region_3"]').empty();
                $('#app_processor_record_info select[name="business_license_region_2"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorARecord&act=getCity', {province: _t}, function(data) {
                        $('#app_processor_record_info select[name="business_license_region_2"]').append(data);
                        if (_t == _province) {
                            $('#app_processor_record_info select[name="business_license_region_2"]').select2("val", _city, true);
                        }
                        $('#app_processor_record_info select[name="business_license_region_2"]').change();
                    });
                }
                else {
                    $('#app_processor_record_info select[name="business_license_region_2"]').change();
                }
            });
            $('#app_processor_record_info select[name="business_license_region_2"]').select2({
                placeholder: "请选择市",
                allowClear: true,
                value: _city
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_record_info select[name="business_license_region_3"]').empty();
                $('#app_processor_record_info select[name="business_license_region_3"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorARecord&act=getDistrict', {city: _t}, function (data) {
                        $('#app_processor_record_info select[name="business_license_region_3"]').append(data);
                        if (_t == _city) {
                            $('#app_processor_record_info select[name="business_license_region_3"]').select2("val", _district, true);
                        }
                        $('#app_processor_record_info select[name="business_license_region_3"]').change();
                    });
                }
                else {
                    $('#app_processor_record_info select[name="business_license_region_3"]').change();
                }
            });
            $('#app_processor_record_info select[name="business_license_region_3"]').select2({
                placeholder: "请选择区",
                allowClear: true,
                value: _district
            }).change(function(e) {
                $(this).valid();
            });

            //取货地址
            $('#app_processor_record_info select[name="pro_region_1"]').select2({
                placeholder: "请选择省",
                allowClear: true,
                value: pro_province
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_record_info select[name="pro_region_2"]').empty();
                $('#app_processor_record_info select[name="pro_region_3"]').empty();
                $('#app_processor_record_info select[name="pro_region_2"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorARecord&act=getCity', {province: _t}, function(data) {
                        $('#app_processor_record_info select[name="pro_region_2"]').append(data);
                        if (_t == pro_province) {
                            $('#app_processor_record_info select[name="pro_region_2"]').select2("val", pro_city, true);
                        }
                        $('#app_processor_record_info select[name="pro_region_2"]').change();
                    });
                }
                else {
                    $('#app_processor_record_info select[name="pro_region_2"]').change();
                }
            });
            $('#app_processor_record_info select[name="pro_region_2"]').select2({
                placeholder: "请选择市",
                allowClear: true,
                value: pro_city
            }).change(function(e) {
                $(this).valid();
                $('#app_processor_record_info select[name="pro_region_3"]').empty();
                $('#app_processor_record_info select[name="pro_region_3"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=AppProcessorARecord&act=getDistrict', {city: _t}, function (data) {
                        $('#app_processor_record_info select[name="pro_region_3"]').append(data);
                        if (_t == pro_city) {
                            $('#app_processor_record_info select[name="pro_region_3"]').select2("val", pro_district, true);
                        }
                        $('#app_processor_record_info select[name="pro_region_3"]').change();
                    });
                }
                else {
                    $('#app_processor_record_info select[name="pro_region_3"]').change();
                }
            });
            $('#app_processor_record_info select[name="pro_region_3"]').select2({
                placeholder: "请选择区",
                allowClear: true,
                value: pro_district
            }).change(function(e) {
                $(this).valid();
            });

            //税点添加
            $('#app_processor_tax_point_btn a').on('click',function(){
                var li = $(this).parent("li");
                if (!li.hasClass('disabled')) {
                    $('#app_processor_tax_point_btn').removeClass('open');
                    li.addClass('disabled');
                    var point =  $(this).attr('data-value');;
                    var label = $(this).text();
                    var url = 'index.php?mod=processor&con=AppProcessorARecord&act=getPoint';
                    $.post(url,{'point':point,'label':label},function(e){
                        $('#app_processor_tax_point').append(e);
                    });    
                }
                return false
            });
            //获取焦点
            $("#app_processor_tax_point").on('focus','input', function(){
                var v = $(this).val();
                var d =  $(this).parent("div");
                d.parent("div").removeClass('has-error');
                d.next('span').text("");
            });
            //失去焦点
            $("#app_processor_tax_point").on('blur','input',function(){
                var v = $(this).val();
                var d =  $(this).parent("div");
                if (v == null||v == '') {
                    d.parent("div").addClass('has-error');
                    d.next('span').text('不能为空');
                }else if(isNaN(v) == true) {
                    d.parent("div").addClass('has-error');
                    d.next('span').text('只能输入数字');
                }else if (v > 100) {
                    d.parent("div").addClass('has-error');
                    d.next('span').text('不能大于100');
                }else if (v <= 0) {
                    d.parent("div").addClass('has-error');
                    d.next('span').text('不能小于或等于0');
                }
            });
            //关闭标签
            $('#app_processor_record_info .close-btn').click(function(){
                util.closeTab();
                //$('.modal-scrollable').trigger('click');
            });
        };

        //表单验证和提交
        var handleForm = function() {
            var url = info_id ? 'index.php?mod=processor&con=AppProcessorARecord&act=update' : 'index.php?mod=processor&con=AppProcessorARecord&act=insert';
            var options1 = {
                url: url,
                error: function(){
                    $('.modal-scrollable').trigger('click');
                    bootbox.alert({
                        message: "请求超时，请检查链接",
                        buttons: {
                            ok: {
                                label: '确定'
                            }
                        },
                        animate: true,
                        closeButton: false,
                        title: "提示信息"
                    });
                    return;
                },
                beforeSubmit: function(frm, jq, op) {
                    //税点验证
                    if ($("#app_processor_tax_point").find('div').hasClass('has-error') == true) {
                        $("#app_processor_tax_point").find('span.help-block').show();
                        return false;
                    }
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        util.xalert(
                            info_id ? "修改成功!": "添加成功!",
                            function(){
                                util.closeTab();
                                if (data._cls)
                                {//查看编辑
                                    util.retrieveReload();//刷新查看页签
                                    util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
                                }
                                else
                                {
                                    if (info_id)
                                    {//刷新当前页
                                        util.page(util.getItem('url'));
                                    }
                                    else
                                    {//刷新首页
                                        app_processor_record_search_page(util.getItem("orl"));
                                    }
                                }
                            }
                        );

                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        bootbox.alert({
                            message: data.error ? data.error : (data ? data : '程序异常'),
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });
                        return;
                    }
                }
            };

            $('#app_processor_record_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    name: {required:true,maxlength:30,remote:'index.php?mod=processor&con=AppProcessorARecord&act=checkCode'},
					code: {required:true,maxlength:30,checkFields:true},
                    balance_type:{required:true},
                    "business_scope[]":{required:true},
                    "point[]":{required:true,min:0},
                    bank_name:{required: true,maxlength:30},
                    account_name:{required: true,maxlength:30},
                    account:{required: true,maxlength:30},
                    pay_type:{required: true},
                    business_license:{required:true,checkFields:true,maxlength:45},
                    tax_registry_no:{required:true,checkFields:true,maxlength:45},
                    tax_invoice:{required: true},
                    business_license_region_3:{required: true},
                    business_license_address:{required: true},
                    pro_contact:{required:true},
                    pro_phone:{required: true},
                    pro_qq:{maxlength:150},
                    contact:{required:true},
                    kela_phone:{required:true,maxlength:50},
                    kela_qq:{maxlength:150},
                    department_id:{required: true},
                    purchase_amount:{isFloat:true, number:true}
                },
                messages: {
					name: {required: "供应商名称必填", maxlength: "名称长度不能超过30",remote:'只鞥填写字母数字汉字和小括号'},
					code: {required: "供应商编码必填", maxlength: "编码长度不能超过30"},
                    balance_type:{required: "付款周期必填"},
                    "business_scope[]":{required: "经营范围必填"},
                    bank_name:{required: "开户银行必填",maxlength:"不可超过30个字"},
                    account_name:{required: "户名必填",maxlength:"不可超过30个字"},
                    account:{required: "银行账户必填",maxlength:"不可超过30个字"},
                    pay_type:{required: "结算方式必填"},
                    business_license:{required: "营业执照号码必填"},
                    tax_registry_no:{required: "税务登记证号必填"},
                    tax_invoice:{required: "增值税发票必填"},
                    business_license_region_3:{required: "营业执照地址必填"},
                    business_license_address:{required: "营业执照地址必填"},
                    pro_contact:{required: "公司联系人必填"},
                    pro_phone:{required: "公司联系人手机必填"},
                    pro_qq:{maxlength:'最大长度为150'},
                    kela_qq:{maxlength:'最大长度为150'},
                    contact:{required: "BDD联系人必填"},
                    kela_phone:{required: "BDD紧急联系人手机必填"},
                    department_id:{required: "请选择申请部门"},
                    purchase_amount:{isFloat:'采购额度不能为负数', number:'采购额度必须是数字'}
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
                    $("#app_processor_record_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#app_processor_record_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#app_processor_record_info').validate().form()
                }
            });
        };
        var initData = function() {
            $('#app_processor_record_info :reset').on('click', function() {
                $('#app_processor_record_info select[name="business_license_region_1"]').select2("val", _province).change();
                $('#app_processor_record_info select[name="business_license_region_2"]').select2("val", _city).change();
                $('#app_processor_record_info select[name="business_license_region_3"]').select2("val", _district).change();
                $('#app_processor_record_info select[name="pro_region_1"]').select2("val", pro_province).change();
                $('#app_processor_record_info select[name="pro_region_2"]').select2("val", pro_city).change();
                $('#app_processor_record_info select[name="pro_region_3"]').select2("val", pro_district).change();
            })
            if (info_id)
            {//修改
                $('#app_processor_record_info :reset').click();
                var scop = $('#app_processor_record_info select[name="business_scope[]"]');
                if(business_scope == ""){
                    scop.val([]).trigger("change")
                }else{
                    scop.val(business_scope.split(",")).trigger("change");
                }
				if(pay_type.length>0)
				{
					$('#app_processor_record_info select[name="pay_type[]"]').val(pay_type.split(",")).trigger("change");
				}
                if(balance_type == 2){
                    $('#app_processor_record_balance_day').fadeIn("slow");
                }
            }

            if(tax_point){
                var point= new Array();
                point = tax_point.split(",");
                var i =0;var _ps =new Array();
                for(var i =0 ;i<point.length;i++) {
                    _ps[i]= point[i].split("|");
                    var vol = _ps[i][0];
                    $("#app_processor_tax_point_btn").find('a[data-value="'+vol+'"]').parent("li").addClass('disabled');
                }
//                for(i in point){alert(point[i]);
//                    _ps[i]= point[i].split("|");
//                    var vol = _ps[i][0];
//                    $("#app_processor_tax_point_btn").find('a[data-value="'+vol+'"]').parent("li").addClass('disabled');
//                    i++;
//                }
                var url = 'index.php?mod=processor&con=AppProcessorARecord&act=showPoint';
                $.post(url,{'points':_ps},function(e){
                    $('#app_processor_tax_point').append(e);
                });
            }
            /*二期加上
            if(sup_id){
                $('#app_processor_record_info input[name="name"]').attr('readonly','readonly');
                $('#app_processor_record_info input[name="code"]').attr('readonly','readonly');
                $('#app_processor_record_info input[name="business_license"]').attr('readonly','readonly');
                $('#app_processor_record_info input[name="tax_registry_no"]').attr('readonly','readonly');
            }*/
        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况//portlet();
            }
        }
    }();
    obj.init();
});
//删除添加税点
function del_point(e,d){
    var li = $("#app_processor_tax_point_btn a[data-value="+d+"]").parent("li").removeClass('disabled');
    $(e).parent().parent().parent().parent().remove();
}