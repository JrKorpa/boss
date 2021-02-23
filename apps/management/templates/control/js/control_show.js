function operation_search_page_1(url){
    util.page(url,1);
}

function operation_recycle_search_page_1(url){
    util.page(url,2);
}

$import(function(){
    util.setItem('orl1','index.php?mod=management&con=Operation&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
    util.setItem('listDIV1','operation_search_list_1');


    util.setItem('orl2','index.php?mod=management&con=OperationRecycle&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
    util.setItem('listDIV2','operation_recycle_search_list_1');

    var OprObj = function(){
        var handleForm1 = function(){
            util.search(1);
        }

        return {

            init:function(){
                handleForm1();
                operation_search_page_1(util.getItem('orl1'));
            }
        }

    }();
    var OprRecycleObj = function(){
        var handleForm1 = function(){
            util.search(2);
        }

        return {

            init:function(){
                handleForm1();
                operation_recycle_search_page_1(util.getItem('orl2'));
            }
        }

    }();
    OprObj.init();
    OprRecycleObj.init();

    //util.closeDetail();//收起所有明细
    util.closeDetail(true);//展示第一个明细
});
