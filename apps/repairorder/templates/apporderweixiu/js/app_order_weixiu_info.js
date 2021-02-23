function submit() {
    $("#app_order_weixiu_info").submit();
}
function AcceptProduct(){
    change_status(6);//收货status：6
}
function finish(){
    change_status(5);
}
function make_order() {
    change_status(4);
}
function loading(){
    change_status(3);
}
function cancel() {
    change_status(7);
}
function confirms() {
    change_status(2);
}
function remark() {
    add_log(1);
}
function fqc_not_pass() {	
	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确定',  
				className: 'btn-success'  
			},  
			cancel: {  
				label: '取消',  
				className: 'btn-default'  
			}  
		},  
		message: '<form class="form-horizontal" role="form"><div class="form-group">\
						<label class="col-sm-3 control-label no-padding-right">\
						质检不通过原因：<span class="required">*</span><br/></label>\
						<div class="col-sm-9">\
							<textarea class="form-control" id="info" name="info" placeholder="原因"></textarea>\
						</div>\
				 </div></form>',
		callback: function(result) {
			if(result) {
				var info = $('#info').val();
				if(info =='' ){
					util.xalert("原因必填。");
					return false;
				}
				$('#remark_log').val('质检不通过原因：' + info);
				add_log(2);
			};
			
		}, 
		title: "质检未通过",  
	});
    //
}
function change_status(status)
{
	var id= '<%$view->get_id()%>';
	var remark_log = $('#remark_log').val();
	//alert(remark_log);
	$.post('?mod=repairorder&con=AppOrderWeixiu&act=change_status',{id:id,status:status,remark_log:remark_log},function(res){
		if(res.success)
		{
			util.xalert("操作成功",function(){
				util.retrieveReload();//页面刷新
			});
		}
		else
		{
			util.error(res);
		}
	});
}
function add_log(status)
{
	var id= '<%$view->get_id()%>';
	var remark_log = $('#remark_log').val();
	//alert(remark_log);
	$.post('?mod=repairorder&con=AppOrderWeixiu&act=add_log',{id:id,status:status,remark_log:remark_log},function(res){
		if(res.success)
		{
			//$("#qc_status").val("2");
			util.xalert("操作成功",function(){
				util.retrieveReload();//页面刷新
			});
		}
		else
		{
			util.error(res);
		}
	});
}
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('edit_url','index.php?mod=repairorder&con=AppOrderWeixiu&act=edit');

	util.setItem('orl','index.php?mod=repairorder&con=AppOrderWeixiu&act=logList&id='+'<%$view->get_id()%>');//设定刷新的初始url
	//util.setItem('formID','app_order_weixiu_search_form');//设定搜索表单id
	util.setItem('listDIV','app_order_weixiu_log_list');//设定列表数据容器id



	var id= '<%$view->get_id()%>';
	var AppOrderWeixiuInfoObj = function(){

		var initElements=function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			var test = $("#app_order_weixiu_info input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			//初始化组件
			$('#app_order_weixiu_info select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
                if(e.currentTarget.id == 're_type'){
                    if(e.val == 3 || e.val == 5){
                        $('#app_order_weixiu_info input[name="rec_id"]').val('').attr('readonly',true);
                        $('#app_order_weixiu_info input[name="order_sn"]').val('').attr('readonly',true);
                        $('#app_order_weixiu_info input[name="consignee"]').removeAttr('readonly');
                    }else{
                        $('#app_order_weixiu_info input[name="rec_id"]').removeAttr('readonly');
                        if($('#app_order_weixiu_info input[name="rec_id"]').val() == '')
                            $('#app_order_weixiu_info input[name="order_sn"]').removeAttr('readonly');
                    }
                    if(e.val == 5){
                        //$('#app_order_weixiu_info input[name="change_sn"]').val('').attr('readonly',true);
                    }else{
                        $('#app_order_weixiu_info input[name="change_sn"]').removeAttr('readonly');
                    }
                }
			});

			$('#app_order_weixiu_info input[name="order_sn"]').on('blur',function(e){
                if($(e.currentTarget).attr('readonly'))
                    return;
				$('#app_order_weixiu_info input[name="consignee"]').val('');
                $('#app_order_weixiu_info select[name="channel_class"]').val('');
				//$('#app_order_weixiu_info input[name="rec_id"]').val('');
				var order_sn = $(this).val();
				if(order_sn == ''){
                    $('#app_order_weixiu_info input[name="consignee"]').removeAttr('readonly');
                    $('#app_order_weixiu_info select[name="channel_class"]').removeAttr('readonly');
                    $('#s2id_channel_class').find('span.select2-chosen').html('请选择');
					return;
				}else{
                    $('#app_order_weixiu_info input[name="consignee"]').attr('readonly',true);
                    $('#app_order_weixiu_info select[name="channel_class"]').attr('readonly',true);
                }

                var channel_name = '';
				$.ajax({
					type:'POST',
					url:'index.php?mod=repairorder&con=AppOrderWeixiu&act=getConsignee',
					data:{'order_sn':order_sn},
					dtatType:'text',
					success:function(data){
                        //console.log(data);return;
						$('#app_order_weixiu_info input[name="consignee"]').val(data.consignee);
                        $('#channel_class option').each(function(){
                            if($(this).val() == data.channel_class){
                                if($(this).val() == 1){
                                    channel_name = '线上';
                                }
                                if($(this).val() == 2){
                                    channel_name = '线下';
                                }
                                $(this).attr("selected","selected");
                                $('#s2id_channel_class').find('span.select2-chosen').html(channel_name);
                            }
                        });
						//$('#app_order_weixiu_info input[name="rec_id"]').val(data.bc_sn);
					}
				});
			});

			$('#app_order_weixiu_info input[name="rec_id"]').on('blur',function(e){
                if($(e.currentTarget).attr('readonly'))
                    return;
				//$('#app_order_weixiu_info input[name="consignee"]').val('');
				//$('#app_order_weixiu_info input[name="order_sn"]').val('');
				var bc_sn = $("#app_order_weixiu_info input[name='rec_id']").val();
				//alert(bc_sn);
				if(bc_sn == ''){
                    $('#app_order_weixiu_info input[name="consignee"]').removeAttr('readonly');
                    $('#app_order_weixiu_info input[name="order_sn"]').removeAttr('readonly');
                    //$('#app_order_weixiu_info input[name="consignee"]').val('');
                    //$('#app_order_weixiu_info input[name="order_sn"]').val('');
					return false;
				}else{
                    $('#app_order_weixiu_info input[name="consignee"]').attr('readonly',true);
                    $('#app_order_weixiu_info input[name="order_sn"]').attr('readonly',true);
                }
				$.ajax({
					type:'POST',
					url:'index.php?mod=repairorder&con=AppOrderWeixiu&act=getConsignee',
					data:{'bc_sn':bc_sn},
					dtatType:'text',
					success:function(data){
						if(data != 0){
							$('#app_order_weixiu_info input[name="consignee"]').val(data.consignee);
							if(data.from_type == 2){
								$('#app_order_weixiu_info input[name="order_sn"]').val(data.p_sn);
							}
						}else{
							$('#app_order_weixiu_info input[name="consignee"]').val('');
							$('#app_order_weixiu_info input[name="order_sn"]').val('');
						}
					}
				});
			});

		}
		//表单验证和提交
		var handleForm = function(){
			var url = id ? 'index.php?mod=repairorder&con=AppOrderWeixiu&act=update' : 'index.php?mod=repairorder&con=AppOrderWeixiu&act=insert';
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout('app_order_weixiu_info');
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock('app_order_weixiu_info');
				},
				success: function(data) {
					$('#app_order_weixiu_info :submit').removeAttr('disabled');//解锁
					if(data.success == 1 ){//debugger;
						$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
						util.xalert(

							id ? "修改成功!": "添加成功!",
							function(){

								if (id)
								{//刷新当前页
									util.retrieveReload();
								}
								else
								{
									//添加后跳转编辑页面
									if (data.x_id && data.tab_id)
									{
										util.syncTab(data.tab_id);
										util.buildEditTab(data.x_id,util.getItem('edit_url'),data.tab_id,'维修编辑');
									}
								}
							}
						);
					}
					else
					{
						util.error(data);//错误处理
					}


				},
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");
				}
			};

			$('#app_order_weixiu_info').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					//至少选择一个维修原因、输入客户姓名、请选择维修类型、三个必选项
					//腾讯维修或库房维修 4 5 货号不能为空
					//新货维修或售后维修 必须输入布产号与订单号
					 repair_factory: {
					 	required: true,
					 },
					consignee:{
						required: true,
					},
					re_type:{
						required: true,
					}//,
					// end_time:{
					// 	required: true,
					// }
				},
				messages: {
					repair_factory: {
						required: "请选择工厂"
					},
					consignee:{
						required: "请输入客户姓名",
					},
					re_type:{
						required: "请选择维修类型",
					}//,
					// end_time:{
					// 	required: "请选择预计时间",
					// }
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
					//js验证
					//验证预计维修时间：
					//if (!$("#end_time").val()) {
						//alert("请选择预计时间");
						//return false;
					//};
					//维修原因必选
					var obj = document.getElementsByName("repair_act[]");
					var sel_flg = false;
					for (var i = 0; i< obj.length; i++)
					{
						if(obj[i].checked)
						{
							sel_flg = true;
							break;
						}
					}
					if (!sel_flg)
					{
						alert ("至少选择一个维修原因!!");
						return false;
					}
					var re_type = $("#re_type").val();
					var goods_id = $("#goods_id").val();
					if (re_type == 4 || re_type == 5)
					{
					   if (goods_id == '')
					   {
						alert ("请输入货号");
						return false;
					   }
					}
					else if (re_type == 1 || re_type == 2)
					{
						//新货维修和售后维修都需要填写订单号
						if ($('#app_order_weixiu_info input[name="order_sn"]').val() == "")
						{
							alert ("请输入订单号");
							return false;
						}
						/*if ($('#rec_id').val() == "")
						{
							alert ("请输入布产号");
							return false;
						}*/
					}
					//return false;
					$("#app_order_weixiu_info").ajaxSubmit(options1);
				}
			});

			$('#app_order_weixiu_info input').keypress(function (e) {
				if (e.which == 13) {
					$('#app_order_weixiu_info').validate().form();
				}
			});
		};
		var initData=function(){
			$('#app_order_weixiu_info :reset').on('click',function(){
				$('#app_order_weixiu_info select[name="repair_factory"],#app_order_weixiu_info select[name="re_type"]').select2("val",'').change();
			})

			util.closeForm(util.getItem("listDIV"));
			app_order_weixiu_log_page(util.getItem("orl"));

		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	AppOrderWeixiuInfoObj.init();
});

//分页
function app_order_weixiu_log_page(url){
	util.page(url);
}


function AppOrderWeixiuInfoObj(){
	$("#app_order_weixiu_info").trigger("submit");
}

$(function(){
	if($('#app_order_weixiu_info select[name="re_type"]').val() == 5){
		//$('#app_order_weixiu_info input[name="change_sn"]').val('').attr('readonly',true);
		//alert($('#app_order_weixiu_info select[name="re_type"]').val());
	    $('#app_order_weixiu_info input[name="change_sn"]').removeAttr('readonly'); 
	}else{
		$('#app_order_weixiu_info input[name="change_sn"]').removeAttr('readonly');
	}
});
