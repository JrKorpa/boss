//分页
function rel_channel_pay_search_page(url){
	util.page(url);
}

//列表自定义添加方法
function rel_channel_pay_add(obj){
	var trobj = $('#rel_sale_channels_search_form table tr.tab_click');
	if (!trobj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	util._pop($(obj).attr('data-url'),{id:$('td',trobj).eq(0).text()});
}

//匿名回调
$import(["public/js/jquery-datatables/css/jquery.dataTables.css","public/js/jquery-datatables/js/jquery.dataTables.min.js"],function(){
	util.setItem('orl','index.php?mod=management&con=RelChannelPay&act=search');//设定刷新的初始url
	util.setItem('formID','rel_channel_pay_search_form');//设定搜索表单id
	util.setItem('listDIV','rel_channel_pay_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			$('#rel_sale_channels_search_form').load('index.php?mod=management&con=RelChannelPay&act=LeftList');
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