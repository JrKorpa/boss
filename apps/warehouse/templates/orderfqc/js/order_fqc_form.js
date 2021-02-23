$import(function(){
	util.setItem('listDIV','order_fqc_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var FQCobj = function(){
		var initElements = function(){};
		var handleForm = function(){
			//sreach button
			$('#order_fqc_search_form button').click(function(){
				var _no = $('#order_fqc_search_form input[name="order_sn"]').val();
				if(_no == ''){
					$('#order_fqc_search_list').empty().append("<div class='alert alert-info'>请填写订单号！</div>");
				}else{
					$('body').modalmanager('loading');
					var url = 'index.php?mod=warehouse&con=OrderFqc&act=search';
					var data = {'order_sn':_no};
					$.post(url,data,function(e){
						$('.modal-scrollable').trigger('click');
						$('#order_fqc_search_list').empty().append(e);
						$("#order_sn").val('');
						$("#order_sn").focus();
					});
				}
			});

			$('#order_fqc_search_form input[name="order_sn"]').keypress(function (e) {
				if (e.which == 13) {
					$('#order_fqc_search_form button').click();
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