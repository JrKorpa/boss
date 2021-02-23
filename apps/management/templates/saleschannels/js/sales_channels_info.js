$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'sales_channels_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=SalesChannels&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	var channel_class = '<%$view->get_channel_class()%>';
	var channel_type = '<%$view->get_channel_type()%>';
	var own_id = '<%$view->get_channel_own_id()%>';
	var channel_own = '<%$view->get_channel_own()%>';
	var company_id = '<%$view->get_company_id()%>';

	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('#sales_channels_info select[name="channel_class"],#sales_channels_info select[name="channel_own_id"],#sales_channels_info select[name="company_id"],#sales_channels_info select[name="wholesale_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
            $('#is_tsyd').bind("click", function () {
				if($(this).attr("checked")=='checked'){				
					$('#wholesale_id').show();
				}else{
					$('#sales_channels_info select[name="wholesale_id"]').select2("val",'0');
					$('#wholesale_id').hide();
				}
			})
			if(info_id){
				var _this=$('#sales_channels_info select[name="channel_type"]');
				var _t = _this.val();
				// var channel_option = $('#sales_channels_info select[name="channel_type"]');
				var own_option = $('#sales_channels_info select[name="channel_own_id"]');
				var url = 'index.php?mod=management&con=SalesChannels&act=appendHtml';
				var data = {'type':_this.val(),'channel_type':channel_type,'own_id':own_id};

				own_option.select2("val",'').empty();

				$.post(url,data,function(e){
					// console.log(e);
					own_option.empty().append('<option value=""></option>').append(e);//追加options
					own_option.select2('val',own_id).change();

				})
			}

			//to_channel_own
			$('#sales_channels_info select[name="channel_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				var _t = $(this).val();
				// var channel_option = $('#sales_channels_info select[name="channel_type"]');
				var own_option = $('#sales_channels_info select[name="channel_own_id"]');
				var url = 'index.php?mod=management&con=SalesChannels&act=appendHtml';
				var data = {'type':$(this).val(),'channel_type':channel_type,'own_id':own_id};

				own_option.select2("val",'').empty();

				$.post(url,data,function(e){
					// console.log(e);
					own_option.empty().append('<option value=""></option>').append(e);//追加options

					if(info_id && channel_type == _t){
						// own_option.attr('disabled', false).empty().append('<option value="'+own_id+'">'+warehouse_name+'</option>').change();
						own_option.select2('val',own_id).change();
					}else{
						own_option.select2('val','').change();
					}

				})
			});
			//end
		};

		//表单验证和提交
		var handleForm = function(){
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
			var options1 = {
				url: url,
				error:function ()
				{
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
										sales_channels_search_page(util.getItem("orl"));
									}
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

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					channel_name:{required: true,checkName:true},
					channel_code:{required: true,checkField:true},
					channel_class:{required: true},
					channel_type:{required: true},
					channel_own_id:{required: true},
					channel_email:{email:true},
					channel_phone:{isMobile:true}
				},
				messages: {
					channel_name:{required: "名称必填"},
					channel_code:{required: "编码必填"},
					channel_class:{required: "请选择分类"},
					channel_type:{required: "请选择分类"},
					channel_own_id:{required: "请选择"},
					channel_email:{email:"请填写正确的邮箱"}

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
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		}
			var initData = function(){
			$('#sales_channels_info :reset').on('click',function(){

				$('#sales_channels_info select[name="channel_class"]').select2("val",channel_class).change();
				if(info_id){
					$('#sales_channels_info select[name="channel_type"]').select2("val",channel_type)
				}else{
					$('#sales_channels_info select[name="channel_type"]').select2("val",channel_type).change();
				}
//				$('#sales_channels_info select[name="channel_type"]').select2("val",channel_type).change();
				$('#sales_channels_info select[name="channel_own_id"]').select2("val",own_id).change();

			});
			if (info_id)
			{//修改
				$('#sales_channels_info :reset').click();
			}
			//toCode
			$('#sales_channels_info input[name="channel_name"]').blur(function(){
				var url = 'index.php?mod=management&con=SalesChannels&act=toChannelCode';
				var value = /^([0-9]+)$/.test($(this).val())?'':$(this).val();
				var data = {'channel_name':value};
				$.post(url,data,function(e){
					$('#sales_channels_info input[name="channel_code"]').val(e);
				});
			});
			//end
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

