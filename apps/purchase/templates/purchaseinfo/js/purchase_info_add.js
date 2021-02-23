$import('public/js/select2/select2.min.js',function(){
	var purchase_info_id= '<%$view->get_id()%>';
	var t_id ='<%$view->get_t_id()%>';
	var put_in_type ='<%$view->get_put_in_type()%>';
	var obj = function(){
	
		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#purchase_info_add input[name='is_tofactory']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function (){
					if ($(this).parents(".checker").size() == 0){
						$(this).show();
						$(this).uniform();
					}
				});
			}
            var test = $("#purchase_info_add input[name='is_zhanyong']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function (){
                    if ($(this).parents(".checker").size() == 0){
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
			$('#purchase_info_add select[name="t_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});
			
			$('#purchase_info_add select[name="put_in_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});

            $('#purchase_info_add select[name="channel_id[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            
            }).change(function(e){
                $(this).valid();
            });
		};
		var handleForm = function(){
			$('#purchase_info_add').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					t_id: {
						required: true
					},
					apply_uname: {
						required: true
					},
					put_in_type: {
						required: true
					},
					purchase_fee:{
						required: true,
						//number:true
					}
				},
				messages: {
					t_id: {
						required: "亲~ 采购分类必选."
					},
					apply_uname: {
						required: "亲~ 申请人不能为空."
					},

					put_in_type:{
						required: "亲~ 采购方式必选."
					},
					purchase_fee:{
						required: "亲~ 采购申请费用不能为空.",
						//number: "采购申请费用只能为数字"
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
					$("#purchase_info_add").ajaxSubmit(opt);
				}
			});
			var url = purchase_info_id ? 'index.php?mod=purchase&con=PurchaseInfo&act=update' : 'index.php?mod=purchase&con=PurchaseInfo&act=insert';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(purchase_info_id ? "修改成功!": "添加成功!");
						
						if (data._cls)
						{
							util.retrieveReload();
							util.syncTab(data.tab_id);
						}
						else
						{//刷新首页
							purchase_info_search_page(util.getItem("orl"));
							//util.page('index.php?mod=purchase&con=purchaseinfo&act=search');
						}
								
					
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						util.xalert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					util.xalert("数据加载失败");  
				}
			}

			//回车提交
			$('#purchase_info_add input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#purchase_info_add').validate().form()) {
						$('#purchase_info_add').submit();
					}
					else
					{
						return false;
					}
				}
			});
		
		};

		var initData = function(){

			$('#purchase_info_add :reset').on('click',function(){
				$('#purchase_info_add select[name="t_id"]').select2('val',t_id);
			});
			$('#purchase_info_add :reset').on('click',function(){
				$('#purchase_info_add select[name="put_in_type"]').select2('val',put_in_type);
			});
            $('#purchase_info_add :reset').on('click',function(){
                $('#purchase_info_add select[name="channel_id[]"]').select2('val',[]);
            });
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			
			}
		
		}
	}();


	obj.init();
});