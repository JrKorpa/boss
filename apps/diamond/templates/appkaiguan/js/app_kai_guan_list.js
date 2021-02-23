//分页
function app_kai_guan_search_page(url){
	util.page(url);
}

//匿名回调
$import(function(){
	util.setItem('orl','index.php?mod=diamond&con=AppKaiGuan&act=search');//设定刷新的初始url
	util.setItem('formID','app_kai_guan_search_form');//设定搜索表单id
	util.setItem('listDIV','app_kai_guan_search_lists');//设定列表数据容器id

	//匿名函数+闭包
	var objDiamond = function(){
		
		var initElements = function(){
			$('#save_dia_fetch').on('click',function(){
				var data=$("#form_diamond_switch :checkbox,:checked").serialize();
				$.post('index.php?mod=diamond&con=AppKaiGuan&act=update_dia_switch',data,function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('更改成功');
					}
					else
					{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			})		
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_kai_guan_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();
	objDiamond.init();
});