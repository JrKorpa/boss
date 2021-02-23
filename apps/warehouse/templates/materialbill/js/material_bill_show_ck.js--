var bill_id='<%$view->get_id()%>';
var bill_no='<%$view->get_bill_no()%>';

function material_bill_goods_search_page(url){
	util.page(url,1);
}
$import(function(){
    
	util.setItem('orl1','index.php?mod=warehouse&con=MaterialBill&act=searchBillGoods&_id='+getID().split('-').pop());
	util.setItem('listDIV1','material_bill_goods_search_list');
	
	
	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {		
			init:function(){
				handleForm1();
				material_bill_goods_search_page(util.getItem('orl1'));
			}
		}
	
	}();
	obj1.init();

	
});
	
	

