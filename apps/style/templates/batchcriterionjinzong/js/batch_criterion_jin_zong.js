$import(function(){

	//匿名函数+闭包
	var obj = function(){
		//表单验证和提交
		var handleForm = function(){
            var info_form_id = 'batch_criterion_jin_zong';
			var url = 'index.php?mod=style&con=BatchCriterionJinZong&act=uploadJinZongFile';
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
					$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
					if(data.success == 0){

                        util.xalert(data.error);
                    }else{

                        util.xalert(data.error);
                    }
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				submitHandler: function (form) {
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
		};

        var initData = function(){
            $(".xzmb_bz").on('click', function(){
                location.href = "index.php?mod=style&con=BatchCriterionJinZong&act=download_demo";
            });
        }

		return {
			init:function(){
				handleForm();//处理表单验证和提交
                initData();
			}
		}	
	}();
	obj.init();
});



