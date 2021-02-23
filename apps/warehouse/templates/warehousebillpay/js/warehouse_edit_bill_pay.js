$import(function(){
	var id = '<%$view.id%>';
        var max = '<%$view_bill->get_goods_total()%>';
        var total = parseInt(max.substring(0,max.indexOf(".")));
        
	var obj = function(){
			var initElements = function(){
				
				$('#warehouse_bill_pay1 select').select2({
					placeholder: "请选择",
					allowClear: true
				
				}).change(function(e){
					$(this).valid();
				});	
			}
			var initData = function(){}
			var handleForm = function(){
				var url = id ? 'index.php?mod=warehouse&con=WarehouseBillPay&act=updateBillPay' : 'index.php?mod=warehouse&con=WarehouseBillPay&act=insertBillPay';
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
							//util.retrieveReload();
							$('#warehouse_bill_pay').html(data.content);
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

				$('#warehouse_bill_pay1').validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					rules: {
						pro_id: {
							required: true
						},
						pay_content: {
							required: true
						},
						pay_method: {
							required: true
						},
						tax: {
							required: true
						},
						amount: {
							required: true,
							number:true,
							//min:0,  //提示错误屏了  
                           // max:total,
						}
					},
					messages: {
						pro_id: {
							required: "结算商必选."
						},
						pay_content: {
							required: "支付内容必选."
						},
						pay_method: {
							required: "结算方式必选."
						},
						tax: {
							required: "含税金必选."
						},
						amount: {
							required: "金额不能为空.",
							number:"金额只能输入数字",
							//min:"输入的金额大于0",  //同上 BY liyanhong
                            //max:"不能超过成本总计"
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
						$("#warehouse_bill_pay1").ajaxSubmit(options1);
					}
				});
			};
		var initData = function(){
			//下拉组件重置
			$('#warehouse_bill_pay1 :reset').on('click',function(){
				$('#warehouse_bill_pay1 select[name="pro_id"],#warehouse_bill_pay1 select[name="pay_content"],#warehouse_bill_pay1 select[name="pay_method"],#warehouse_bill_pay1 select[name="tax"]').select2("val",'').change();
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
	obj.init();
				 
});