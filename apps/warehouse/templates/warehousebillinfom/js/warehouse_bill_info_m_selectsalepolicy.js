$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/jquery.validate.extends.js"],function(){
//打印条码


        var bill_id ='<%$bill_id%>';
        var goods_id="<%$goods_id%>";
         var policy_goods="<%$policy_goods%>";
    

	var baseMemberInfoInfogObj = function(){
	
    	var initElements = function(){
    	$('#warehouse_bill_info_m_selectsalepolicy select[name="policy_type"]').select2({
				placeholder: "请选择",
				allowClear: true,

			}).change(function(e){
				$(this).valid();
                var url ="index.php?mod=warehouse&con=WarehouseBillInfoM&act=getPolicytypeInfo"
                var policy_type=$(this).val();
                $.post(url,{policy_type:policy_type,goods_id:goods_id,policy_goods:policy_goods}, function (data) {
                    if(data.success==1){
                        $('#warehouse_bill_info_m_selectsalepolicy input[name=sta_value]').val(data.msg1);
                       $('#warehouse_bill_info_m_selectsalepolicy input[name=jiajialv]').val(data.msg);
                       }
                       else{
                         util.xalert(data.error);
                       }
                });
			});//validator与select2冲突的解决方案是加change事件
			
           
    	};
			if (!jQuery().uniform) {
				return;
			}
            var dateobj = new Date();
            var month  =dateobj.getMonth()+1;
            var mindata = dateobj.getFullYear()+'-'+month+'-'+dateobj.getDate();

			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
                    startDate: mindata,

				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
            
		var handleForm = function(){
			//表单验证和提交
			var url = 'index.php?mod=warehouse&con=WarehouseBill&act=printcode';
			
        

			$('#warehouse_bill_info_m_selectsalepolicy').validate({
				errorElement: 'span', //default input error base_salepolicy_info container
				errorClass: 'help-block', // default input error base_salepolicy_info class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
                    
                    policy_name: {
                        required: true,
                        maxlength:60,
                    },
                    sta_value: {
                        required: true,
                        number:true
                    },
                    jiajialv: {
                        required: true,
                        number:true
                    },
				},
				messages: {
                    policy_name: {
                        required: "销售策略名称最长是60."
                    },
                    sta_value: {
                        required: "固定值不能为空！",

                    },
                    jiajialv: {
                        required: "加价率不能为空！",
                    },
				},
                highlight: function(element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
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
                    var policy_type= $('#warehouse_bill_info_m_selectsalepolicy select[name="policy_type"]').val();
                    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=printcode&bill_id="+bill_id+"&policy_type="+policy_type;
                }
			});
			//回车提交
			$('#warehouse_bill_info_m_selectsalepolicy input').keypress(function (e) {
				if (e.which == 13) {
					if ($('#warehouse_bill_info_m_selectsalepolicy').validate().form()) {
						$('#warehouse_bill_info_m_selectsalepolicy').submit();
					}
					else
					{
						return false;
					}
				}
			});
		}
		var initData = function(){}
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	baseMemberInfoInfogObj.init();
});

