function cancelInfo(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定取消此单据?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}
function exportData()
{
	location.href='index.php?mod=purchase&con=PurchaseReceipt&act=exportData&id=<%$view->get_id()%>'
}

function printDetail()
{
	window.open('index.php?mod=purchase&con=PurchaseReceipt&act=printDetail&id=<%$view->get_id()%>');	
}

$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",],function(){
	var info_id= '<%$view->get_id()%>';

	var obj = function(){
		var initElements = function(){
			$('#purchase_receipt_info_edit select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			
			}).change(function(e){
				$(this).valid();
			});	
		};
		//表单验证和提交
		var handleForm = function(){
			var url = 'index.php?mod=purchase&con=PurchaseReceipt&act=update';
			var options1 = {
				url: url,
				error:function ()
				{
					bootbox.alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert("修改成功!");
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
			};

			$('#purchase_receipt_info_edit').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					ship_num: {
						required: true
					},
					prc_id: {
						required: true
					}
				},
				messages: {
					ship_num: {
						required: "出货单号不能为空.",
					},
					prc_id: {
						required: "供应商不能为空.",
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
					$("#purchase_receipt_info_edit").ajaxSubmit(options1);
				}
			});
		};
		var initData = function(){};

		var from_table = function(){
			$.ajax({
				//url:"public/json/load.json",
				url:"index.php?mod=purchase&con=PurchaseReceipt&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':info_id},
				success:function(res) {

					from_table_data(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值
			$("body").find("#from_table_data_btn").click(function() {
				if ($("#from_table_data").find("td").hasClass("htInvalid") == true) {
					$("#from_table_data").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}else{
					$("#from_table_data").prev("p").addClass('text-success').text("");
					//console.log($("#from_table_data").handsontable('getData'));
					//取得供应商、出货单号信息
					var ship_num = $("#ship_num").val();
					var prc_id   = $("#prc_id").val();
					var remark   = $("#remark").val();
					var save = {'data':$("#from_table_data").handsontable('getData')};
					$.ajax({
					    url:"index.php?mod=purchase&con=PurchaseReceipt&act=getJson&id="+info_id+"&ship_num="+ship_num+"&prc_id="+prc_id+"&remark="+remark,
					    data:save,
					    dataType:"json",
					    type:"POST",
					    success:function(res) {
					      if (res.success == true) {
							  bootbox.alert("保存成功");
							  util.retrieveReload();
							//$('#purchase_receipt_info_edit').submit();
					      } else {
					        bootbox.alert(res.error);
					      }
					    }
					});
				}
			});
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
				from_table();
			}
		}
	}();
	obj.init();
});

