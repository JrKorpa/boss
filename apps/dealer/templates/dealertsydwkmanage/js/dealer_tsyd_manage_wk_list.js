//分页
function dealer_tsyd_manage_wk_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
         "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=dealer&con=DealerTsydWKManage&act=search');//设定刷新的初始url
	util.setItem('formID','dealer_tsyd_manage_search_wk_form');//设定搜索表单id
	util.setItem('listDIV','dealer_tsyd_manage_search_wk_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			
			$('#dealer_tsyd_manage_search_wk_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
			
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
			dealer_tsyd_manage_wk_search_page(util.getItem("orl"));
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

function open_sub_table(obj){
    if($(obj).find('i').hasClass('fa-plus')){
        $(obj).find('i').removeClass('fa-plus').addClass('fa-minus');
        $(obj).parent().parent().next().show();
    }else{
        $(obj).find('i').removeClass('fa-minus').addClass('fa-plus');
        $(obj).parent().parent().next().hide();
    }
}