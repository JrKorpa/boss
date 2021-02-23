//打开详情页
function viewDetail(obj,is_quick_distrib){
	//检测配货单状态是否是配货中，如果不是配货中，就不能
	var tObj = $(obj).parent().parent().parent().parent().find('.flip-scroll>.table-scrollable>table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var peihuo_status = tObj[0].getAttribute('delivery-status');
	if(peihuo_status == 2){
		/*为了让库房发货更快，就跳过了变更配货中 的步骤~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~^(*￣(oo)￣)^
		bootbox.alert("当前单据状态不是“<span style='color:red;'>配货中</span>” ，不允许配货");
		return false;*/
	}

	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	if (typeof is_quick_distrib == 'undefined') is_quick_distrib = false;
	var prefix = params['con'].toLowerCase();
	//不能同时打开两个详情页
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
							var title=tObj[0].getAttribute("data-sn");
							if (title==null || $(obj).attr("use"))
							{
								title = $(obj).attr('data-sn');
							}
							if ('undefined' == typeof title)
							{
								title = id;
							}
							url+="&id="+_id;
							
							if (is_quick_distrib) {							
								new_tab(id,'门店发货',url);
							} else {
								// new_tab(id,title,url);
								new_tab(id,'待配货详情',url);
							}
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
		var id = prefix+"-"+_id;
		var title=tObj[0].getAttribute("data-sn");
		if (title==null || $(obj).attr("use"))
		{
			title = $(obj).attr('data-sn');
		}
		if ('undefined' == typeof title)
		{
			title = id;
		}
		url+="&id="+_id;
		
		if (is_quick_distrib) {							
			new_tab(id,'门店发货',url);
		} else {
			// new_tab(id,title,url);
			new_tab(id,'待配货详情',url);
		}
	}
}

//分页
function wait_distribution_search_page(url){
	util.page(url);
}

function quick_distribution(obj) {
	viewDetail(obj, true);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=WaitDistribution&act=showList');//设定刷新的初始url
	util.setItem('formID','wait_distribution_search_form');//设定搜索表单id
	util.setItem('listDIV','wait_distribution_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		$('#wait_distribution_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});
		var initElements = function(){
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			$('#wait_distribution_search_form :reset').on('click',function(){
				$('#wait_distribution_search_form select').select2("val","");
			});
			$('#wait_distribution_search_form select[name="channel_class"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
  				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WaitDistribution&act=getChannelIdByClass', {'channel_class': _t}, function (data) {
						$('#wait_distribution_search_form select[name="sales_channels_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#wait_distribution_search_form select[name="sales_channels_id"]').change();
					});
				}else{
					$('#wait_distribution_search_form select[name="sales_channels_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});
			

		};
		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			wait_distribution_search_page(util.getItem("orl"));
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

//订单号 批量搜索
function bachorder(){

	var col = $("#bachorder").attr('class');
	if(col=='col-sm-3'){
		$("#bachorder").attr('class','col-sm-9');
		$("#order_sn").attr('placeholder','输入多个订单号时逗号或空格分隔！');
	}
	if(col=='col-sm-9'){
		$("#bachorder").attr('class','col-sm-3');
		$("#order_sn").attr('placeholder','双击可批量输入订单号');
	}

}
//款号号 批量搜索
function bachstyle(){

	var col = $("#bachstyle").attr('class');
	if(col=='col-sm-3'){
		$("#bachstyle").attr('class','col-sm-9');
		$("#style_sn").attr('placeholder','输入多个货号时请用逗号或空格分隔！');
	}
	if(col=='col-sm-9'){
		$("#bachstyle").attr('class','col-sm-3');
		$("#style_sn").attr('placeholder','双击可批量输入订单号');
	}

}