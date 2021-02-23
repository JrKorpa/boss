$import(function(){
	var id = '<%$view->get_id()%>';
	var obj = function(){
			var initElements = function()
			{
				$('#order_address_info select[name="country_id"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});
				$('#order_address_info select[name="province_id"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});
				$('#order_address_info select[name="city_id"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});
				$('#order_address_info select[name="regional_id"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});
				$('#order_address_info select[name="distribution_type"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
				});
			}
			var initData = function(){}
			var handleForm = function(){
					var url = id ? 'index.php?mod=sales&con=OrderAddress&act=update' : 'index.php?mod=sales&con=OrderAddress&act=insert';
			
					
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
								util.retrieveReload();
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
		
					$('#order_address_info').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							consignee: {
								required: true,
								
							},
							tel: {
								required: true,
								isMobile:true

							},
							email:{
								email:true
							},
							zipcode:{
								digits:true
							},
							distribution_type:{
								required:true
							},
							country_id:{
								required:true
							},
							province_id:{
								required:true
							},							
							city_id:{
								required:true
							},
							regional_id :{
								required:true
							},
							address:{
								required:true
							},
						},
						messages: {
							consignee: {
								required: "收货人不能为空."
							},
							tel:{
								required:"电话号码不能为空",
								isMobile:"格式不正确",

							},
							email:{
								email:"email格式不正确",
							},
							zipcode:{
								digits:"只能输入数字",
							},
							distribution_type: {
								required: "请选择方式配送.",
								
							},
							country_id: {
								required: "请选择国家.",
								
							},

							province_id: {
								required: "请选择省份.",
								
							},
							city_id: {
								required: "请选择城市.",
							},
							regional_id: {
								required: "请选择区域.",	
							},							
							address: {
								required: "请填写详细地址.",
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
							$("#order_address_info").ajaxSubmit(options1);
						}
					});
					//回车提交
					$('#order_address_info input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#order_address_info').validate().form()) {
								$('#order_address_info').submit();
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