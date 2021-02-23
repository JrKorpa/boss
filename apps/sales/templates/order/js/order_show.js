function order_check_no(obj)
{
change_status(obj,3,"操作成功");
}
function order_check(obj)
{
	change_status(obj,2,"审核成功");
}
function order_off(obj)
{
	change_status(obj,5,"关闭成功");
}
function order_sub(obj)
{
	change_status(obj,1,"提交成功");
}
function order_edit(obj)
{
	change_status(obj,1,"提交成功");
}



function addressEdit(obj)
{
	util.retrieveEdit(obj);
}

function change_status(obj,num,msg)
{
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var id = $(obj).attr('data-id');
	//alert(id);
	bootbox.confirm("确定继续?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id,status:num},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						alert(msg);
						$('.modal-scrollable').trigger('click');
						//alert(obj);
						util.retrieveReload(obj);
					}
					else{
						
						alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}
function del_goods(obj)
{
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var tObj = $('#'+getID()+' .tab_click');
	if (!tObj.length)
	{
		bootbox.alert('很抱歉，您当前未选中任何一行！');
		$('.modal-scrollable').trigger('click');
		return false;
	}
	var objid = tObj[0].getAttribute("data-id").split('_').pop();

		//setTimeout(function()
		//{

			$.post(url,{id:objid},function(data)
			{
				$('.modal-scrollable').trigger('click');
				if(data.success==1)
				{
					util.sync(obj);
					alert('操作成功');
					util.retrieveReload(obj);
				}
				else
				{
					alert(data.error);
				}
			});
		//}, 0);


 }
//分页
function app_order_details_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	id = <%$view->get_id()%>;
	util.setItem('orl','index.php?mod=sales&con=AppOrderDetails&act=search&id='+id);//设定刷新的初始url
	//util.setItem('formID','order_search_form');//设定搜索表单id
	util.setItem('listDIV','order_goods_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){

		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			//util.closeForm(util.getItem("formID"));
			app_order_details_search_page(util.getItem("orl"));
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
