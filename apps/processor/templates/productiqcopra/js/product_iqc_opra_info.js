$import('public/js/select2/select2.min.js',function(){
	var buchan_id= '<%$id%>';
	var obj = function(){
	
		var initElements = function(){
		};
		var handleForm = function(){
				$('#product_shipment').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					sj_num: {
						required: true,
						digits:true
					},
					bf_num: {
						required: true,
						digits:true
					},
					iqc_num: {
						required: true,
						digits:true
					}
				},
				messages: {
					sj_num: {
						required: "实际交货数量不能为空.",
						digits:"必须填入数字"
					},
					bf_num: {
						required: "报废数量不能为空.",
						digits:"必须填入数字"
					},
					iqc_num: {
						required: "IQC未过数量不能为空.",
						digits:"必须填入数字"
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
					$("#product_shipment").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductIqcOpra&act=insert';
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

			//回车提交
			$('#product_shipment input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#product_shipment').validate().form()) {
						$('#product_shipment').submit();
					}
					else
					{
						return false;
					}
				}
			});	
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