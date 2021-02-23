//匿名回调
$import(['public/js/select2/select2.min.js'], function(){

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){

			$('#warehouse_deal_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

            $('#warehouse_deal_form2 select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});

            $('#warehouse_deal_form_order select').select2({
                placeholder: "请选择",
                allowClear: true

            }).change(function(e){
                $(this).valid();
            });
			
			$('#warehouse_BillS select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});
					
                    $('#warehouse_deal_form2 select[name="company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
  				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WarehouseGoods&act=getTowarehouseId', {'id': _t}, function (data) {
						$('#warehouse_deal_form2 select[name="warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#warehouse_deal_form2 select[name="warehouse_id"]').change();
					});
				}else{
					$('#warehouse_deal_form2 select[name="warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});
                };
		//表单验证和提交
		var handleForm = function(){
			//处理更改状态的表单提交
			$('#warehouse_deal_form2').validate({
				submitHandler: function (form) {
				$("#warehouse_deal_form2").ajaxSubmit(opt2);
				}
			});
			var opt2 = {
				url: 'index.php?mod=warehouse&con=WarehouseDeal&act=modifyGoodsInfo',
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('修改成功~.~');
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
                        
			$('#warehouse_deal_form').validate({
				submitHandler: function (form) {
					$("#warehouse_deal_form").ajaxSubmit(opt);
				}
			});
			var opt = {
				url: 'index.php?mod=warehouse&con=WarehouseDeal&act=addMbill',
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('成功');
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

            $('#warehouse_deal_form_order').validate({
                submitHandler: function (form) {
                    $("#warehouse_deal_form_order").ajaxSubmit(opt_order);
                }
            });
            var opt_order = {
                url: 'index.php?mod=warehouse&con=WarehouseDeal&act=up_order_dvtype',
                beforeSubmit:function(frm,jq,op){
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if(data.success == 1 ){
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        bootbox.alert('更改成功');
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
			
			$('#warehouse_BillS').validate({
				submitHandler: function (form) {
					$("#warehouse_BillS").ajaxSubmit(opt4);
				}
			});
			var opt4 = {
				url: 'index.php?mod=warehouse&con=WarehouseDeal&act=addMbillS',
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						bootbox.alert('成功');
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
			
			
			$('#warehouse_deal_form3').validate({
				submitHandler: function (form) {
					("#warehouse_deal_form3").submit();
				}
			});
//           var opt3 = {
//				url: 'index.php?mod=warehouse&con=WarehouseDeal&act=batinsert',
//				beforeSubmit:function(frm,jq,op){
//					$('body').modalmanager('loading');//进度条和遮罩
//				},
//				success: function(data) {
//					if(data.success == 1 ){
//						$('.modal-scrollable').trigger('click');//关闭遮罩
//						bootbox.alert('成功');
//					}else{
//						$('body').modalmanager('removeLoading');//关闭进度条
//						bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
//					}
//				}, 
//				error:function(){
//					$('.modal-scrollable').trigger('click');
//					bootbox.alert("数据加载失败");  
//				}
//			}          
		
		};
		
		var initData = function(){
		}
		return {
			init:function(){
                                initElements();
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});