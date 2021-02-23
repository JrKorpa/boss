//匿名回调
$import(function(){

		$("#BatchPrintOrder_print_form button[name=print_order]").click(
			function(){
					var ids=$('#BatchPrintOrder_print_form textarea[name=ids]').val();
					if(ids.length==0)
					{
						util.xalert('请输入要打印的订单号！');
						return false;
					}
					ids=ids.replace(/\s+/g,',');
					// window.open('/index.php?mod=sales&con=BatchPrintOrders&act=exportOrder&ids='+ids,'newwindow','height='+screen.height+',width='+screen.width+',top=0,left=0,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no');
					window.open('/index.php?mod=warehouse&con=WaitDistribution&act=printBills&_ids='+ids+'&sign=1','newwindow','height='+screen.height+',width='+screen.width+',top=0,left=0,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no');
					return false;
				}
			);

});
