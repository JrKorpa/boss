function purchase_receipt_detail_search_page(url){
	util.page(url);
}
//全选按钮时间
function select_all(obj)
{
	$('input:checkbox').prop('checked',obj.checked);
}

//生成不良品返厂事件
function addBl(obj)
{
	var ids = [];
	$("input:checked[name='check[]']").each(function(i,o){
		ids.push(o.value);
	});
	if(!ids.length)
	{
		bootbox.alert('至少选择一个货品生成不良品返厂单');
	}else{
		bootbox.confirm("确定生成不良品返厂单?", function(result) {
			if (result == true) {
			$.post('index.php?mod=purchase&con=DefectiveProduct&act=insert',{
				   ids:ids
				},function(data){
				if(data.success==1)
				{
					bootbox.alert('操作成功');
					util.sync(obj);
				}
				else
				{
					bootbox.alert(data.error);
				}
			});}
		});
	}
}

//批量选取并弹框
function batch_add(obj){
	var ids = [];
	$("input:checked[name='check[]']").each(function(i,o){
		ids.push(o.value);
	});
	if (!ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}

	util._pop($(obj).attr('data-url'),{ids:ids});
}

$import('public/js/select2/select2.min.js',function(){
	util.setItem('orl','index.php?mod=purchase&con=PurchaseReceiptDetail&act=search');
	util.setItem('listDIV','purchase_receipt_detail_search_list');
	util.setItem('formID','purchase_receipt_detail_search_form');

	var PurchaseInfoObj = function(){
		var initElements = function(){
			$('#purchase_receipt_detail_search_form select[name="status"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#purchase_receipt_detail_search_form select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
		};
		
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			purchase_receipt_detail_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	PurchaseInfoObj.init();
});
