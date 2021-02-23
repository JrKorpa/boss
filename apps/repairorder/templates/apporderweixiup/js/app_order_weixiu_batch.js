
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	


	var AppOrderWeixiuInfoObj = function(){

		var initElements=function(){

		}
		//表单验证和提交
		var handleForm = function(){

		};
		var initData=function(){

		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	AppOrderWeixiuInfoObj.init();
});

function batch_order(obj)
{
	
	var ids = $('#app_order_weixiu_batch #weixiu_id').val();
	//将回车替换为‘,’
	ids=ids.replace(/\r\n/g,",") 
	ids=ids.replace(/\n/g,",");  
	//alert(ids);
    var url ='index.php?mod=repairorder&con=AppOrderWeixiuP&act=batch_order';
     //js请求方法
  
    //window.open(url);      
    $.post(url,{_ids:ids},function(res){
		if(res.success)
		{
			util.xalert("操作成功",function(){
				util.retrieveReload();//页面刷新
			});
		}
		else
		{ 
	
			util.error(res.error);
		}
	});
}

function batch_complete(obj)
{
	var ids = $('#app_order_weixiu_batch #weixiu_id').val();
	//将回车替换为‘,’
	ids=ids.replace(/\r\n/g,",") 
	ids=ids.replace(/\n/g,",");  
	//alert(ids);
    var url ='index.php?mod=repairorder&con=AppOrderWeixiuP&act=batch_complete';
     //js请求方法
     $.post(url,{_ids:ids},function(res){
		if(res.success)
		{
			util.xalert("操作成功",function(){
				util.retrieveReload();//页面刷新
			});
		}
		else
		{ 
	
			util.error(res.error);
		}
	});
}


function batch_goods(obj)
{
	var ids = $('#app_order_weixiu_batch #weixiu_id').val();
	//将回车替换为‘,’
	ids=ids.replace(/\r\n/g,",") 
	ids=ids.replace(/\n/g,",");  
	//alert(ids);
    var url ='index.php?mod=repairorder&con=AppOrderWeixiuP&act=batch_goods';
     //js请求方法
     $.post(url,{_ids:ids},function(res){
		if(res.success)
		{
			util.xalert("操作成功",function(){
				util.retrieveReload();//页面刷新
			});
		}
		else
		{ 
	
			util.error(res.error);
		}
	});
}