//分页
function app_diamond_color_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=diamond&con=AppDiamondColor&act=search');//设定刷新的初始url
	util.setItem('formID','app_diamond_color_search_form');//设定搜索表单id
	util.setItem('listDIV','app_diamond_color_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			//下拉组件
			$('#app_diamond_color_search_form select').select2({
					placeholder: "请选择",
					allowClear: true
      });

		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_diamond_color_search_page(util.getItem("orl"));
			$('#app_diamond_color_search_form :reset').on('click',function(){
				$('#app_diamond_color_search_form select[name="color"]').select2('val','');
				$('#app_diamond_color_search_form select[name="shape"]').select2('val','');
				$('#app_diamond_color_search_form select[name="clarity"]').select2('val','');
				$('#app_diamond_color_search_form select[name="cert"]').select2('val','');
				$('#app_diamond_color_search_form select[name="color_grade"]').select2('val','');
				$('#app_diamond_color_search_form select[name="from_ad"]').select2('val','');
				$('#app_diamond_color_search_form select[name="status"]').select2('val','');
			
			})
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

function downloads() {
	var formdata = $("#app_diamond_color_search_form").serialize();
    location.href = "index.php?mod=diamond&con=AppDiamondColor&act=downLoad&"+formdata;
}