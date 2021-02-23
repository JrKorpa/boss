$import('public/js/select2/select2.min.js',function(){

	var obj = function(){
		var handleForm = function(){
				$('#product_info_add_img').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
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
					$("#product_info_add_img").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductInfo&act=InsertImg';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert("上传成功!");
						//util.refresh("productinfo-"+buchan_id,data.title,'index.php?mod=processor&con=ProductInfo&act=show&id='+buchan_id);
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					bootbox.alert("数据加载失败");  
				}
			}
		};

		var initData = function(){
			
		};
		return {
			init:function(){
			
				handleForm();
				initData();
			}
		
		}
	}();


	obj.init();
});