$import("public/js/select2/select2.min.js",function(){
	var info_id= '<%$view->get_id()%>';
	var business_scope = '<%$view->get_business_scope()%>';

	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#app_processor_process_info input[name='is_enabled']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

			/*选择部门*/
			$('#app_processor_process_info select[name="department_id"]').select2({
				placeholder: "请选择部门	",
				allowClear: true
			}).change(function () {
				var val = $(this).find('option:selected').text();
				$('#app_processor_process_info input[name="depart_name"]').val(val);
			});


			/*选择审核人*/
			$('#app_processor_process_info select[name="user_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				var user = $(this).val();
				if(!user){return}
				var ids = [];
				$('#app_processor_process_info input[name="user[]"]').each(function(){
					ids.push($(this).val());
				});
				//判断是否已存在
				var flag = false;
				for(var x in ids){
					if(ids[x] == user){
						flag = true;
						break;
					}
				}
				if(flag){
					$('#processor_process_select_user').empty().append("<b>该用户已存在</b>");
				}else{
					$('#processor_process_select_user').empty();
					var str = "<tr><td></td><td>"+e.added.text+"</td><td><span class='btn btn-xs red' onclick='del(this)'><i class='fa fa-times'>删除</i></span> </td> <input type='hidden' name='user[]' value='"+e.added.id+"' from='app_processor_process_info' /></tr>";
					$('#processor_process_user_tbody').append(str);
					var n = $('#processor_process_user_tbody tr').length-1;
					$($("#processor_process_user_tbody").find("tr")[n]).find("td:first").text('第'+(n+1)+'步');
				}
				$(this).valid();
			});

			$('#app_processor_process_info select[name="scope[]"]').select2({
				placeholder: "请选择(可多选)",
				allowClear: true
			});

			$('#app_processor_process_info .form-actions span').click(function(){
				$('.modal-scrollable').trigger('click');
			});

		}
		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=processor&con=AppProcessorProcess&act=update' : 'index.php?mod=processor&con=AppProcessorProcess&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
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
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
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
										app_processor_process_search_page(util.getItem("orl"));
									}
								}
							}
						);


					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert({   
							message: data.error ? data.error : (data ? data :'程序异常'),
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

			$('#app_processor_process_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					
				},
				messages: {
					
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
					$("#app_processor_process_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_processor_process_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_processor_process_info').validate().form()
				}
			});
		};
		var initData = function(){
			if(info_id){
				var url = 'index.php?mod=processor&con=AppProcessorProcess&act=getUserTable';
				$.post(url,{'id':info_id},function(e){
					$('#processor_process_user_tbody').append(e);
				});
				$('#app_processor_process_info select[name="scope[]"]').val(business_scope.split(",")).trigger("change");
			}

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


function del(e){
	$(e).parent().parent().remove();
	var n = $('#processor_process_user_tbody tr').length;
	if(n>0){
		for(var i =0 ;i < n ;i++){
			$($("#processor_process_user_tbody").find("tr")[i]).find("td:first").text('第'+(i+1)+'步');
		}
	}

}