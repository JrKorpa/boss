
function pay_should_show_page(url){
    util.page(url);
}

$import(function(){
    var id = <%$info['pay_number_id']%>;
    util.setItem('orl','index.php?mod=finance&con=PayShould&act=showlist&id='+id);
    util.setItem('listDIV','pay_should_show_list_'+id);

    var PurchaseInfoObj = function(){
        var initElements = function(){};
        var handleForm = function(){};
        var initData = function(){
            pay_should_show_page(util.getItem("orl"));
        };
        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();

    PurchaseInfoObj.init();

});
