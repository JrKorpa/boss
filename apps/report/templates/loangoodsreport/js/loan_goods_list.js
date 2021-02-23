//分页
function loan_goods_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=LoanGoodsReport&act=search');//设定刷新的初始url
	util.setItem('formID','loan_goods_search_form');//设定搜索表单id
	util.setItem('listDIV','loan_goods_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#loan_goods_search_form select').select2({
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
			loan_goods_search_page(util.getItem("orl"));
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

function downloadLoanGoods(){
    var down_info = 'downLoangoods';
    var goods_id = $("#loan_goods_search_form [name='goods_id']").val();
    var goods_status = $("#loan_goods_search_form [name='goods_status']").val();
    var start = $("#loan_goods_search_form [name='start']").val();
    var end = $("#loan_goods_search_form [name='end']").val();
    var create_start_time = $("#loan_goods_search_form [name='create_start_time']").val();
    var create_end_time = $("#loan_goods_search_form [name='create_end_time']").val();
    var param = "&down_info="+down_info+"&goods_id="+goods_id+"&goods_status="+goods_status+"&start="+start+"&end="+end+"&create_start_time="+create_start_time+"&create_end_time="+create_end_time;
    url = "index.php?mod=report&con=LoanGoodsReport&act=search"+param;
	window.open(url);
}