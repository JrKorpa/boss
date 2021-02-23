function conf_item_search_page (url)
{
    util.page(url);
}

function getdoc(obj){
    $.ajax({
        async:true,
        type: "POST",
        url: "index.php?mod=management&con=ConfItem&act=getDoc",
        dataType:"text",
        success: function(res){
            //alert(res);exit;
            window.location.href=res;
        }
    });

}
$import(function(){
    var moduls = 'ConfItem';
    util.setItem('orl','index.php?mod=management&con='+moduls+'&act=search');
    //util.setItem('formID',moduls + '_search_form');
    util.setItem('listDIV',moduls + '_search_list');

    var obj = function(){
        var initElements=function(){};
        var handleForm=function(){
            util.search();
        };
        var initData=function(){
            util.closeForm(util.getItem("formID"));
            conf_item_search_page(util.getItem("orl"));
        };

        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();
    obj.init();
});