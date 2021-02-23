function shopcount_search_page (url)
{
	util.page(url);
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/select2/select2.min.js",
	"public/js/fancyapps-fancyBox/jquery.fancybox.css"],function(){
	util.setItem('orl','index.php?mod=report&con=ShopcountReport&act=search');
	util.setItem('formID','shopcountreport_search_form');
	util.setItem('listDIV','shopcountreport_search_list');
	
	var ShopCfgObj = function(){
		var initElements=function(){
            $('#shopcountreport_search_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
						
			$('#shopcountreport_search_form select[name="shop_type"]').change(function(e){
                $(this).valid();
                $('#shopcountreport_search_form select[name="shop_id[]"]').empty();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=ShopcountReport&act=getShops',{shop_type:t_v},function(data){
		                $('#shopcountreport_search_form select[name="shop_id[]"]').empty();
                        $('#shopcountreport_search_form select[name="shop_id[]"]').append(data);
                    });
                }
                else
                {
                    $('#shopcountreport_search_form select[name="shop_id[]"]').select2('val','').attr('readOnly',false).change();
                }
            });


			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open");
			}
			
			$('#shop_cfg_search_form :reset').on('click',function(){
				$('#shop_cfg_search_form select').select2("val","");
			})
        };
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			util.closeForm(util.getItem("formID"));
			shopcount_search_page(util.getItem("orl"));
		};
	
		return {
			init:function(){
				initElements();
				handleForm();
				//initData();
			}
		}
	}();
	ShopCfgObj.init();
});

function getShopcountReportMan(obj)
{
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
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
		url+="&"+$("#shopcountreport_search_form").serialize();
		new_tab(id,title,url);
	}
}

//导出
function download(){
	var args=$("#shopcountreport_search_form").serialize();
    url= "index.php?mod=report&con=ShopcountReport&act=downloads&"+args;
	window.open(url);
}