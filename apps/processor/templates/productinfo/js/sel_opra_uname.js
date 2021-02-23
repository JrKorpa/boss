$import('public/js/select2/select2.min.js',function(){
	var buchan_id= '<%$id%>';
	var opra_uname;
	var obj = function(){
	
		var initElements = function(){
		
			$('#sel_opra_uname select[name="opra_uname"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});
			
		};
		var handleForm = function(){
				$('#sel_opra_uname').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					opra_uname: {
						required: true
					}
				},
				messages: {
					opra_uname: {
						required: "跟单人为必填项."
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
					$("#sel_opra_uname").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductInfo&act=sel_opra_uname&c=sub';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						alert("分配成功!");
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

			//回车提交
			$('#sel_opra_uname input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#sel_opra_uname').validate().form()) {
						$('#sel_opra_uname').submit();
					}
					else
					{
						return false;
					}
				}
			});	
		};

		var initData = function(){
			$('#sel_opra_uname :reset').click(function(){
				$('#sel_opra_uname select[name="opra_uname"]').select2('val',opra_uname)})
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