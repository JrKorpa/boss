var id = "<%if isset($data.id)%><%$data.id%><%else%><%/if%>";
var form_id = id?"goodsprice_salepolicy_edit":"goodsprice_salepolicy_add";
$import("public/js/select2/select2.min.js", function() {
	    var Obj = function() {
        var initElements = function() {
            $('#'+form_id+' select').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });			
			$('#'+form_id+' :reset').on('click',function(){
				$('#'+form_id+' select[name="channel_id"]').select2("val","");
			})
        };

        //表单验证和提交
   var handleForm = function() {
	   
         var url = 'index.php?mod=style&con=GoodsPriceSalepolicy&act=';
         var options1 = {
                url: url+(id?"update":"insert"),
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
					$('#'+form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							"保存成功!",
							function(){
								util.retrieveReload();//刷新查看页签
								util.syncTab(data.tab_id);								
							}
						);
					}
					else	
					{
						util.error(data);//错误处理
					}
					
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
        };

		$('#'+form_id).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    price: {
                        required: true
                    },
                },
                messages: {
                    price: {
                        required: "定价不能为0."
                    },
                },
                highlight: function(element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#"+form_id).ajaxSubmit(options1);
                }
        });
   }
	var initData = function() {

	};
	return {
		init: function() {
			initElements();//处理表单元素
			handleForm();//处理表单验证和提交
			initData();//处理表单重置和其他特殊情况
		}
	}
}();
Obj.init();	
});