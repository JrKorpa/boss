//分页
function rel_style_lovers_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"],function(){
	util.setItem('orl','index.php?mod=style&con=RelStyleLovers&act=search');//设定刷新的初始url
	util.setItem('formID','rel_style_lovers_search_form');//设定搜索表单id
	util.setItem('listDIV','rel_style_lovers_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
		   $('#rel_style_lovers_search_form select[name="xilie[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            });
			 $('#rel_style_lovers_search_form select[name="product_type_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#rel_style_lovers_search_form select[name="check_status"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
        
            $('#rel_style_lovers_search_form select[name="is_made"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#rel_style_lovers_search_form select[name="style_sex"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
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
			rel_style_lovers_search_page(util.getItem("orl"));
			 $('#rel_style_lovers_search_form button[type="reset"]').on('click',function(){
                $('#rel_style_lovers_search_form select[name="product_type_id"]').select2('val','').change();
                $('#rel_style_lovers_search_form select[name="check_status"]').select2('val','').change();
                $('#rel_style_lovers_search_form select[name="dismantle_status"]').select2('val','').change();
                $('#rel_style_lovers_search_form select[name="is_made"]').select2('val','').change();
                $('#rel_style_lovers_search_form select[name="style_sex"]').select2('val','').change();
            });
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

//款号号 批量搜索
function bachstyle_love(){

    var col = $("#bachstyle_love").attr('class');
    if(col=='col-sm-3'){
        $("#bachstyle_love").attr('class','col-sm-9');
        $('#rel_style_lovers_search_form select[name="style_sn"]').attr('placeholder','输入多个款号时,请用英文模式逗号分隔！');
    }
    if(col=='col-sm-9'){
        $("#bachstyle_love").attr('class','col-sm-3');
        $('#rel_style_lovers_search_form select[name="style_sn"]').attr('placeholder','双击可批量输入款号');
    }
    
}