function change_pro()
{

	var v = $("#oqc_reason").val();
	if (v == 0)
	{
		$("#oqc_problem").html("<option value='0'>请选择</option>");
		return false;
	}
	$.post("index.php?mod=processor&con=ProductOqcOpra&act=get_protype",{id:v},function(data){
		//$("#oqc_problem").html(data);
		$('#oqc_opra select[name="oqc_problem"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
		$('#oqc_opra select[name="oqc_problem"]').change();
	})

}

$import('public/js/select2/select2.min.js',function(){
	var buchan_id= '<%$id%>';
	var obj = function(){

		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}


			var test = $("#oqc_opra input[name='oqc_result']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

			$('#oqc_opra .radio-list').delegate('input' ,'click' , function(){
				if($(this).val()==1){
					$("#oqc_div").css('display','none');
                    $('#oqc_opra input[name="oqc_num"]').attr('disabled', false).val('');
                    $('#oqc_opra input[name="oqc_no_num"]').val('');
                    $('#oqc_opra select[name="oqc_reason"]').select2('val','');
                    $('#oqc_opra select[name="oqc_problem"]').select2('val','');
				}else{
					$("#oqc_div").css('display','block');
                    $('#oqc_opra input[name="oqc_num"]').attr('disabled', 'disabled').val('');
				}
			});

			//初始化下拉组件
			$('#oqc_opra select[name="oqc_reason"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			$('#oqc_opra select[name="oqc_problem"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});

		};
		var handleForm = function(){
				$('#oqc_opra').validate({
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
					$("#oqc_opra").ajaxSubmit(opt);
				}
			});
			var url = 'index.php?mod=processor&con=ProductOqcOpra&act=insert';
			var opt = {
				url: url,
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert(data.error);
						util.retrieveReload();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
					}
				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					bootbox.alert("数据加载失败");
				}
			}

			//回车提交
			$('#oqc_opra input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#oqc_opra').validate().form()) {
						$('#oqc_opra').submit();
					}
					else
					{
						return false;
					}
				}
			});
		};

		var initData = function(){
			$('#oqc_opra :reset').on('click',function(){
				$('#oqc_opra select').select2('val','');
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