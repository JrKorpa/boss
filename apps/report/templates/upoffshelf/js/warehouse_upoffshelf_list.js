//导出csv
function upoffshelf_export_cxv_index(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['time_start']=document.getElementById("time_start").value;
	param['time_end']=document.getElementById("time_end").value;
	param['warehouse_type']=document.getElementById("warehouse_type").value;
	param['company_id']=document.getElementById("company_id").value;
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
function show_warehouse_detail_list(obj){
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
	var time_start = tObj[0].getAttribute("time_start");
	var time_end = tObj[0].getAttribute("time_end");
	var warehouse_type = tObj[0].getAttribute("warehouse_type");
	var warehouse_string = tObj[0].getAttribute("warehouse_string");
	var company_id = tObj[0].getAttribute("company_id");
	var dt = tObj[0].getAttribute("id");
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var prefix = params['con'].toLowerCase();
	url+="&time_start="+time_start+"&time_end="+time_end+"&warehouse_type="+warehouse_type+'&id='+dt+'&warehouse_string='+warehouse_string+'&company_id='+company_id;
		//不能同时打开两个详情页
	//alert(url);return;
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
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
							var id = prefix+"-"+_id;
							var title=tObj[0].getAttribute("data-title");
							if (title==null || $(obj).attr("use"))
							{
								title = $(obj).attr('data-title');
							}
							if ('undefined' == typeof title)
							{
								title = id;
							}
							//url+="&id="+_id;
							
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
		//url+="&time_start="+time_start+"&time_end="+time_end+"&warehouse_type="+warehouse_type+'&id='+dt;

		var id = prefix+"-"+_id;
		new_tab(id,title,url);
	}
}
function searchType(obj){
	var url = $(obj).attr('data-url');
	var listid = $(obj).attr('list-id');
	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xxa";
	var title = $(obj).attr('data-title');
	if (!title)
	{
		title=params['con'];
	}
	//不能同时打开两个添加页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '前往查看'
					},
					cancel: {
						label: '点错了'
					}
				},
				closeButton: false,
				message: "发现同类数据的页签已经打开。",
				callback: function(result) {
					if (result == true) {
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
		var id = prefix+"-0";
		new_tab(id, title,url+'&tab_id='+listid);
	}

}


//分页
function warehouse_pandian_plan_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=report&con=UpOffShelf&act=search');//设定刷新的初始url
	util.setItem('formID','warehouse_upoffshelf_search_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_upoffshelf_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
			$('#warehouse_upoffshelf_search_form select').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});

			//单选美化
			var test = $("#warehouse_upoffshelf_search_form input[type='radio']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_pandian_plan_search_page(util.getItem("orl"));
			$('#warehouse_upoffshelf_search_form :reset').on('click',function(){
				//下拉置空
				$('#warehouse_upoffshelf_search_form select[name="company_id"]').select2('val','').change();//single
				$('#warehouse_upoffshelf_search_form select[name="warehouse[]"]').select2('val','').change();//single
			});
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}
	}();

	obj.init();
});