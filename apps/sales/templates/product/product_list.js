//分页
function product_search_page(url){
	util.page(url);
}


function tongdengxiangkou(obj){
    var xiangkou = $(obj).attr('xiangkou');
    util._pop($(obj).attr('data-url'),{xiangkou:xiangkou});
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/select2/select2.min.js"],function(){
	var goods_id=$('#product_form input[name="goods_id"]').val();
	util.setItem('orl','index.php?mod=sales&con=Product&act=search&goods_id='+goods_id);//设定刷新的初始url
	util.setItem('formID','product_form');//设定搜索表单id
	util.setItem('listDIV','product_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){
			$('#product_form select').select2({
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
			product_search_page(util.getItem("orl"));
            //这里做下拉的重置
            $('#product_form button[type=reset]').on('click',function(){
                $('#product_form select').select2('val','').change();
            })
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	ListObj.init();
});

