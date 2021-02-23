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

//导出结果
function downCsv(obj){
	var tObj = $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var id = tObj[0].getAttribute("data-id").split('_').pop();
	var down_url = 'index.php?mod=warehouse&con=UpOffShelf&act=downCsv&id='+id;
	window.open(down_url);
}


//分页
function warehouse_pandian_plan_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=report&con=UpOffShelf&act=warehouse_detail_list&dt='+dt);//设定刷新的初始url
	util.setItem('formID','warehouse_upoffshelf_search_second_form');//设定搜索表单id
	util.setItem('listDIV','warehouse_upoffshelf_search_list2');//设定列表数据容器id

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
				$('#warehouse_upoffshelf_search_form select[name="type"]').select2('val','').change();//single
				$('#warehouse_upoffshelf_search_form select[name="status"]').select2('val','').change();//single
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