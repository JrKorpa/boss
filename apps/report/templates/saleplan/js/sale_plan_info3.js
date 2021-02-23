
$import(["public/js/select2/select2.min.js"] , function(){
	var info_form_id = 'purchase_qiban_goods_info';//form表单id
	var info_form_base_url = 'index.php?mod=report&con=SalePlan&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';
    var zhengshuhao_flag = false;
	/*function setGoodsAttr(sn){
		if($.trim(sn)==''){
			if(zhengshuhao_flag==true){
				$('#'+info_form_id+' input[name="specifi"]').val('0').attr('readonly',false);
			    $('#'+info_form_id+' select[name="cert"]').select2("val",'').attr('readonly',false);		
				//$('#'+info_form_id+' select[name="yanse"]').select2("val",'').attr('readonly',false);		
				//$('#'+info_form_id+' select[name="jingdu"]').select2("val",'').attr('readonly',false);
				zhengshuhao_flag = false;
			}else{
				$('#'+info_form_id+' input[name="specifi"]').attr('readonly',false);
			    $('#'+info_form_id+' select[name="cert"]').attr('readonly',false);
			}
			
			
		} else {
			$('body').modalmanager('loading');
			$.ajax({
					type: 'POST',
					url: 'index.php?mod=sales&con=AppOrderDetails&act=getDiamandInfoAjax',
					data: {'sn':sn},
					dataType: 'json',
					success: function (res) {
						 $('body').modalmanager('removeLoading');
						if(res.error >0){
							 zhengshuhao_flag=false;
							 setGoodsAttr('');
							 return false;
						}else{						
							zhengshuhao_flag=true;
							var goods = res.data;
							$("#"+info_form_id+" input[name='specifi']").val(goods.carat).attr('readonly',true);
							$("#"+info_form_id+" select[name='cert']").select2("val",goods.cert).attr('readonly',true).change();
							
						}				
					},
					error:function(res){
						alert('Ajax出错!');
					}
			});
		}
	}			 */
	var obj = function(){
		var initElements = function(){
			//下拉美化 需要引入"public/js/select2/select2.min.js"
			/*$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true,
//				minimumInputLength: 2
			}).change(function(e){
				$(this).valid();
			});
			
			var zhengshuhao = $("#"+info_form_id+" input[name='zhengshu']").val();
			 if(zhengshuhao!=''){
			    setGoodsAttr(zhengshuhao);
			 }
			 $("#"+info_form_id+" input[name='zhengshu']").blur(function (e) {
				if($(this).attr('readonly') !='readonly'){				 
                    setGoodsAttr($(this).val());
				}
            });*/
		};

		//表单验证和提交
		var handleForm = function(){
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
							data.error,
							function(){
								if (data._cls)
								{//查看编辑
									util.retrieveReload();//刷新查看页签
									util.syncTab(data.tab_id);//刷新数据主列表，无法定位到分页（有可能数据列表页签已经关闭，也有可能是其他对象穿透查看，所以分页函数不一定存在）
								}
								else
								{
									//关闭当前页
									//util.closeTab();
									purchase_qiban_goods_search_page(util.getItem("orl"));
								}
							}
						);
					}
					else
					{
						util.error(data);//错误处理
					}
				}
			};

			$('#'+info_form_id).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					
				},
				messages: {
					
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
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form();
				}
			});
		};
		var initData = function(){
			$('#'+info_form_id+' :reset').on('click',function(){
				//下拉置空
				$('#'+info_form_id+' select[name="xuqiu"]').select2('val','').change();
				$('#'+info_form_id+' select[name="jinliao"]').select2('val','').change();
				$('#'+info_form_id+' select[name="jinse"]').select2('val','').change();
				$('#'+info_form_id+' select[name="gongyi"]').select2('val','').change();
				$('#'+info_form_id+' select[name="gongchang_info"]').select2('val','').change();
			});
			if (!info_id) {
				$('#'+info_form_id+' input[name="kuanhao"]').on('blur', function(){
					var value = $(this).val().toUpperCase();
					if (!value) {
						util.error('请输入QIBAN或款号');
						return;
					} else if (value == 'QIBAN') {
						return;
					}
					$.ajax({
						type: 'GET',
						url: info_form_base_url + 'retrieveKuan',
						data: "style_sn="+value,
						dataType: 'json',
						success:function(resp) {
			               if (resp.error) {
			            	   util.error(resp.error);	 
			            	   $('#'+info_form_id+' select[name="gongchang_info"]').select2('val','').change();
			               } else {
			            	   if (resp.factory_id) $('#'+info_form_id+' select[name="gongchang_info"]').select2('val',resp.factory_id+'|'+resp.factory_name.name).change();
			               }
			            }
					});
				});
			} else {
				$('#'+info_form_id+' input[name="kuanhao"]').attr('readonly', 'readonly');
			}
			console.log("------------------------------------");
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});