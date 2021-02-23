$import(function(){
	var id = '<%$view->get_id()%>';
	var obj = function(){
			var initElements = function(){}
			var initData = function(){}
			var handleForm = function(){
					var url = id ? 'index.php?mod=purchase&con=PurchaseInfo&act=update' : 'index.php?mod=purchase&con=PurchaseInfo&act=insert';
			
					
					var options1 = {
						url: url,
						error:function ()
						{
							alert('请求超时，请检查链接');
						},
						beforeSubmit:function(frm,jq,op){
							$('body').modalmanager('loading');//进度条和遮罩
						},
						success: function(data) {
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								util.xalert(id ? "修改成功!": "添加成功!");
								if (id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									purchase_type_search_page(util.getItem("orl"));
									util.page('index.php?mod=purchase&con=PurchaseInfo&act=search');
								}
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								util.xalert(data.error ? data.error : (data ? data :'程序异常'));
							}
						}, 
						error:function(){
							$('.modal-scrollable').trigger('click');
							util.xalert("数据加载失败");  
						}
					};
		
					
					//回车提交
					$('#purchase_info input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#purchase_info').validate().form()) {
								$('#purchase_info').submit();
							}
							else
							{
								return false;
							}
						}
					});
				
				}
		
		     ////
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
			////
		
	}();
	obj.init();
				 
});