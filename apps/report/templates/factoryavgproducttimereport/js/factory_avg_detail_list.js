
//分页
function factory_avg_detail_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){

    var xx_url = '';
    var xx_self = '';
    if(xx_self == true){
    	xx_url = 'index.php?mod=report&con=FactoryAvgProductTimeReport&act=detail_list_ajax&self='+xx_self;
    }else{
    	xx_url = 'index.php?mod=report&con=FactoryAvgProductTimeReport&act=detail_list_ajax';
    }
	util.setItem('orl',xx_url);//设定刷新的初始url

	util.setItem('formID','factory_avg_detail_list_search_form');//设定搜索表单id
	util.setItem('listDIV','factory_avg_detail_search_list');//设定列表数据容器id
	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
                     //下拉列表美化
                            $('#factory_avg_detail_list_search_form select').select2({
                                placeholder: "全部",
                                allowClear: true,
                            }).change(function(e) {
                                $(this).valid();
                            });//validator与select2冲突的解决方案是加change事件
                            //时间控件
                            if ($.datepicker) {
                                $('.date-picker').datepicker({
                                    format: 'yyyy-mm-dd',
                                    rtl: App.isRTL(),
                                    autoclose: true,
                                    clearBtn: true
                                });
                                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
                            }
                            $('#factory_avg_detail_list_search_form :reset').on('click',function(){
				$('#factory_avg_detail_list_search_form select').select2("val","");
				$('#factory_avg_detail_list_search_form input').select2("val","");
			    });
				$('#factory_avg_detail_list_search_form input[name=checkAll]').click(function(){
					var status=$(this).attr('checked');
					var arr=new Array();
					if(status=='checked'||status==true)
					{

						$('#factory_avg_detail_list_search_form select[name="buchan_fac_opra[]"] option').each(function(key,v){
							arr[key]=$(this).val();
						})
						$('#factory_avg_detail_list_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);
					}
					$('#factory_avg_detail_list_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);

				});

                };
		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			factory_avg_detail_search_page(util.getItem("orl"));
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
function show_avg_last_detail_list(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var start_time = tObj[0].getAttribute("start_time");
	var end_time = tObj[0].getAttribute("end_time");
	var from_type = tObj[0].getAttribute("from_type");
	var opra_uname = tObj[0].getAttribute("opra_uname");
	var prc_name= tObj[0].getAttribute("prc_name");
	var style_sn = tObj[0].getAttribute("style_sn");
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
							var title=tObj[0].getAttribute("data-title");
							if (title==null || $(obj).attr("use"))
							{
								title = $(obj).attr('data-title');
							}
							if ('undefined' == typeof title)
							{
								title = id;
							}
							url+="&id="+_id;

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
		var title=tObj[0].getAttribute("data-title");
		var _id = tObj[0].getAttribute("data-id").split('_').pop();
		var id = prefix+"-"+_id;
		if (title==null || $(obj).attr("use"))
		{
			title = $(obj).attr('data-title');
		}
		if ('undefined' == typeof title)
		{
			title = '';
		}
		url+="&start_time="+start_time+"&end_time="+end_time+"&from_type="+from_type+"&opra_uname="+opra_uname+"&prc_name="+prc_name+"&style_sn="+style_sn;
		new_tab(id,title,url);
	}
}