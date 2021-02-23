var formID = "peishi_list_show_form";
var p_id = '<%$id%>';
//匿名回调
$import('public/js/select2/select2.min.js',function(){
	var obj = function(){
		
		var initElements = function(){
			$('#'+formID+' select').select2({
				placeholder: "请选择",
				allowClear: true

		    }).change(function(e){
				$(this).valid();
		    });
			$('#'+formID+' .js_row').each(function(){
			       var id = $(this).attr('data-id');
				   if(id == p_id){
					    $(this).attr('class','js_row tab_hover tab_click');  
				   }
			})
			$('#'+formID+' .js_row').click(function(){
				var id = $(this).attr('data-id');
				if(id != p_id){
				   load_peishi_info(id);		
				}
													
	        });
			
			$("#"+formID+' select[name="peishi_status_all"]').change(function(){
			   $('#'+formID+' .js_peishi_status').select2('val',$(this).val()).change();																  
			});
			
			$("#"+formID+' select[name="peishi_status_all"]').change(function(){
			   $('#'+formID+' .js_peishi_status').select2('val',$(this).val()).change();																  
			});
			$("#"+formID+' .js_goods_id').focus();
			//条码框失去焦点
			$("#"+formID+' .js_goods_id').blur(function(){
				var goods_id = $.trim($(this).val());				
				var peishi_status_default = $(this).attr('peishi-status-default');
				var peishi_status_default2 = $(this).attr('peishi-status-default2');
				var peishi_status_edit = $(this).attr('peishi-status-edit');
				
				var peishiStatusObj = $(this).parent().parent().find('.js_peishi_status');
				if(peishi_status_edit!=''){
					peishi_status_edit = peishi_status_edit==0?true:false;
					if(goods_id!=''){
						if(peishi_status_default2!=''){
						   peishiStatusObj.select2('val',peishi_status_default2).attr('readonly',peishi_status_edit).change();
						}
					}else{
						if(peishi_status_default!=''){
						   peishiStatusObj.select2('val',peishi_status_default).attr('readonly',false).change();
						}
					}
				}
			});
			
			
		};
		    
			

		var handleForm = function(){
			
		};
		
		var initData = function(){
			 if(p_id>0){
				 load_peishi_info(p_id);
			 }
			 
             var options1 = {
                url: 'index.php?mod=processor&con=PeishiList&act=mutiPeishiUpdate',
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
					$('#'+formID+' :submit').removeAttr('disabled');//解锁
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

		$('#'+formID).validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {

                },
                messages: {

                },
                highlight: function(element) {
                    $(element)
                            .closest('.form-group').addClass('has-error');
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#"+formID).ajaxSubmit(options1);
                }
        });
			 
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

function load_peishi_info(id){
	if(!id){
	   util.xalert("配石ID为空，请重新点击行尝试！");	 
	   return false;	
	}
	$("#peishi_info_box").html('<h2 style="color:red"><b>正在获取流水号为'+id+'的配石信息...</b></h2>');
	$.ajax({
		 type: "POST",
		 url: "index.php?mod=processor&con=PeishiList&act=getPeishiInfo",
		 data: {id:id},
		 dataType: "text",
		 success: function(res){
			 $("#peishi_info_box").html(res);
			 p_id = id;
		 }
	});		
}