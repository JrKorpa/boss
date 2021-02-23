
function updateshopship(obj){
	var order_sn = $('#ship_freight_shoporder_form input[name="__order_sn"]').val();
	var order_id  =	$('#ship_freight_shoporder_form input[name="__order_id"]').val();
	var express_id = $('#ship_freight_shoporder_form select[name="express_id"]').val();
	var send_good_status = $('#ship_freight_shoporder_form input[name="__send_status"]').val();
    util._pop($(obj).attr('data-url'),{'order_sn':order_sn,'express_id':express_id , 'send_status':send_good_status});
}

//匿名回调
$import(function(){

	//匿名函数+闭包
	var obj = function(){
      
		var initElements = function(){
		};

		var handleForm = function(){
			//submit button
			$('#ship_freight_shoporder_form :submit').on("click", function(){alert(1);
				var order_sn = $('#ship_freight_shoporder_form input[name="__order_sn"]').val();
				var order_id  =	$('#ship_freight_shoporder_form input[name="__order_id"]').val();
				var express_id = $('#ship_freight_shoporder_form select[name="express_id"]').val();
				var freight_no = $('#ship_freight_shoporder_form input[name="freight_no"]').val();
				var send_good_status = $('#ship_freight_shoporder_form input[name="__send_status"]').val();
				if(!order_sn){
					util.error('找不到订单号');
					return false;
				}else if (!express_id) {
					util.error('快递公司不能为空');
					return false;
				}else if(!freight_no) {
					util.error('快递单号不能为空');
					return false;
				} else {
					var url = '/index.php?mod=shipping&con=ShipFreight&act=updateShipMethod&a=quikdistrib&order_id='+order_id+'&note_t=订单发货';
					var data = {'order_sn':order_sn, 'send_status':send_good_status, 'freight_no':freight_no, 'express_id':express_id};
					$.post(url, data, function(res){
						if(res.success == 1 ){
							if (typeof get_shop_order_shipping=='function' && util.getItem("shopship")) {
								get_shop_order_shipping(util.getItem("shopship"));
								wait_order_action_list(util.getItem("shop_actl_url"));
							}
						}else{
							if (res.error_code == 'address_is_empty') {
								bootbox.confirm({
						  			size : 'medium',
						  			title: "提示信息",
						  			message : '<font size="2">该订单没有收货地址，请去订单页面添加</font>',
						  			buttons: {
						  				confirm: { label: '去添加', className: 'btn-primary'},
						  				cancel: { label: '知道了', className: 'btn-default'}
						  			},
						  			callback : function(res) {
						  				if(res) {
						  					new_tab('baseorderinfo-'+order_sn,order_sn,'/index.php?mod=sales&con=BaseOrderInfo&act=show&order_sn='+order_sn);
						  				}
						  			}
						         });
							} else {
								util.error(res.error);
							}
						}
					});
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