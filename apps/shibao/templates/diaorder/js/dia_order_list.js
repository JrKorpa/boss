//编辑单据页面
function editOrder(obj)
{
	var tObj	= $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');
	if(tObj[0]== undefined)
	{
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var type	= tObj[0].getAttribute("data-type");
	//var url		= obj.getAttribute("data-url")+type;//获取地址

	$(obj).attr("data-url","index.php?mod=shibao&con=DiaOrder&act=edit&type="+type);
	util.editNew(obj);
}
//编辑单据页面
function show_detail(obj)
{
	var tObj	= $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');
	if(tObj[0]== undefined)
	{
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var type	= tObj[0].getAttribute("data-type");
	//var url		= obj.getAttribute("data-url")+type;//获取地址

	$(obj).attr("data-url","index.php?mod=shibao&con=DiaOrder&act=show&type="+type);
	util.view(obj);

}
//打印
function print_shibao(obj)
{
	var tObj = $(obj).parent().parent().next().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var order_id=$(tObj).attr('data-title');
		$.post("index.php?mod=shibao&con=DiaOrder&act=print_shibao",{id:order_id},function(res){
    				if(res.error){
    					alert(res.error);	
    				}else{

    					var id = tObj[0].getAttribute("data-id").split('_').pop();

						var url = $(obj).attr('data-url');
						var _name = $(obj).attr('data-title');

						var son = window.open(
						$(obj).attr('data-url')+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false'
						);
							son.onUnload = function(){
						util.sync(obj);
						};



    				}
 		 });

}
//分页
function dia_order_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=shibao&con=DiaOrder&act=search');//设定刷新的初始url
	util.setItem('formID','dia_order_search_form');//设定搜索表单id
	util.setItem('listDIV','dia_order_search_list');//设定列表数据容器id
	
	//匿名函数+闭包
	var obj = function(){
			$('#dia_order_search_form select').select2({
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

			$('#dia_order_search_form :reset').on('click',function(){
				$('#dia_order_search_form select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){

			util.closeForm(util.getItem("formID"));
			dia_order_search_page(util.getItem("orl"));
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

//下载
function downloads() {

    var order_id = $("#order_id").val();
    var type 	 = $("#type").val();
    var status 	 = $("#status").val();
    var send_goods_sn = $("#send_goods_sn").val();
    var shibao 	 = $("#dia_order_search_form input[name='shibao']").val();
    var zhengshuhao = $("#zhengshuhao").val();
    var make_order = $("#make_order").val();
    var prc_id	 = $("#prc_id").val();
    var in_warehouse_type = $("#in_warehouse_type").val();
    var account_type = $("#account_type").val();
    var add_time_start = $("#add_time_start").val();
    var add_time_end = $("#add_time_end").val();
    var check_time_start = $("#check_time_start").val();
    var check_time_end = $("#check_time_end").val();
    var info = $("#info").val();



    var args = "&order_id="+order_id+"&type="+type+"&status="+status+"&send_goods_sn="+send_goods_sn+"&shibao="+shibao+
    			"&zhengshuhao="+zhengshuhao+"&make_order="+make_order+"&prc_id="+prc_id+"&in_warehouse_type="+in_warehouse_type+"&account_type="+account_type+"&add_time_start="+add_time_start+
    			"&add_time_end="+add_time_end+"&check_time_start="+check_time_start+"&check_time_end="+check_time_end+"&info="+info;
    location.href = "index.php?mod=shibao&con=DiaOrder&act=downloads"+args;
}