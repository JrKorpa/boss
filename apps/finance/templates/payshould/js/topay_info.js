$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	var obj = function(){
	
		var initElements = function(){
			//日期
			var dateobj = new Date();
			var month = dateobj.getMonth()+1;
			var mindata = dateobj.getFullYear()+'-'+month+'-'+dateobj.getDate();
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true,
					endDate:mindata,
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		};
		var handleForm = function(){
			$('#topay_add').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					bank_water: {
						required: true,
						checkField:true
					},
					payment_amount: {
						required: true,
						number:true
					},
					payment_time: {
						required: true
					}
				},
				messages: {
					bank_water: {
						required: "银行交易流水号必填",
						checkField: "银行交易流水格式不正确"
					},
					payment_amount: {
						required: "付款金额必填",
						number:"付款金额不合法"
					},
					payment_time: {
						required: "付款时间必填"
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
					$("#topay_add").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=finance&con=PayShould&act=pay_form';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert("付款成功");
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");  
				}
			}
		};

		var initData = function(){};
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