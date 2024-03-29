//分页
function app_order_gift_search_page_zp(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=giftman&con=OrderZp&act=search');//设定刷新的初始url
	util.setItem('formID','gift_order_goods_search_form_s');//设定搜索表单id
	util.setItem('listDIV','gift_order_goods_search_list_s');//设定列表数据容器id
    var info_form_id='gift_order_goods_search_form_s';
	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
            $('#'+info_form_id+' select').select2({
				placeholder: "请选择",
                allowClear: true,
                });
            $('#'+info_form_id+'  :reset').on('click',function(){
                $('#'+info_form_id+' select').select2("val","");
            })
            //时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

			$('#gift_order_goods_search_form_s button[type="button"]').on('click',function(){
				var data=$("#gift_order_goods_search_form_s").serialize();
				location.href = "index.php?mod=giftman&con=OrderZp&act=downLoad&"+data;
			})
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
            app_order_gift_search_page_zp(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});