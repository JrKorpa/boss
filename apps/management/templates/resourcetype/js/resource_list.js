function resource_search_page(url)
{
    util.page(url);
}

$import(function(){
    var moduls = 'ResourceType';
    util.setItem('orl','index.php?mod=management&con='+moduls+'&act=search');
    util.setItem('formID',moduls + '_search_form');
    util.setItem('listDIV',moduls + '_search_list');

    var obj = function(){
        var initElements=function(){};
        var handleForm=function(){
            util.search();
        };
        var initData=function(){
            util.closeForm(util.getItem("formID"));
            resource_search_page(util.getItem("orl"));
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