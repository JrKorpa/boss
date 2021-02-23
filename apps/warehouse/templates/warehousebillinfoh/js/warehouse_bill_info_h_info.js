//审核单据
function checkBillH(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';
    var to_warehouse_id = $("#to_warehouse_id").val();
    <%if $smarty.const.SYS_SCOPE eq 'boss'%>
			bootbox.confirm("确定审核吗?注意查看入库仓是否正确。", function(result){
				if(result == true){
					$.post(url, {id : id,bill_no : bill_no,to_warehouse_id:to_warehouse_id}, function(data){
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
    <%/if%>      


    <%if $smarty.const.SYS_SCOPE eq 'zhanting'%>
			bootbox.confirm("确定审核?", function(result){
				if(result == true){
					$.post(url, {id : id,bill_no : bill_no,to_warehouse_id:to_warehouse_id}, function(data){
						console.log(data);
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
    <%/if%> 
}





//签收单据
function signBillH(obj) {
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var id = '<%$view->get_id()%>';
	var bill_no = '<%$view->get_bill_no()%>';
    var to_warehouse_id = $("#to_warehouse_id").val();
    <%if $smarty.const.SYS_SCOPE eq 'zhanting'%>
			bootbox.prompt("确定签收?（<font color='red'>注意查看入库仓是否正确</font>）。<br>备注:", function(result){
				if(result != null){
					$.post(url, {id : id,bill_no : bill_no,to_warehouse_id:to_warehouse_id,remark:result}, function(data){
						console.log(data);
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
    <%/if%>          
}



/** 塞进页面 **/
function warehouse_bill_goods_show_page(url){
	util.page(url);
}

$import(
	["public/js/select2/select2.min.js",
	"public/js/jquery-zero/ZeroClipboard.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",	//明细table插件
	],function() {
        
			var info_id = '<%$view->get_id()%>';
			//var to_company_id = '<%$view->get_to_company_id()%>';
			//var to_warehouse_id = '<%$view->get_to_warehouse_id()%>';
			//var from_company_id = '<%$view->get_from_company_id()%>';

			var obj1 = function() {
				var initElements1 = function(){
					if (!jQuery().uniform){
						return;
					}
				/*
					$('#warehouse_bill_info_r_show select[name="to_company_id"]').select2({
						placeholder: "请选择",
						allowClear: true,
					}).change(function (e){
		  				$(this).valid();
						var _t = $(this).val();
						if (_t) {
							$.post('index.php?mod=warehouse&con=WarehouseBillInfoR&act=getTowarehouseId', {'id': _t}, function (data) {
								$('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
								$('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').change();
							});
						}else{
							$('#warehouse_bill_info_r_show select[name="to_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
						}
					});

					$('#warehouse_bill_info_r_show select[name="to_warehouse_id"], #warehouse_bill_info_r_show select[name="from_company_id"]').select2({
						placeholder : "请选择",
						allowClear : true
					}).change(function(e){
						$(this).valid();
					});
*/
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

				var from_table1 = function(){
					
					
				};

				//重置表单
				var initData = function(){
					/*$('#reset').on('click', function(){
						$('#warehouse_bill_info_r_show select[name="to_company_id"]').select2("val", to_company_id).change();
						$('#warehouse_bill_info_r_show select[name="from_company_id"]').select2("val", from_company_id).change();
					});*/
					util.batchCopyGoodsid('<%$view->get_id()%>','batch_copy_goodsid_o_show');
				};

				return {
					init : function() {
						initElements1();// 处理表单元素
						from_table1();
						initData();
					}
				}
			}();
			obj1.init();

			util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoH&act=getGoodsInDetails&bill_id='+info_id);
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