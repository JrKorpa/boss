function change_pro()
{

	var v = $("#oqc_no_type").val();
	if (v == 0)
	{
		$("#oqc_no_reason").html("<option value='0'>请选择</option>");
		return false;
	}
	$.post("index.php?mod=processor&con=ProductOqcOpra&act=get_protype",{id:v},function(data){
		//$("#oqc_no_reason").html(data);
		$('#product_shipment select[name="oqc_no_reason"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
		$('#product_shipment select[name="oqc_no_reason"]').change();
	})

}
$import('public/js/select2/select2.min.js',function(){
	var buchan_id= '<%$id%>';
	var obj = function(){
	
		var initElements = function(){
			$('#product_shipment .radio-list').delegate('input' ,'click' , function(){
				if($(this).val()==1){
					$("#oqc_div").css('display','none');
					$('#product_shipment input[name="num"]').attr('disabled', false).val('');	
                    $('#product_shipment input[name="oqc_no_num"]').val('');
					$('#product_shipment select[name="oqc_no_type"]').select2('val','');
					$('#product_shipment select[name="oqc_no_reason"]').select2('val','');		
				}else{
					$("#oqc_div").css('display','block');
					$('#product_shipment input[name="num"]').attr('disabled', 'disabled').val('');
					
				}
			});
			
						//初始化下拉组件
			$('#product_shipment select[name="oqc_no_type"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			$('#product_shipment select[name="oqc_no_reason"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});
		};
		var handleForm = function(){
				$('#product_shipment').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					shipment_number: {
						required: true,
						checkField:true
					},
					/*
					num: {
						required: true,
						digits:true
					},
					bf_num: {
						required: true,
						digits:true
					}
                   */
				},
				messages: {
					shipment_number: {
						required: "工厂出货单号不能为空.",
						checkField: "只能输入数字、字母和下划线"
					},
					/*
					num: {
						required: "出货数量不能为空.",
						digits:"必须填入数字"
					},
					bf_num: {
						required: "报废数量不能为空.",
						digits:"必须填入数字"
					}
					*/
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
			var url = 'index.php?mod=processor&con=ProductShipment&act=insert';
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