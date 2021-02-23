//分页
function base_lz_discount_config_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=diamond&con=BaseLzDiscountConfig&act=search');//设定刷新的初始url
	util.setItem('formID','base_lz_discount_config_search_form');//设定搜索表单id
	util.setItem('listDIV','base_lz_discount_config_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
            //单选组件
            var test = $("#base_lz_discount_config_search_form input[name='enabled']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            $('#base_lz_discount_config_search_form select[name="type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
            $('#base_lz_discount_config_search_form select[name="user_id[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#base_lz_discount_config_search_form :reset').on('click',function(){
                $('#base_lz_discount_config_search_form select[name="type"]').select2("val","");
            })

			$('#base_lz_discount_config_search_form select[name="enabled"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#base_lz_discount_config_search_form :reset').on('click',function(){
                $('#base_lz_discount_config_search_form select[name="enabled"]').select2("val","");
            })


           
           
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			base_lz_discount_config_search_page(util.getItem("orl"));
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