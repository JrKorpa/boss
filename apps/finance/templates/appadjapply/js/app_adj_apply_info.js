$import(["public/js/select2/select2.min.js"],function(){
	var info_id= '<%$view->get_apply_id()%>';

	var obj = function(){
		var initElements = function(){
            // 上传按钮点击事件
            $("input[type=file]").change(function() {
                $(this).parents(".fileinput-button").parent(".fileupload-buttonbar").find('p.control-label').text($(this).val());
            });
            $("input[type=file]").each(function() {
                if ($(this).val() == "") {
                    $(this).parents(".fileinput-button").next("control-label").text("未选择文件...");
                }
            });// 上传按钮点击事件结束
			$('#app_adj_apply_info select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_adj_apply_info select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		};

		//表单验证和提交
		var handleForm = function(){
			var url = info_id ? 'index.php?mod=finance&con=AppAdjApply&act=update' : 'index.php?mod=finance&con=AppAdjApply&act=insert';
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
						bootbox.alert({
							message: info_id ? "修改成功!": "添加成功!",
							buttons: {
									   ok: {
											label: '确定'
										}
									},
							animate: true,
							closeButton: false,
							title: "提示信息",
							callback:function(){
								if (data._cls)
								{
									util.retrieveReload();
									util.syncTab(data.tab_id);
								}
								else
								{//刷新首页
									app_adj_apply_search_page(util.getItem("orl"));
									//util.page('index.php?mod=management&con=application&act=search');
								}
							}
						});



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

			$('#app_adj_apply_info').validate({
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
					$("#app_adj_apply_info").ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#app_adj_apply_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_adj_apply_info').validate().form()
				}
			});
		};
		var initData = function(){

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

function app_adj_apply_goods_download_mo_info()
{
    location.href = "index.php?mod=finance&con=AppPayApply&act=themes&target=demo";
}