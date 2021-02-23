$import("public/js/jquery-tags-input/jquery.tagsinput.min.js",function(){

	var Obj = function(){
		var handleTagsInput = function () {
			if (!jQuery().tagsInput) {
				return;
			}
			$('#tags_11').tagsInput({
				'height':'100px', //设置高度
				'width':'auto',  //设置宽度
				'interactive':true, //是否允许添加标签，false为阻止
				'defaultText':'单号', //默认文字
				'removeWithBackspace' : true, //是否允许使用退格键删除前面的标签，false为阻止
				//'onRemoveTag':delete_tag,//删除标签的回调
				'minChars' : 0, //每个标签的小最字符
				'maxChars' : 0 ,//每个标签的最大字符，如果不设置或者为0，就是无限大
				'placeholderColor' : '#666666' //设置defaultText的颜色
			});

		}

		var initElements=function(){}
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=warehouse&con=WholesaleSaleSettlement&act=settlement';
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					util.xalert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 0 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert(data.content)
						$('.tagsinput .tag').each(function(i,v){
							if(data.red.indexOf(i.toString())>=0){
								$('.tagsinput .tag').eq(i).addClass('tagreds').removeClass('tagreens','tagblues');
							}else if(data.greend.indexOf(i.toString())>=0){
								$('.tagsinput .tag').eq(i).addClass('tagreens').removeClass('tagreds','tagblues');
							}else if(data.blue.indexOf(i.toString())>=0){
								$('.tagsinput .tag').eq(i).addClass('tagblues').removeClass('tagreds','tagreds');
							}
						})
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						util.xalert(data.error ? data.error : (data ? data :'程序异常'));
						$('.tagsinput .tag').addClass('tagreds');
					}
				}
			};

			$('#wholesaleSaleSettlement_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					orderids:{

					}
				},
				messages: {
					orderids:{

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
					$("#wholesaleSaleSettlement_info").ajaxSubmit(options1);
				}
			});
		};
		var initData=function(){
			$('#wholesaleSaleSettlement_info button[type=reset]').click(function(){
				$('.tagsinput .tag').html('').remove();
			});
		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
				//handleTagsInput();//初始表单
			}
		}
	}();
	Obj.init();
});