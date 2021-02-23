$import("public/js/select2/select2.min.js",function(){

	var info_form_id = 'roleuser_info';//form表单id
	var info_form_base_url = 'index.php?mod=management&con=RoleUser&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';

    var Obj = function(){

        var initElements = function(){
			$('#'+info_form_id+' select[name="user_arr[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            });

        }
        var handleForm = function(){
            //表单验证和提交
			var url = info_form_base_url+(info_id ? 'update' : 'insert');
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
							role_user_search_page(util.getItem("orl"));

							}
						);
					}
                }
            };

            $('#roleuser_info').validate({
                errorElement: 'span', //default input error role container
                errorClass: 'help-block', // default input error role class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					'user_arr[]':{
						required:true
					}
                },
                messages: {
					'user_arr[]':{
						required:'请选择用户'
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
                    $("#roleuser_info").ajaxSubmit(options1);
                }
            });

            //回车提交
            $('#roleuser_info input').keypress(function (e) {
                if (e.which == 13) {
					$('#roleuser_info').validate().submit();
                }
            });
        };
        var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select[name="user_arr[]"]').val([]).change();
			})			
		};
        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();

    Obj.init();


});
