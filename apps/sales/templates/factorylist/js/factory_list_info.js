$import(function(){
	var id = '<%$view->get_id()%>';
	var obj = function(){
			var initElements = function(){}
			var initData = function(){}
			var handleForm = function(){
					var url = id ? 'index.php?mod=sales&con=AppOrderDetails&act=update' : 'index.php?mod=sales&con=AppOrderDetails&act=insert';
			
					
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
								alert(id ? "修改成功!": "添加成功!");
								util.retrieveReload(obj);
								if (id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页

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
		
					$('#order_goods_info').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							goods_sn: {
								required: true
							},
							goods_name: {
								required: true,
								
							},
							goods_price:{
								required:true,
								number:true
								}

						},
						messages: {
							goods_sn: {
								required: "款号不能为空."
							},
							goods_name:{
								required:"商品名称不能为空"
							},
							goods_price: {
								required: "价格不能为空.",
								number:"请出入正确的格式"
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
							$("#order_goods_info").ajaxSubmit(options1);
						}
					});
					//回车提交
					$('#order_goods_info input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#order_goods_info').validate().form()) {
								$('#order_goods_info').submit();
							}
							else
							{
								return false;
							}
						}
					});
				
				}
		
		     ////
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
			////
		
	}();
	obj.init();
				 
});