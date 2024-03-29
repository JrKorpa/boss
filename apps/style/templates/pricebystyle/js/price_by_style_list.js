//分页
function price_by_style_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"],function(){
	util.setItem('orl','index.php?mod=style&con=PriceByStyle&act=search');//设定刷新的初始url
	util.setItem('formID','price_by_style_search_form');//设定搜索表单id
	util.setItem('listDIV','price_by_style_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var Obj = function(){
		var initElements = function(){
	    $('#price_by_style_search_form select[name="xilie[]"]').select2({
                placeholder: "全部",
                allowClear: true
            });
            $('#price_by_style_search_form select[name="is_xiaozhang"]').select2({
                placeholder: "全部",
                allowClear: true
            });
            $('#price_by_style_search_form select[name="cat_type_id"]').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#price_by_style_search_form select[name="product_type_id"]').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#price_by_style_search_form select[name="check_status"]').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#price_by_style_search_form select[name="dismantle_status"]').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#price_by_style_search_form select[name="is_made"]').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#price_by_style_search_form select[name="style_sex"]').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            //初始化单选按钮组
			var is_made = $("#price_by_style_search_form input[name='is_made']");
			if (is_made.size() > 0) {
				is_made.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}	
			
			$('#price_by_style_search_form :reset').on('click',function(){
				$('#price_by_style_search_form select[name="attribute_status"]').select2("val","");
			})

            $('#price_by_style_search_form button[type="reset"]').on('click',function(){
                $('#price_by_style_search_form select[name="product_type_id"]').select2('val','').change();
                $('#price_by_style_search_form select[name="cat_type_id"]').select2('val','').change();
                $('#price_by_style_search_form select[name="check_status"]').select2('val','').change();
                $('#price_by_style_search_form select[name="dismantle_status"]').select2('val','').change();
                $('#price_by_style_search_form select[name="is_made"]').select2('val','').change();
                $('#price_by_style_search_form select[name="style_sex"]').select2('val','').change();
                $('#price_by_style_search_form select[name="is_xiaozhang"]').select2('val','').change();
            });

            // 点击图片弹出大图
	        $(".fancyboximg").fancybox({
                wrapCSS    : 'fancybox-custom',
                closeClick : true,
                openEffect : 'none',
                helpers : {
                    title : {
                        type : 'inside'
                    },
                    overlay : {
                        css : {
                            'background' : 'rgba(0,0,0,0.6)'
                        }
                    }
                }
            });
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			price_by_style_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	Obj.init();
});

//款号号 批量搜索
function bachstyle(){

    var col = $("#bachstyle").attr('class');
    if(col=='col-sm-3'){
        $("#bachstyle").attr('class','col-sm-9');
        $("#style_sn").attr('placeholder','输入多个款号时，请用英文模式逗号或空格分隔！');
    }
    if(col=='col-sm-9'){
        $("#bachstyle").attr('class','col-sm-3');
        $("#style_sn").attr('placeholder','双击可批量输入款号');
    }
    
}
