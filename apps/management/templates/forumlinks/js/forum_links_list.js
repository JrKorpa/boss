function forum_links_search_page (url)
{
    util.page(url);
}

$import(function(){
	util.setItem('orl','index.php?mod=management&con=ForumLinks&act=search');//设定刷新的初始url
	util.setItem('formID','forum_links_search_form');//设定搜索表单id
	util.setItem('listDIV','forum_links_search_list');//设定列表数据容器id

    var obj = function(){
        var initElements=function(){};
        var handleForm=function(){
            util.search();
        };
        var initData=function(){
            util.closeForm(util.getItem("formID"));
            forum_links_search_page(util.getItem("orl"));
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