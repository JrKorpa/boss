
//匿名回调
$import("public/js/select2/select2.min.js",function(){

	var order_money = '<%$orderInfo.order_amount%>';
	var coupon_price = '<%$orderInfo.coupon_price%>';

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
			$('#from_company_id').select2({
				placeholder: "请选择",
				allowClear: true
			});
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
		}

		var form_table = function(){
			$('#wait_destribution_form_btn').click(function(){
				$('body').modalmanager('loading');//进度条和遮罩
				//提交的货号
				var goods_id_str = '';
				$("input[name='jxc_goods_id[]']").each(function(element) {
					goods_id_str += ','+this.value;
				}); //取值
				//提交的货品订单明细id
				var orderDetailId = '';
				$("input[name='orderDetailId[]']").each(function(element) {
					orderDetailId += ','+this.value;
				}); //取值
				//商品的最终价格
				var goods_id_price = '';
				$("input[name='goods_id_price[]']").each(function(element) {
					goods_id_price += ','+this.value;
				}); //取值
				var goods_sns_str = $('#goods_sns').val();
				var goods_nums_str = $('#goods_nums').val();
				var from_company_id = $('#from_company_id').val();
				var data = {
					'order_id':order_id, 	//订单id
					'order_sn':order_sn,	//订单号
					'order_money': order_money, 	//订单金额
					'goods_ids':goods_id_str, 		//提交的货号
					'goods_sns':goods_sns_str, 		//每个货品款号
					'goods_nums':goods_nums_str, 	//每个货品的数量
					'orderDetailId':orderDetailId, 	//提交的货品订单明细id
					'from_company_id':from_company_id,
					'coupon_price':coupon_price,//订单优惠金额
					'delivery_status': '<%$orderInfo.delivery_status%>',	//单据的配送状态
					'goods_id_price':goods_id_price,
					'distribution_type':'<%$orderInfo.distribution_type|default:0%>',
				};

				var url = 'index.php?mod=warehouse&con=WaitDistribution&act=xiaozhang';
				// console.log(data);
				$.post(url , data , function(ext){
					if(ext.success ==1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						//$('#sudu').html('<span>'+ext.order_sn+'  配货成功......</span>');
						//$('#sudu span').fadeOut(7000);
						$('#tab_bill_m_1').html('');
						bootbox.alert(ext.order_sn+'  配货成功');
						$('#xiaozhang_search input[name="order_sn"]').val('');
						$('#xiaozhang_search input[name="order_sn"]')[0].focus();
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						bootbox.alert({
							message: ext.error ? ext.error : (ext ? ext :'程序异常'),
							buttons: {
									   ok: {
											label: '确定'
										}
									},
							animate: true,
							closeButton: false,
							title: "提示信息"
						});
					}
				});
			});
		}

		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
				form_table();	//提交货号
			}
		}
	}();
	obj.init();
});