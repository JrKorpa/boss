function check_order(obj)
{
	var type = "<%$view->get_type()%>";
	var id   = "<%$view->get_order_id()%>";
	url = "index.php?mod=shibao&con=DiaOrder&act=check&type="+type;
	bootbox.confirm("确定审核吗?", function(result){
		if(result == true){
			$.post(url, {id : id,type : type}, function(data){
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
					bootbox.alert("审核成功");
					util.retrieveReload();
				}else{
					bootbox.alert(data.error);
				}
			});
		}
	});
}
function cancle(obj)
{
	var type = "<%$view->get_type()%>";
	var id   = "<%$view->get_order_id()%>";
	url = "index.php?mod=shibao&con=DiaOrder&act=cancle&type="+type;
	bootbox.confirm("确定取消吗?", function(result){
		if(result == true){
			$.post(url, {id : id,type : type}, function(data){
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
					bootbox.alert("取消成功");
					util.retrieveReload();
				}else{
					bootbox.alert(data.error);
				}
			});
		}
	});
}
function dia_order_goods_search_page(url){
	util.page(url,1);
}


$import(function(){
	util.setItem('orl1','index.php?mod=shibao&con=DiaOrderGoods&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('formID1','dia_order_goods_search_form');
	util.setItem('listDIV1','dia_order_goods_search_list');


	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				dia_order_goods_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();

	//util.closeDetail();//收起所有明细
	util.closeDetail(true);//展示第一个明细
});