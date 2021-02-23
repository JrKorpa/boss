// JavaScript Document
function importorder()
{
	var taobaoid = $("#out_order_sn").val();
	if( taobaoid < 1)
	{
		$("#message").html('请输入淘宝订单号');
	}else{
		$.ajax({
			type:"POST",
			url: "../zhuadan.php",
			data: {'taobaoid':taobaoid},
			success: function(message)
			{
				$("#message").html(message);
			}
		});
	}
}