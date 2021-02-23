$import(function(){
	var info_form_id = 'app_base_order_goods_info';//form表单id
	var info_form_base_url = 'index.php?mod=sales&con=BaseOrderInfo&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';//记录主键

	var obj = function(){
		var initElements = function(){
			$('#app_base_order_goods_info input[name="save_diamond_goods"]').on('click',function(){
						var goods_id = $('#app_base_order_goods_info input[name="goods_id"]').val();
						var goods_type = $('#app_base_order_goods_info input[name="goods_type"]').val();

						var id = $('#app_base_order_goods_info input[name="_id"]').val();
						var  data ={goods_id:goods_id,id:id,goods_type:goods_type};
						
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=BaseOrderInfo&act=saveOrderGoods&',
							data: data,
							dataType: 'json',
							async: false,
							success: function (res) {
								
								if(res.error >0){
									 alert(res.error);
								}else{
									 alert(res.error);
								}
								
								},
							error:function(res){
								alert('Ajax出错!');
							}
							});

					});

			$('#see_Dia_goods').on('click',function(){
						var goods_sn = $('#app_base_order_goods_info input[name="goods_id"]').val();
						var  data ={goods_sn:goods_sn};
						
						$.ajax({
							type: 'POST',
							url: 'index.php?mod=sales&con=BaseOrderInfo&act=seeDiaGoods&',
							data: data,
							dataType: 'json',
							async: false,
							success: function (res) {
								
								if(res.error >0){
									 alert(res.content);return false;
								}else{
									
								$('#t').html(res.content);
								}
								
							},
							error:function(res){
								alert('Ajax出错!');
							}
							});

					});

			$('#see_style_goods').on('click',function(){
						var goods_sn = $('#app_base_order_goods_info input[name="sale_goods_id"]').val();
						var id = $('#app_base_order_goods_info input[name="_id"]').val();
						var department = $('#app_base_order_user_info select[name="department_id"]').val();
						if(department==''){
							alert('请选择部门');
						}else{
							var data ={goods_sn:goods_sn,id:id,department:department};
						
							$.ajax({
								type: 'POST',
								url: 'index.php?mod=sales&con=BaseOrderInfo&act=getSaleGoods',
								data: data,
								dataType: 'json',
								async: false,
								success: function (res) {
									
									if(res.error >0){
										 alert(res.content);return false;
									}else{
										$('#order_detail_style_info').html("");
										$('#order_detail_style_info').html(res.content);
										
									}
									
								},
								error:function(res){
									alert('Ajax出错!');
								}
								});
						
						}
					});
	
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
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					if(data.success == 1 )
					{
						util.xalert(
							info_id ? "修改成功!": "添加成功!",
							function(){
								util.retrieveReload();
								if (data.tab_id)
								{
									util.syncTab(data.tab_id);
								}
							}
						);
 
					}
					else
					{
						util.error(data);
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
					$('#'+info_form_id).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+info_form_id).validate().form()
				}
			});
		};
		var initData = function(){
		
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

function app_order_detail_close_button(tabid){
	$('#app_base_order_goods_info').parent().parent().prev().find('.close').trigger('click');
	util._sync(util.getItem("url",'index.php?mod=sales&con=BaseOrderInfo&act=show&id='+tabid),$('#baseorderinfo-'+tabid).find('.flip-scroll')[0],false);
}