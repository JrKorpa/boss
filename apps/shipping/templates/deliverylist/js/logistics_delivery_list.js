//分页
function logistics_delivery_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=shipping&con=DeliveryList&act=search');//设定刷新的初始url
	util.setItem('formID','logistics_delivery_search_form');//设定搜索表单id
	util.setItem('listDIV','logistics_delivery_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var AppOrderWeixiuListObj = function(){
		
		var initElements = function(){
			//初始化下拉组件
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

			$('#logistics_delivery_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});


		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			//util.closeForm(util.getItem("listDIV"));
			logistics_delivery_search_page(util.getItem("orl"));
			$('#logistics_delivery_search_form :reset').on('click',function(){
				$('#logistics_delivery_search_form select').select2("val","");
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

	AppOrderWeixiuListObj.init();
});


function download() {
	//快递单号 订单号 开始时间 结束时间
	var down_info='down_info';
    var freight_no = $("#logistics_delivery_search_form [name='freight_no']").val();
    var order_no = $("#logistics_delivery_search_form [name='order_no']").val();
    var date_time_s = $("#logistics_delivery_search_form [name='date_time_s']").val();
    var date_time_e = $("#logistics_delivery_search_form [name='date_time_e']").val();
    var create_name = $("#logistics_delivery_search_form [name='create_name']").val();
    var department = $("#logistics_delivery_search_form [name='department']").val();
    var remark = $("#logistics_delivery_search_form [name='remark']").val();
    var is_print = $("#logistics_delivery_search_form [name='is_print']").val();
    var express_id = $("#logistics_delivery_search_form [name='express_id']").val();

	 var channel_id = $("#logistics_delivery_search_form [name='channel_id']").val();
    var out_order_sn = $("#logistics_delivery_search_form [name='out_order_sn']").val();
	
	if (freight_no == "" && order_no == "" && date_time_s == "" && date_time_e == "" &&create_name == "" && department == "" && remark == "" && is_print == "" && express_id  == "" && channel_id == "" && out_order_sn == "")
	{
		util.xalert("请至少选择一个条件，将数据缩减到2-3万才下载，数据量太大无法下载！");
		return false;
	}
 
    var args = "&down_info="+down_info+"&freight_no="+freight_no+"&order_no="+order_no+"&date_time_s="+date_time_s+"&date_time_e="+date_time_e+"&create_name="+create_name+"&department="+department+"&remark="+remark+"&is_print="+is_print+"&express_id="+express_id+"&channel_id="+channel_id+"&out_order_sn="+out_order_sn;
    location.href = "index.php?mod=shipping&con=DeliveryList&act=search"+args;
}

function print_order(obj) {
	
	var tObj = $(obj).parent().parent().next().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var bill_id=$(tObj).attr('id');
	var bill_type=$(tObj).attr('data-type');
	//多选判断
	var chk_value =[]; 
	$("#logistics_delivery_search_list [name='_ids[]']:checked").each(function(){ 
		chk_value.push($(this).val()); 
	}); 
	if(chk_value.length<=0){
		util.xalert("最少选择一条打印单！");return false;
	}
	$.post("index.php?mod=shipping&con=DeliveryList&act=print_order",{id:bill_id},function(res){
    				if(res.error){
    					alert(res.error);
    				}else{

    					var id = tObj[0].getAttribute("data-id").split('_').pop();

						var url = "index.php?mod=shipping&con=DeliveryList&act=print_order";
						var _name = $(obj).attr('data-title');
						var son = window.open(
						url+'&id='+id+'&chk_value='+chk_value,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes'
						);
						son.onUnload = function(){

						};



    				}
 		 });
	
}

//全选 反选
function checkboxes(obj) {
	var test =$(obj).attr('checked');
	//选中 都选中
	if(test=='checked'){
		var chk_value =[]; 
		$("#logistics_delivery_search_list [name='_ids[]']").each(function(){ 
			chk_value.push($(this).attr('checked',true)); 
		}); 

	}else{
		var chk_value =[]; 
		$("#logistics_delivery_search_list [name='_ids[]']").each(function(){ 
			chk_value.push($(this).attr('checked',false));
		}); 
	}

}
//add by zhangruiying
function DeliveryListEdit(obj)
{
	var id=$('#logistics_delivery_search_list .tab_click').attr('data-id');
	if(typeof id=='undefined')
	{
		util.xalert('请选择您要编辑的记录444！');
		return false;
	}
	var t=id.split('_');
	t=t.pop();
	$.post('index.php?mod=shipping&con=DeliveryList&act=checkStatus',{'id':t},function(data){
		if(data.status==1)
		{
			util.edit(obj);
		}
		else
		{
			util.xalert(data.error_msg);
		}
	});
	
}

function mutiPrintParcelLists(obj)
{
    
    var url=$(obj).attr('data-url');
    var temp=''
    var ids=$('#logistics_delivery_search_list input[name="_ids[]"]:checked').each(
                function(){
                    temp+=$(this).val()+',';
                }
            );
	if(temp.length==0)
	{
		util.xalert("请先选中你要打印的包裹！");
		return false;
	}
    url+='&ids='+temp.substr(0,temp.length-1);


    //location.href=url;


	var son = window.open(url,'','fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);
	};
}
//add end 