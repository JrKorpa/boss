function purchase_goods_check_123(e){
    var id = '<%$view->get_id()%>';
    var url = 'index.php?mod=purchase&con=PurchaseGoods&act=applycheck';
	$('body').modalmanager('loading');//进度条和遮罩
    $.post(url,{'id':id,'pass':1},function(e){
		$('body').modalmanager('removeLoading');//关闭进度条
        if(e.success == 1){
            util.xalert((e.pass)?'操作成功-审核通过':'操作成功-审核取消',function(){
                util.closeTab();
				util.retrieveReload();
            });
        }else{
            util.xalert(e.error);
        }		
    })

}

function purchase_goods_checkout_123(){
    var id = '<%$view->get_id()%>';
    var url = 'index.php?mod=purchase&con=PurchaseGoods&act=applycheck';
	$('body').modalmanager('loading');//进度条和遮罩
    $.post(url,{'id':id,'pass':0},function(e){
		$('body').modalmanager('removeLoading');//关闭进度条
        if(e.success == 1){
            util.xalert((e.pass)?'操作成功-审核通过':'操作成功-审核取消',function(){
                util.closeTab();util.retrieveReload();
                //$('.modal-scrollable').trigger('click');
            });
        }else{
            util.xalert(e.error);
        }
		
    })
}