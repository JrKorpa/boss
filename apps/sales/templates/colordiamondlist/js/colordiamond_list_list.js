//分页
function colordiamond_list_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
    util.setItem('orl','index.php?mod=sales&con=ColorDiamondList&act=search');//设定刷新的初始url
	util.setItem('formID','colordiamond_list_search_form');//设定搜索表单id
	util.setItem('listDIV','color_diamond_list_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){
			var test = $("#colordiamond_list_search_form input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
			 	test.each(function () {
			   	if ($(this).parents(".checker").size() == 0) {
			     	$(this).show();
			     	$(this).uniform();
			    }
			  });
			}
			//初始化下拉组件
			$('#colordiamond_list_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});//validator与select2冲突的解决方案是加change事件	
			
			$('#colordiamond_list_search_form :reset').on('click',function(){
				$('#colordiamond_list_search_form :checkbox').each(function(){
					$(this).parent().removeClass("active");
				})
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			colordiamond_list_search_page(util.getItem("orl"));
            // $('#'+util.getItem('formID')).submit();
            $('#colordiamond_list_search_form :reset').on('click',function(){
				$('#colordiamond_list_search_form select[name="goods_sn"]').select2('val','');
				$('#colordiamond_list_search_form select[name="carat_min"]').select2('val','');
				$('#colordiamond_list_search_form select[name="carat_max"]').select2('val','');
				$('#colordiamond_list_search_form select[name="color_grade"]').select2('val','');
				$('#colordiamond_list_search_form select[name="color"]').select2('val','');
				$('#colordiamond_list_search_form select[name="shape"]').select2('val','');
				$('#colordiamond_list_search_form select[name="clarity"]').select2('val','');
				$('#colordiamond_list_search_form select[name="cert"]').select2('val','');
				$('#colordiamond_list_search_form select[name="cert_id"]').select2('val','');
				$('#colordiamond_list_search_form select[name="price_min"]').select2('val','');
				$('#colordiamond_list_search_form select[name="price_max"]').select2('val','');
				$('#colordiamond_list_search_form select[name="from_ad"]').select2('val','');
			})
			//diamond_list_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	ListObj.init();
});

