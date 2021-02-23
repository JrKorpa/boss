//审核单据
function checkBillM(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	bootbox.confirm("确定审核吗?", function(result){
		if(result == true){
			$.post(url, {id : id,bill_no : bill_no}, function(data){
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
					util.retrieveReload();
				}else{
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
				}
			});
		}
	});
}


//确认发货
function confirmP(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';

	bootbox.confirm("确定要改变确认发货吗?", function(result){
		if(result == true){
			$.post(url, {id : id,bill_no : bill_no}, function(data){
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
					util.retrieveReload();
				}else{
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
				}
			});
		}
	});
}


//结算
function jieJia(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';

	bootbox.confirm("确定要结算吗?", function(result){
		if(result == true){
			$.post(url, {id : id}, function(data){
				$('.modal-scrollable').trigger('click');
				if (data.success == 1) {
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
					util.retrieveReload();
				}else{
					bootbox.alert({
						message : data.error,
						buttons : {
							ok : {
								label : '确定'
							}
						},
						animate : true,
						closeButton : false,
						title : "提示信息",
					});
				}
			});
		}
	});
}

//打印条码
function printcode(){
	var bill_id = '<%$view->get_id()%>';
	location.href = "index.php?mod=warehouse&con=WarehouseBillInfoP&act=print_q&bill_id="+bill_id;
}

/** 塞进页面 **/
function warehouse_bill_goods_show_page(url){
	util.page(url);
}

//签收
function sign_p_bill(obj){
	var id = '<%$view->get_id()%>';
	var com_id = '<%$view->get_to_company_id()%>';
	var url = 'index.php?mod=warehouse&con=WarehouseBillInfoP&act=sign_p_bill&ops=presign&bill_id='+id+'&to_comp='+com_id;
	util._pop(url);
}

$import(
	["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/jquery-zero/ZeroClipboard.min.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	],function() {
	var info_id = '<%$view->get_id()%>';
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoP&act=getGoodsInDetails&bill_id='+info_id);
	util.setItem('listDIV',"warehouse_bill_goods_list_p");

			var obj1 = function() {
				var initElements1 = function(){
					warehouse_bill_goods_show_page(util.getItem("orl"));
					//关闭容易引起js冲突的页签
					if (info_id) {
						var txt = $('#nva-tab li a');
						txt.each(function(i){
							if($.trim($(this).text()) == '批发销售'){
								$(this).parent().children('i').trigger('click');
							}
						});
					}
				};

				//重置表单
				var initData = function(){
					//批量复制货号
					util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_m');
				};

				return {
					init : function() {
						initElements1();// 处理表单元素
						initData();
					}
				}
			}();
			obj1.init();
		});

