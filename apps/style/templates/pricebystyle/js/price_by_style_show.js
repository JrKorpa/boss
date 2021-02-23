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
                'background' : 'rgba(0,0,0,0.6)'
            }
        }
    }
});


var style_sn='<%$view->get_style_sn()%>';
var style_id='<%$view->get_style_id()%>';
var cat_type_id='<%$view->get_style_type()%>';
var product_type_id='<%$view->get_product_type()%>';
function price_app_xiangkou_search_page(url){
	util.page(url,6);
}

function price_rel_style_stone_search_page(url){
	util.page(url,1);
}


	
$import(function(){
	util.setItem('orl_p_6','index.php?mod=style&con=AppXiangkou&act=search&_id='+getID().split('-').pop()+'&style_sn='+style_sn+'&style_id='+style_id);//设定刷新的初始url
	util.setItem('listDIV_P_6','price_app_xiangkou_search_list');

	var obj6 = function(){
		var handleForm1 = function(){
			util.search(6);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				price_app_xiangkou_search_page(util.getItem('orl_p_6'));
			}
		}
	
	}();

	obj6.init();
});
	
	

