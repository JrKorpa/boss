//分页
function material_bill_search_page(url){
	util.page(url);
}

var info_form_id = 'material_bill_search_form';
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){	util.setItem('orl','index.php?mod=warehouse&con=MaterialBillJin&act=search');//设定刷新的初始url
    util.setItem('orl','index.php?mod=warehouse&con=MaterialBillJin&act=search');//设定刷新的初始url
	util.setItem('formID',info_form_id);//设定搜索表单id
	util.setItem('listDIV','material_bill_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
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
			$('#'+info_form_id+' select').select2({
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
			//util.closeForm(util.getItem("form_id"));
			material_bill_search_page(util.getItem("orl"));			
			$('#'+info_form_id+' :reset').on('click',function(){
				
				//下拉置空
			    $('#'+info_form_id+' select').select2('val','').change();//single
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

/**
 * 
 * 打印
 * @param {*} obj 
 */
function printBill(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var id = tObj[0].getAttribute("data-id").split('_').pop();
	$.post("index.php?mod=warehouse&con=MaterialBillJin&act=printBill",{id:id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = tObj[0].getAttribute("data-id").split('_').pop();
			var url = "index.php?mod=warehouse&con=MaterialBillJin&act=printBill";
			var _name = $(obj).attr('data-title');
			var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){

			};
		}
	});
}

function printSalesBill(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var bill_id=$(tObj).attr('id');
	$.post("index.php?mod=warehouse&con=MaterialBillJin&act=printSalesBill",{id:bill_id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = tObj[0].getAttribute("data-id").split('_').pop();
			var url = "index.php?mod=warehouse&con=MaterialBillJin&act=printSalesBill";
			var _name = $(obj).attr('data-title');
			var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){

			};
		}
	});
}

function download(){
	var form = $("#material_bill_search_form").serializeArray();
	var url ="index.php?mod=warehouse&con=MaterialBillJin&act=search";
	$.each(form, function(){
		if(this.value!=''){
			url += "&"+this.name+"="+this.value;
		}
	});
	location.href = url+"&dow_info=dow_info";
}