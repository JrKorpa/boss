var bill_id='<%$view->get_id()%>';
var bill_no='<%$view->get_bill_no()%>';

function material_order_goods_search_page(url){
	util.page(url,1);
}
$import(["public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"],function(){
    
	util.setItem('orl1','index.php?mod=sales&con=MaterialOrder&act=searchOrderGoods&_id='+getID().split('-').pop());
	util.setItem('listDIV1','material_order_goods_search_list');
	
	
	var obj1 = function(){
		
		var initElements = function(){
            
            // 点击图片弹出大图
            $(".fancyboximg").fancybox({
                wrapCSS    : 'fancybox-custom',
                closeClick : true,
                openEffect : 'none',
                helpers : {
                    title : {
                        type : 'inside'
                    },
                    overlay : {
                        css : {
                            'background' : 'rgba(0,0,0,0.6)',
							
                        }
                    }
                }
            });
        };
		
		
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {		
			init:function(){
				initElements();
				handleForm1();
				material_order_goods_search_page(util.getItem('orl1'));
			}
		}
	
	}();
	obj1.init();

	
});
	
	

