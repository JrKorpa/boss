//分页
function tsyd_discount_config_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=diamond&con=TsydDiscountConfig&act=search');//设定刷新的初始url
	util.setItem('formID','tsyd_discount_config_search_form');//设定搜索表单id
	util.setItem('listDIV','tsyd_discount_config_search_list');//设定列表数据容器id

    var info_form_id= "tsyd_discount_config_search_form";
	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){

            $('#'+info_form_id+' select').select2({
                placeholder: "请选择",
                allowClear: true,
//              minimumInputLength: 2
            }).change(function(e){
                $(this).valid();
            });

            //单选组件
            var test = $("#tsyd_discount_config_search_form input[name='enabled']:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function() {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
            }
            $('#tsyd_discount_config_search_form select[name="type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#tsyd_discount_config_search_form :reset').on('click',function(){
                $('#tsyd_discount_config_search_form select[name="type"]').select2("val","");
            })

			$('#tsyd_discount_config_search_form select[name="enabled"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

            $('#tsyd_discount_config_search_form :reset').on('click',function(){
                $('#tsyd_discount_config_search_form select[name="enabled"]').select2("val","");
            })


           
           
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			tsyd_discount_config_search_page(util.getItem("orl"));
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