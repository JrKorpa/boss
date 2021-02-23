//导出csv
function export_cxv_second(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['company_id']=document.getElementById("company_id").value;
	param['warehouse_id']=document.getElementById("warehouse_id").value;
	param['type']=document.getElementById("type").value;
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
//分页
function warehouse_stock_second_search_page(url){
	util.page(url);
}
function show_warehouse_stock_index_third(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var title=tObj[0].getAttribute("data-title");
	if(title=='all_count'){
			util.xalert("很抱歉，请选择具体的一个日期！");
			return ;
	}
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var warehouse_ids_string = tObj[0].getAttribute("warehouse_ids_string");
	var company_ids_string = tObj[0].getAttribute("company_ids_string");
	var types_string = tObj[0].getAttribute("types_string");
	var dotime_string = tObj[0].getAttribute("dotime_string");
	var con=params['con'].toLowerCase();
	var act=params['act'].toLowerCase();
	var prefix = con+'-'+act;
	var _id = tObj[0].getAttribute("data-id");
	var id =_id;
	url+="&types_string="+types_string+"&company_ids_string="+company_ids_string+"&warehouse_ids_string="+warehouse_ids_string+"&dotime_string="+dotime_string;
	//alert(url);return;
		//不能同时打开两个详情页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({  
				buttons: {  
					confirm: {  
						label: '确认' 
					},  
					cancel: {  
						label: '查看'  
					}  
				},
				closeButton:false,
				message: "发现同类数据的查看页已经打开。\r\n点确定将关闭同类查看页。\r\n点查看将激活同类查看页。",  
				callback: function(result) {  
					if (result == true) {
						setTimeout(function(){
							$(that).children('i').trigger('click');
							var title=tObj[0].getAttribute("data-title");
							if (title==null || $(obj).attr("use"))
							{
								title = $(obj).attr('data-title');
							}
							if ('undefined' == typeof title)
							{
								title = id;
							}
							new_tab(id,title,url);
						}, 0);
					}
					else if (result==false)
					{
						$(that).children('a').trigger("click");
					} 
				},  
				title: "提示信息", 
			});
			return false;
		}
	});
	
	if (!flag)
	{
		
		if (title==null || $(obj).attr("use"))
		{
			title = $(obj).attr('data-title');
		}
		if ('undefined' == typeof title)
		{
			title = '';
		}
		new_tab(id,title,url);
	}
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	var url='index.php?mod=report&con=WarehouseStockReport&act=search_second';
	util.setItem('orl',url);//设定刷新的初始url
	util.setItem('formID','warehouse_stock_search_form_second');//设定搜索表单id
	util.setItem('listDIV','warehouse_stock_search_list_second');//设定列表数据容器id

	//匿名函数+闭包
	var ApplicationListObj = function(){

		var initElements = function(){
			//初始化下拉组件
			$('#warehouse_stock_search_form_second select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });
			$('#warehouse_stock_search_form_second :reset').on('click',function(){
				$('#warehouse_stock_search_form_second select').select2("val","");
			})

		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_stock_second_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}
	}();

	ApplicationListObj.init();
});