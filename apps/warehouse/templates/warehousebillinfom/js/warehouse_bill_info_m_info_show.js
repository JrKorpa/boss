function print_info(obj) {
    var url =$(obj).attr('data-url') ;
    var id = '<%$view->get_id()%>';
    //js请求方法
    url = url+'&id='+id;
    window.location.href=url;
    
    
}

//调拨单详情页打印单据
function printMShowBill(obj) {
    var url =$(obj).attr('data-url') ;
    var id = '<%$view->get_id()%>';
    //js请求方法
    url = url+'&id='+id;
    window.open(url);
    //window.location.href=url;  
}

function printHunbohui(obj){
    var url =$(obj).attr('data-url');
    var id = '<%$view->get_id()%>';
     //js请求方法
    url = url+'&id='+id;
    window.open(url);      
    //window.location.href=url;
}

//核对货品
function hedui_goods(obj){
    var url = $(obj).attr('data-url');
    var bill_no = '<%$view->get_bill_no()%>';
    var bill_id = '<%$view->get_id()%>';
    util._pop(url+'&bill_no='+bill_no+'&bill_id='+bill_id);
}
function add(obj){
	var url = $(obj).attr('data-url');
	var tab = $(obj).attr('data-id');
	var title = $(obj).attr('data-title');
    new_tab(tab,title,url);
}

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
// 取消单据
function closeBillM(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';
	
	//出库公司
	var company = '<%$view->get_from_company_name()%>';
	
	$.get('index.php?mod=warehouse&con=WarehouseBillInfoM&act=checkbing&bill_no='+bill_no,'',function(res){
		var massage = '确定取消单据吗?';
		if(company == '总公司'){
			if(res == 1){
				massage = "当前调拨单已经绑定了包裹单，确定取消单据吗?";
			}
		}

		bootbox.confirm(massage, function(result) {
			if (result == true) {
				$.post(url, {id : id,bill_no : bill_no}, function(data) {
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
					} else {
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
	});
	
}

/** 塞进页面 **/
function warehouse_bill_goods_show_page(url){
	util.page(url);
}

$import(
	[
	"public/js/jquery-zero/ZeroClipboard.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	],function() {
			var info_id = '<%$view->get_id()%>';
			var to_company_id = '<%$view->get_to_company_id()%>';
			var to_warehouse_id = '<%$view->get_to_warehouse_id()%>';
			var from_company_id = '<%$view->get_from_company_id()%>';

			var obj1 = function() {
				var initElements1 = function(){
					if (!jQuery().uniform){
						return;
					}
					
					//关闭容易引起js冲突的页签
					if (info_id) {
						var txt = $('#nva-tab li a');
						txt.each(function(i){
							if($.trim($(this).text()) == '调拨单'){
								$(this).parent().children('i').trigger('click');
							}
						});
					}
				};

				var initElements = function(){
				
				};



				//重置表单
				var initData = function(){
					//批量复制货号
					util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_m');
				};

				return {
					init : function() {
						initElements1();// 处理表单元素
						initElements();
						initData();
					}
				}
			}();
			obj1.init();

			util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoM&act=getGoodsInDetails&bill_id='+info_id);
			util.setItem('listDIV',"warehouse_bill_goods_list");
			var obj2 = function(){
				var initElements2 = function(){
					warehouse_bill_goods_show_page(util.getItem("orl"));
				}

				return {
					init : function(){
						initElements2();// 处理表单元素
					}
				}
			}();
			obj2.init();
		});

//打印条码
function printcode(){
	var down_info = 'down_info';
    var bill_id = $("#bill_id").val();
    var args = "&down_info="+down_info+"&bill_id="+bill_id;
    location.href = "index.php?mod=warehouse&con=WarehouseBill&act=printcode"+args;

}