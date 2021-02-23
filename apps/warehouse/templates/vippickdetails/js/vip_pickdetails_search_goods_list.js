//分页
function vip_pickdetail_search_goods_page(url){
	util.page(url);
}
var formID= 'vip_pickdetail_search_goods_form';
//匿名回调
$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=VipPickDetails&act=searchGoodsList');//设定刷新的初始url
	util.setItem('formID',formID);//设定搜索表单id
	util.setItem('listDIV','vip_pickdetail_search_goods_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			 $('#'+formID+' select').select2({
				placeholder: "请选择",
				allowClear: true

		    }).change(function(e){
				$(this).valid();
		    });
		};
		   
		var handleForm = function(){
			util.search();
			util.openForm(util.getItem("formID"));
		};
		
		var initData = function(){
			//util.closeForm(util.getItem("formID"));
			//alert(util.getItem("orl"));
			vip_pickdetail_search_goods_page(util.getItem("orl"));
			$('#'+formID+' :reset').on('click',function(){
				$('#'+formID+' select').select2('val','');
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
