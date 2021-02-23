function fqc(obj)//质检未通过
{
	var order_sn = $("#fqc_order_sn1").val();
	//alert(order_sn);
	$(obj).attr("data-url","index.php?mod=warehouse&con=OrderFqc&act=fqc_pass_no&order_sn="+order_sn);
	util.add(obj);
}
function fqc_pass(obj)//质检通过
{
	var order_sn = $("#fqc_order_sn1").val();
	if(!confirm("确定质检通过"))
	{
		return false;
	}

	$.post('index.php?mod=warehouse&con=OrderFqc&act=fqc_pass',{order_sn:order_sn},function(data){
		if (data.success == 1)
		{
			//util.xalert("操作成功",function(){
				$("#fqc_err").html("操作成功");
				$('#order_fqc_search_form input[name="order_sn"]').val('');
				$('#order_fqc_search_form input[name="order_sn"]').focus();
			//});
		}
		else
		{
			//util.xalert(data.error,function(){
				$("#fqc_err").html(data.error);
				$('#order_fqc_search_form input[name="order_sn"]').val('');
				$('#order_fqc_search_form input[name="order_sn"]').focus();
			//});
		}
		//var url = 'index.php?mod=warehouse&con=OrderFqc&act=search1';
		//var data = {'order_sn':order_sn};
		//$.post(url,data,function(e){
		//	$('#order_fqc_search_list').empty().append("操作成功");
		//});
	})
	//$(obj).attr("data-url","index.php?mod=warehouse&con=OrderFqc&act=fqc_pass&order_sn="+order_sn);
	//util.add(obj)
}
