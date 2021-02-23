//分页
function base_invoice_info_search_page(url){
	util.page(url);
}

//匿名回调
$import('public/js/select2/select2.min.js',function(){
	util.setItem('orl','index.php?mod=finance&con=BaseInvoiceInfo&act=search');//设定刷新的初始url
	util.setItem('formID','base_invoice_info_search_form');//设定搜索表单id
	util.setItem('listDIV','base_invoice_info_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //单选组件
            var test = $("#base_invoice_info_search_form input[name='type']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            //下拉组件
            $('#base_invoice_info_search_form select[name="status"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            
            
            //重置
            $('#base_invoice_info_search_form :reset').on('click', function() {
                //下拉重置
                $('#base_invoice_info_search_form select[name="status"]').select2("val", '').change();
                //单选按钮组重置
				$("#base_invoice_info_search_form input[name='type'][value='1']").attr('checked','checked');
				var test = $("#base_invoice_info_search_form input[name='type']:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function () {
						if ($(this).parents(".checker").size() == 0) {
							$(this).show();
							$(this).uniform();
						}
					});
				}
            });
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			base_invoice_info_search_page(util.getItem("orl"));
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