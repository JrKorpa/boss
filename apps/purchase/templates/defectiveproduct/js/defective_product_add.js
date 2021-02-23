$import('public/js/select2/select2.min.js',function(){
	var obj = function(){

		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			$('#defective_product_add select').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e){
					$(this).valid();
			});
		
		};
		var handleForm = function(){
				$('#defective_product_add').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					ship_num: {
						required: true,
						maxlength:25
					},
					note: {
						required: true,
						maxlength:100
					}
				},
				messages: {
					ship_num: {
						required: "出货单号不能为空.",
						maxlength:"输入的最大长度是25个字符"
					},
					note: {
						required: "请输入返修原因",
						maxlength: "长度不能超过100"
					}
				},
				highlight: function (element) { // hightlight error inputs
					$(element).closest('.form-group').addClass('has-error'); // set error class to the control group
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
					$("#defective_product_add").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=purchase&con=DefectiveProduct&act=batinsert';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert("操作成功!");
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");  
				}
			}

			
		};

		var initData = function(){
			$('#defective_product_add :reset').on('click',function(){
				$('#defective_product_add select[name="prc_id"]').select2("val",'');
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