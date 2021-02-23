function payment_search_page (url)
{
    util.page(url);
}

$import("public/js/select2/select2.min.js",function(){
    var moduls = 'payment';
    util.setItem('orl','index.php?mod=management&con='+moduls+'&act=search');
    util.setItem('formID',moduls + '_search_form');
    util.setItem('listDIV',moduls + '_search_list');

    var obj = function(){
        var initElements=function(){};
        var handleForm=function(){
            util.search();
        };
        var initData=function(){
            $('#payment_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(){
				$(this).valid();
			});
            util.closeForm(util.getItem("formID"));
            payment_search_page(util.getItem("orl"));
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