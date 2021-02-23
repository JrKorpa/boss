var info_form_id = 'batch_exchange_goods_search_form';//搜索form表单id
var add_form_id  = 'batch_exchange_goods_add_form';//提交换货form表单id
var info_form_base_url = 'index.php?mod=warehouse&con=BatchExchangeGoods&act=';//基本提交路径

$import(["public/js/select2/select2.min.js"],function(){
	var obj = function(){
		var initElements = function(){
			
		};	
		var handleForm = function(){
            //搜索options1
			var options1 = {
				url: info_form_base_url+"search",
				error:function ()
				{   
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					$('#'+info_form_id+' .search_logs').html('');
					return util.lock(info_form_id);
				},
				success: function(data) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					
					$("#batch_exchange_goods_search_list").html(data.content);
					$('#'+info_form_id+' .search_logs').html(data.search_logs);//日志显示
                    
					var scrollHeight = $('#'+info_form_id+' .search_logs')[0].scrollHeight;
					$('#'+info_form_id+' .search_logs')[0].scrollTop = scrollHeight;
				}
			};	
			//提交换货options2
            var options2 = {
				url: info_form_base_url+"exchangeGoods",
				error:function ()
				{   
					util.timeout(add_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(add_form_id);
				},
				success: function(data) {//搜索返回json结果	
				    $('#'+add_form_id+' :submit').removeAttr('disabled');
					$('.modal-scrollable').trigger('click');//关闭遮罩
					
				   if(data.success){							
						util.xalert("操作成功!");
					}else{
					    if(data.error){
							util.xalert(data.error);
						}else{
						   	util.xalert(data.toString());
						}
					}
				}
			};			

			$("#"+info_form_id).on("submit",function(){								   
			    $('#'+info_form_id).ajaxSubmit(options1);
				return false;
			});	
			
			$("#"+info_form_id+" textarea[name='order_sn']").on("keypress",function(event){
			    if(event.keyCode==13){				    
					var order_sn = $.trim($(this).val());					
					if(order_sn==""){
						$("#batch_exchange_goods_search_list").html("");
					    $('#'+info_form_id+' .search_logs').html("");//日志显示
						return false;	
					}else{
						$('#'+info_form_id).submit();
					    $(this).val(order_sn);	
					}
					
				}
			});	
			
			$("#"+add_form_id+" .new_goods_id").live("keypress",function(event){										 
				if(event.keyCode==13){
					if($.trim($(this).val())!=''){
						var tabIndex = parseInt($(this).attr("tabindex"))+1;					
						$("#"+add_form_id+" .new_goods_id[tabindex=" + tabIndex + "]").focus();
						$("#"+add_form_id+" .new_goods_id[tabindex=" + tabIndex + "]").parent().parent().click();
					}
					return false;
				}
				
			});
			
			//批量换货提交
			$("#"+add_form_id).on("submit",function(){							   
			    $('#'+add_form_id).ajaxSubmit(options2);
				return false;
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
