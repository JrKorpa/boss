//分页
function ship_freight_search_page(url){
	util.page(url);
}

//匿名回调
$import(function(){
	util.setItem('listDIV','order_invoice_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){};
		
		var handleForm = function(){
			//sreach button
			$('#order_invoice_search_form button').click(function(){
				var _no = $.trim($('#order_invoice_search_form input[name="order_no"]').val());
                $('#order_invoice_search_form input[name="order_no"]').val(_no);
				if(_no == ''){
					$('#order_invoice_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
				}else{
					var url = 'index.php?mod=finance&con=OrderInvoice&act=orderSearch';
					var data = {'order_no':_no};
					$.post(url,data,function(e){
						$('#order_invoice_search_list').empty().append(e);
						var url = 'index.php?mod=sales&con=BaseOrderInfo&act=orderSearchByorder_sn';
						var data = {'order_no':_no};
						$.post(url,data,function(date){
							var url = 'index.php?mod=sales&con=BaseOrderInfo&act=showLogsed&id='+date.id;
							var data = {'order_no':''};
							$.post(url,data,function(date_html){
								$('#base_action_info_search_listed').html(date_html);
							});
						});
					});
				}
			});

			//回车提交
			$('#order_invoice_search_form input').keypress(function (e) {
					if (e.which == 13)
					{
						var _no = $('#order_invoice_search_form input[name="order_no"]').val();
						if(_no == '')
						{
							$('#order_invoice_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
						}
						else
						{
							var url = 'index.php?mod=finance&con=OrderInvoice&act=orderSearch';
							var data = {'order_no':_no};
							$.post(url,data,function(e){
								$('#order_invoice_search_list').empty().append(e);
                                var url = 'index.php?mod=sales&con=BaseOrderInfo&act=orderSearchByorder_sn';
                                var data = {'order_no':_no};
                                $.post(url,data,function(date){
                                    var url = 'index.php?mod=sales&con=BaseOrderInfo&act=showLogsed&id='+date.id;
                                    var data = {'order_no':''};
                                    $.post(url,data,function(date_html){
                                        $('#base_action_info_search_listed').html(date_html);
                                    });
                                });
							});
						}
						return false;
					}
			});
		};
		
		var initData = function(){

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