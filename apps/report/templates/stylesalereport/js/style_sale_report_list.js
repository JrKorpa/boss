//分页
function style_sale_report_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=StyleSaleReport&act=search');//设定刷新的初始url
	util.setItem('formID','style_sale_report_search_form');//设定搜索表单id
	util.setItem('listDIV','style_sale_report_search_list');//设定列表数据容器id
   var info_form_id = "style_sale_report_search_form";
	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){			
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }               		
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			style_sale_report_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});


function downloads(){
    var down_infos = 'downs';
    var date_start = $("#style_sale_report_search_form [name='date_start']").val();
    var date_end = $("#style_sale_report_search_form [name='date_end']").val();
    var style_sn = $("#style_sale_report_search_form [name='style_sn']").val();    
    var  param= "&down_infos="+down_infos+"&date_start="+date_start+"&date_end="+date_end+"&style_sn="+style_sn;
    url = "index.php?mod=report&con=StyleSaleReport&act=search"+param;
    window.open(url);
}