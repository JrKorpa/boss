//分页
function app_style_gallery_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=style&con=AppStyleGallery&act=search');//设定刷新的初始url
	util.setItem('formID','app_style_gallery_search_form');//设定搜索表单id
	util.setItem('listDIV','app_style_gallery_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var Obj = function(){
		
		var initElements = function(){
			
			//初始化下拉组件
			$('#app_style_gallery_search_form select[name="attribute_status"]').select2({
				placeholder: "全部",
				allowClear: true,

			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			
			$('#app_style_gallery_search_form :reset').on('click',function(){
				$('#app_style_gallery_search_form select[name="attribute_status"]').select2("val","");
			})
		
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_style_gallery_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				//initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	Obj.init();
});