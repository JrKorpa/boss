//分页
function diamond_sale_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=DiamondSaleReport&act=search');//设定刷新的初始url
	util.setItem('formID','diamond_sale_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_sale_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#diamond_sale_search_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open");
            }
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			diamond_sale_search_page(util.getItem("orl"));
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

function downloadDiaSale(){
    var down_info = 'downSale';
    var goods_id = $("#diamond_sale_search_form [name='goods_id']").val();
    var goods_status = $("#diamond_sale_search_form [name='goods_status']").val();
    var start = $("#diamond_sale_search_form [name='start']").val();
    var end = $("#diamond_sale_search_form [name='end']").val();
    var param = "&down_info="+down_info+"&goods_id="+goods_id+"&goods_status="+goods_status+"&start="+start+"&end="+end;
    url = "index.php?mod=report&con=DiamondSaleReport&act=search"+param;
	window.open(url);
}