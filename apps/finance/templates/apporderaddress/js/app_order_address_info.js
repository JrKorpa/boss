$import(["public/js/select2/select2.min.js"],function(){
	var mem_country_id = '<%$view->get_country_id()%>';
	var pro_province = '<%$view->get_province_id()%>';
	var pro_city = '<%$view->get_city_id()%>';
	var pro_district = '<%$view->get_regional_id()%>';
	var get_id = '<%$view->get_id()%>';
	var AppMemberAddressInfogObj = function(){
		var initElements = function(){
			$('#app_order_address_info select[name="mem_country_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				value: mem_country_id
			}).change(function(e){
                $(this).valid();
                $('#app_order_address_info select[name="mem_province_id"]').empty();
                $('#app_order_address_info select[name="mem_city_id"]').empty();
                $('#app_order_address_info select[name="mem_district_id"]').empty();
                $('#app_order_address_info select[name="mem_province_id"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=bespoke&con=AppMemberAddress&act=getCity', {province: _t}, function(data) {
                        $('#app_order_address_info select[name="mem_province_id"]').append(data);
                        if (_t == mem_country_id) {
                            $('#app_order_address_info select[name="mem_province_id"]').select2("val", pro_city, true);
                        }
                        $('#app_order_address_info select[name="mem_province_id"]').change();
                    });
                }
                else {
                    $('#app_order_address_info select[name="mem_province_id"]').change();
                }
			});
			$('#app_order_address_info select[name="mem_province_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				value: pro_province
			}).change(function(e){
                $(this).valid();
                $('#app_order_address_info select[name="mem_city_id"]').empty();
                $('#app_order_address_info select[name="mem_district_id"]').empty();
                $('#app_order_address_info select[name="mem_city_id"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=bespoke&con=AppMemberAddress&act=getCity', {province: _t}, function(data) {
                        $('#app_order_address_info select[name="mem_city_id"]').append(data);
                        if (_t == pro_province) {
                            $('#app_order_address_info select[name="mem_city_id"]').select2("val", pro_city, true);
                        }
                        $('#app_order_address_info select[name="mem_city_id"]').change();
                    });
                }
                else {
                    $('#app_order_address_info select[name="mem_city_id"]').change();
                }
			});
			$('#app_order_address_info select[name="mem_city_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
				value: pro_city
			}).change(function(e){
                $(this).valid();
                $('#app_order_address_info select[name="mem_district_id"]').empty();
                $('#app_order_address_info select[name="mem_district_id"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=bespoke&con=AppMemberAddress&act=getDistrict', {city: _t}, function (data) {
                        $('#app_order_address_info select[name="mem_district_id"]').append(data);
                        if (_t == pro_city) {
                            $('#app_order_address_info select[name="mem_district_id"]').select2("val", pro_district, true);
                        }
                        $('#app_order_address_info select[name="mem_district_id"]').change();
                    });
                }
                else {
                    $('#app_order_address_info select[name="mem_district_id"]').change();
                }
			});
			$('#app_order_address_info select[name="mem_district_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_order_address_info select[name="mem_is_def"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		}

		var handleForm = function(){
			//表单验证和提交
			var url = get_id ? 'index.php?mod=finance&con=AppOrderAddress&act=update' : 'index.php?mod=finance&con=AppOrderAddress&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert(get_id ? "修改成功!": "添加成功!");
						if (get_id)
						{//刷新当前页
							util.retrieveReload();
						}
						else
						{//刷新首页
							util.retrieveReload();
						}
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");
				}
			};

			$('#app_order_address_info').validate({
				errorElement: 'span', //default input error app_member_address container
				errorClass: 'help-block', // default input error app_member_address class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    customer: {
                        required: true,
                        checkName: true,
                        maxlength:19,
                        
                    },
                    mem_country_id: {
                        required: true,
                    },
                    mem_province_id: {
                        required: true,
                    },
                    mem_city_id: {
                        required: true,
                    },
                    mem_district_id: {
                        required: true,
                    },
                    mem_address: {
                        required: true,
                        maxlength:250,
                    },
                    mem_is_def: {
                        required: true,
                    },
                    mobile: {
                        required: true,
                        isMobile:true
                    },
				},
				messages: {
                    customer: {
                        required: "顾客名不能为空.",
                        maxlength:'客户名称不能过长',

						

                    },
                    mobile: {
                        required: "会员电话不符合规则.",
                        isMobile:"你这号码太牛了，都能打到火星去",
                    },
                    mem_country_id: {
                        required: "会员国家不能为空."
                    },
                    mem_province_id: {
                        required: "会员省不能为空."
                    },
                    mem_city_id: {
                        required: "会员城市不能为空."
                    },
                    mem_district_id: {
                        required: "会员区不能为空."
                    },
                    mem_address: {
                        required: "会员详细地址不能为空.",
                        maxlength:'客户名称不能过长',
                    },
                    mem_is_def: {
                        required: "是否默认不能为空."
                    },
				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#app_order_address_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_order_address_info input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#app_order_address_info').validate().form()) {
						$('#app_order_address_info').submit();
					}
					else
					{
						return false;
					}
				}
			});
		}
		var initData = function(){}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	AppMemberAddressInfogObj.init();
});