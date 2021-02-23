$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'],function(){
	var info_form_id = 'app_diamond_color_info';//form表单id
	var info_form_base_url = 'index.php?mod=diamond&con=AppDiamondColor&act=';//基本提交路径
	var info_id= '<%$view->get_goods_id()%>';
    var color='<%$view->get_color()%>';
    var shape='<%$view->get_shape()%>';
    var clarity='<%$view->get_clarity()%>';
    var cert='<%$view->get_cert()%>';
    var clarity='<%$view->get_clarity()%>';
    var color_grade='<%$view->get_color_grade()%>';

	var obj = function(){
		var initElements = function(){
            //下拉组件
            $('#app_diamond_color_info select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });	
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
			var options1 = {
				url: url,
				error:function (){
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								if (data._cls){//查看编辑
									util.retrieveReload();//刷新查看页签
									util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
								}
								else{
									if (info_id){//刷新当前页
										util.page(util.getItem('url'));
									}else{//刷新首页
										app_diamond_color_search_page(util.getItem("orl"));
									}
								}
							}
						);
					}else{
						util.error(data);//错误处理
					}
				}
			};
			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					goods_sn: {
						required: true,
						maxlength: 60
					},
					color: {
						required: true
					},
					shape: {
						required: true
					},
					clarity: {
						required: true
					},
					color_grade: {
						required: true
					},
					carat: {
						required: true,
						number:true,
						isFloat:true,
						maxlength: 10
					},
					cert: {
						required: true
					},
					cert_id: {
						required: true,
						number:true
					},
					cost_price: {
						required: true,
						number:true,
						isFloat:true,
						maxlength: 10
					}
				},
				messages: {
					goods_sn: {
						required: "商品编码不能为空.",
						maxlength: "商品编码最长为60个字符."
					},
					color: {
						required: "颜色不能为空！"
					},
					color_grade: {
						required: "颜色分级不能为空！"
					},
					shape: {
						required: "形状不能为空33！"
					},
					clarity: {
						required: "净度不能为空！"
					},
					carat: {
						required: "石重不能为空！",
						number:"石重只能填数字！",
						isFloat:"石重不能为负数！",
						maxlength: '石重长度不能超过10个字符'
					},
					cert: {
						required: "证书不能为空！"
					},
					cert_id: {
						required: "证书号不能为空！",
						number: "证书号只能填数字！"
					},
					
					price: {
						required: "成本价不能为空！",
						number:"成本价只能填数字！",
						isFloat:"成本价不能为负数！",
						maxlength: '成本价长度不能超过10个字符'
					}
				},

				highlight: function (element) { // hightlight error inputs
					$(element).closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
//					console.log(label);
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
//						alert('6');
					console.log(error);
					console.log(element);
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		};
		var initData = function(){
            $('#'+info_form_id+' :reset').on('click',function(){
                $('#'+info_form_id+' select[name="color"]').select2("val",color);
                $('#'+info_form_id+' select[name="shape"]').select2("val",shape);
                $('#'+info_form_id+' select[name="clarity"]').select2("val",clarity);
                $('#'+info_form_id+' select[name="cert"]').select2("val",cert);
                $('#'+info_form_id+' select[name="color_grade"]').select2("val",color_grade);
                
            });
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});