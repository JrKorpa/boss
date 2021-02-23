//分页
function user_warehouse_search_page(url){
	util.page(url);
}

//
function user_warehouse_add(obj){
	var trobj = $('#user_warehouse_search_form table tr.tab_click');
	if (!trobj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	util._pop($(obj).attr('data-url'),{id:$('td',trobj).eq(0).text()});
}

function user_warehouse_batch_add(o)
{
	util._pop($(o).attr('data-url'));
}


//匿名回调
$import(["public/js/jquery-datatables/css/jquery.dataTables.css","public/js/jquery-datatables/js/jquery.dataTables.min.js"],function(){
	util.setItem('formID','user_warehouse_search_result_form');//设定搜索表单id
	util.setItem('listDIV','user_warehouse_search_list');//设定列表数据容器id
        util.setItem('orl','index.php?mod=management&con=UserWarehouse&act=search');

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){};
		
		var handleForm = function(){
                    util.search();
		};
		
		var initData = function(){
			$('#user_warehouse_search_form').load('index.php?mod=management&con=UserWarehouse&act=leftList');
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