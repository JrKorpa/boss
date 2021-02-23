function shop_cfg_search_page (url)
{
	util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=shipping&con=Tydprint&act=search');
	util.setItem('formID','shop_cfg_search_form');
	util.setItem('listDIV','tydprint_search_list');
	
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


function mutiPrintParcelList(obj)
{
    
    var url=$(obj).attr('data-url');
    var temp=''
    var ids=$('#tydprint_search_list input[name="_ids[]"]:checked').each(
                function(){
                    temp+=$(this).val()+',';
                }
            );
	if(temp.length==0)
	{
		util.xalert("请先选中你要打印的体验店！");
		return false;
	}
    url+='&ids='+temp.substr(0,temp.length-1);


    //location.href=url;


	var son = window.open(url,'','fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);
	};
}