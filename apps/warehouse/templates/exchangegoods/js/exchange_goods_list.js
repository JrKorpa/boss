function fqc(obj)//质检未通过
{
	var order_sn = $("#order_sn1").val();
	//alert(order_sn);
	$(obj).attr("data-url","index.php?mod=warehouse&con=OrderFqc&act=fqc_pass_no&order_sn="+order_sn);
	util.add(obj);
}
function fqc_pass(obj)//质检通过
{
	var order_sn = $("#order_sn1").val();
	if(!confirm("确定质检通过"))
	{
		return false;
	}

	$.post('index.php?mod=warehouse&con=OrderFqc&act=fqc_pass',{order_sn:order_sn},function(data){
		if (data.success == 1)
		{
			util.xalert("操作成功",function(){
				$('#order_fqc_search_form input[name="order_sn"]').val('');
				$('#order_fqc_search_form input[name="order_sn"]').focus();
			});
		}
		else
		{
			util.xalert(data.error,function(){
				$('#order_fqc_search_form input[name="order_sn"]').val('');
				$('#order_fqc_search_form input[name="order_sn"]').focus();
			});
		}
		var url = 'index.php?mod=warehouse&con=OrderFqc&act=search1';
		var data = {'order_sn':order_sn};
		$.post(url,data,function(e){
			$('#order_fqc_search_list').empty().append("操作成功");
		});
	})
	//$(obj).attr("data-url","index.php?mod=warehouse&con=OrderFqc&act=fqc_pass&order_sn="+order_sn);
	//util.add(obj)
}
$import(function(){
	util.setItem('listDIV','exchange_goods_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var FQCobj = function(){
		var initElements = function(){};
		var handleForm = function(){
			//sreach button
			$('#exchange_goods_search_form button').click(function(){
				var _no = $('#exchange_goods_search_form input[name="order_sn"]').val();
				if(_no == ''){
					$('#exchange_goods_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
				}else{
					var url = 'index.php?mod=warehouse&con=ExchangeGoods&act=search';
					var data = {'order_sn':_no};
					$.post(url,data,function(e){
						$('#exchange_goods_search_list').empty().append(e);
						$("#order_sn").val('');
						$("#order_sn").focus();
					});
				}
			});
					//回车提交
			$('#exchange_goods_search_form input').keypress(function (e) {
				if (e.which == 13) {
				var _no = $('#exchange_goods_search_form input[name="order_sn"]').val();
				if(_no == ''){
					$('#exchange_goods_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
				}else{
					var url = 'index.php?mod=warehouse&con=ExchangeGoods&act=search';
					var data = {'order_sn':_no};
					$.post(url,data,function(e){
						$('#exchange_goods_search_list').empty().append(e);
						$("#order_sn").val('');
						$("#order_sn").focus();
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
	FQCobj.init();
});



