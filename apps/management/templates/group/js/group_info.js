$import("public/js/select2/select2.min.js",function(){
	var info_form_id = 'group_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=Group&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
	
	var group_info_parent_id= '<%$view->get_parent_id()%>';
	var GroupInfo = function(){
		var initElements = function(){
			$('#group_info select[name="parent_id"]').select2({
				placeholder: "默认顶级",
				allowClear: true
			});
		};

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
										group_search_page(util.getItem("orl"));
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
					name:{
						required:true,
						maxlength:50,
						checkCN:true
					},
					code:{
						required:true,
						maxlength:50
					},
					note:{
						maxlength:255					
					}	
				},
				messages: {
					name:{
						required:'工作组名称必填',
						maxlength:'输入的最大长度是50'
					},
					code:{
						required:'编码必填',
						maxlength:'输入的最大长度是50'
					},
					note:{
						maxlength:'输入的最大长度是255'					
					}	
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
			$('#group_info :reset').on('click',function(){
				$('#group_info select[name="parent_id"]').select2("val",group_info_parent_id);
			})
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	
	GroupInfo.init();
});