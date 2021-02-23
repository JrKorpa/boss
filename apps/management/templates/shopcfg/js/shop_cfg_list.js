function shop_cfg_search_page (url)
{
	util.page(url);
}

$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js"],function(){
	util.setItem('orl','index.php?mod=management&con=ShopCfg&act=search');
	util.setItem('formID','shop_cfg_search_form');
	util.setItem('listDIV','shop_cfg_search_list');
	
	var ShopCfgObj = function(){
		var initElements=function(){
            $('#shop_cfg_search_form select[name="shop_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
        };
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			util.closeForm(util.getItem("formID"));
			shop_cfg_search_page(util.getItem("orl"));
		};
	
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		
		}
	}();
	ShopCfgObj.init();
});

//导出
function download(){
	var down_info = 'down_info';
    var shop_name = $("#shop_cfg_search_form [name='shop_name']").val();
    var shop_type = $("#shop_cfg_search_form [name='shop_type']").val();

    /*if(!shop_name && !shop_type){
    	if(!confirm('没有导出限制可能会消耗较长的时间，点击‘确定’继续！')){
    		return false;
    	}	
    }*/
    var args = "&down_info="+down_info+"&shop_name="+shop_name+"&shop_type="+shop_type;
    location.href = "index.php?mod=management&con=ShopCfg&act=search"+args;

}