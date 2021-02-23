//分页
function ship_freight_search_page(url){
	util.page(url);
}
//匿名回调
$import(function(){
	//util.setItem('orl','index.php?mod=shipping&con=ShipFreight&act=search');//设定刷新的初始url
	//util.setItem('formID','ship_freight_search_form');//设定搜索表单id
	util.setItem('listDIV','ship_freight_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
			$('#ship_freight_search_form input[name="order_no"]').val("");
			$('#ship_freight_search_form input[name="order_no"]')[0].focus();
			//$("#ship_freight_search_list").html("操作成功");
		};

		var handleForm = function(){
			//sreach button
			$('#ship_freight_search_form button').click(function(){
				var _no = $('#ship_freight_search_form input[name="order_no"]').val();
				if(_no == ''){
					$('#ship_freight_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
				}else{
					$('body').modalmanager('loading');
					var url = 'index.php?mod=shipping&con=ShipFreight&act=orderSearch';
					var data = {'order_no':_no};
					$.post(url,data,function(e){
						$('#ship_freight_search_form input[name=order_no]').val('');
						$('.modal-scrollable').trigger('click');
						$('#ship_freight_search_list').empty().append(e);
					});
				}
			});

			//回车提交
			$('#ship_freight_search_form input[name="order_no"]').keypress(function (e) {
					if (e.which == 13)
					{
						var _no = $('#ship_freight_search_form input[name="order_no"]').val();
						if(_no == '')
						{
							$('#ship_freight_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
						}
						else
						{
							var url = 'index.php?mod=shipping&con=ShipFreight&act=orderSearch';
							var data = {'order_no':_no};
							$.post(url,data,function(e){
								$('#ship_freight_search_list').empty().append(e);
								$('#ship_freight_search_form input[name=order_no]').val('');
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