//分页
function warehouse_goods_report_search_page(url){
	util.page(url);
}
//点击搜索后关闭搜索框
function closeSearchForm() {
    $("#searchform").trigger('click');
}
function bach_search_num(){
	var col = $("#bacheitem").attr('class');
	if(col=='col-sm-3'){
		$("#bacheitem").attr('class','col-sm-9');
		$("#bacheitem").attr('placeholder','输入多个货号时,请用英文模式逗号分隔！');
	}
	if(col=='col-sm-9'){
		$("#bacheitem").attr('class','col-sm-3');
		$("#item_id").attr('placeholder','双击可批量输入货号');
	}
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	var company_id="<%$args.company_id%>";
	var warehouse_id="<%$args.warehouse_id%>";
	util.setItem('orl','index.php?mod=report&con=WarehouseGoodsReport&act=search&warehouse_id='+warehouse_id+'&company_id='+company_id);
	util.setItem('listDIV','warehouse_goods_report_search_list');
	util.setItem('formID','warehouse_goods_report_search_form');
	var WarehouseGoodsObj = function(){
		var initElements = function(){
			$('#warehouse_goods_search_form select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});

                        if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

			$('#warehouse_goods_search_form select[name="company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
  				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WarehouseGoods&act=getTowarehouseId', {'id': _t}, function (data) {
						$('#warehouse_goods_search_form select[name="warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#warehouse_goods_search_form select[name="warehouse_id"]').change();
					});
				}else{
					$('#warehouse_goods_search_form select[name="warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});

			$('#warehouse_goods_search_form select[name="weixiu_company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
				$(this).valid();
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WarehouseBillInfoO&act=getTowarehouseId', {'id': _t}, function (data) {
						$('#warehouse_goods_search_form select[name="weixiu_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
						$('#warehouse_goods_search_form select[name="weixiu_warehouse_id"]').change();
					});
				}else{
					$('#warehouse_goods_search_form select[name="weixiu_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});

                        $('#warehouse_goods_search_form :reset').on('click',function(){
				$('#warehouse_goods_search_form select').select2("val","");
			})

		};
		var handleForm = function(){
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_goods_report_search_page(util.getItem("orl"));
			//avg_deliver_time_search_page(util.getItem("orl"));
			//warehouse_goods_search_page('index.php?mod=warehouse&con=WarehouseGoods&act=search');
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	WarehouseGoodsObj.init();
});
//导出
function download(){
	var down_info = 'down_info';
    var goods_id = $("#warehouse_goods_search_form [name='goods_id']").val();
    var style_sn = $("#warehouse_goods_search_form [name='style_sn']").val();
    var put_in_type = $("#warehouse_goods_search_form [name='put_in_type']").val();
    var weixiu_status = $("#warehouse_goods_search_form [name='weixiu_status']").val();
    var is_on_sale = $("#warehouse_goods_search_form [name='is_on_sale']").val();
    var zhuchengse = $("#warehouse_goods_search_form [name='zhuchengse']").val();
    var company_id = $("#warehouse_goods_search_form [name='company_id']").val();
    var warehouse_id = $("#warehouse_goods_search_form [name='warehouse_id']").val();
    var cat_type = $("#warehouse_goods_search_form [name='cat_type']").val();
    var cat_type1 = $("#warehouse_goods_search_form [name='cat_type1']").val();
    var zhengshuhao = $("#warehouse_goods_search_form [name='zhengshuhao']").val();
    var order_goods_ids = $("#warehouse_goods_search_form [name='order_goods_ids']").val();
    var shoucun = $("#warehouse_goods_search_form [name='shoucun']").val();
    var kucun_start = $("#warehouse_goods_search_form [name='kucun_start']").val();
    var kucun_end = $("#warehouse_goods_search_form [name='kucun_end']").val();
	var processor = $("#warehouse_goods_search_form [name='processor']").val();
    var buchan = $("#warehouse_goods_search_form [name='buchan']").val();
    var mohao = $("#warehouse_goods_search_form [name='mohao']").val();

    var zhushi = $("#warehouse_goods_search_form [name='zhushi']").val();
    var zhengshu_type = $("#warehouse_goods_search_form [name='zhengshu_type']").val();
    var zs_color = $("#warehouse_goods_search_form [name='zs_color']").val();
    var zs_clarity = $("#warehouse_goods_search_form [name='zs_clarity']").val();
    var jinshi_type = $("#warehouse_goods_search_form [name='jinshi_type']").val();
    var jintuo_type = $("#warehouse_goods_search_form [name='jintuo_type']").val();
    var jiejia = $("#warehouse_goods_search_form [name='jiejia']").val();
    var guiwei = $("#warehouse_goods_search_form [name='guiwei']").val();
    var chanpinxian = $("#warehouse_goods_search_form [name='chanpinxian']").val();
	var chanpinxian1 = $("#warehouse_goods_search_form [name='chanpinxian1']").val();
    var jinzhong_begin = $("#warehouse_goods_search_form [name='jinzhong_begin']").val();
    var jinzhong_end = $("#warehouse_goods_search_form [name='jinzhong_end']").val();
    var zhushi_begin = $("#warehouse_goods_search_form [name='zhushi_begin']").val();
    var zhushi_end = $("#warehouse_goods_search_form [name='zhushi_end']").val();
    var weixiu_status = $("#warehouse_goods_search_form [name='weixiu_status']").val();


    var args = "&down_info="+down_info+"&goods_id="+goods_id+"&style_sn="+style_sn+"&put_in_type="+put_in_type+"&is_on_sale="+is_on_sale+"&zhuchengse="+zhuchengse+"&company_id="+company_id+"&warehouse_id="+warehouse_id+"&cat_type="+cat_type+"&cat_type1="+cat_type1
    			+"&zhengshuhao="+zhengshuhao+"&order_goods_ids="+order_goods_ids+"&shoucun="+shoucun+"&kucun_start="+kucun_start+"&kucun_end="+kucun_end+"&processor="+processor+"&buchan="+buchan+"&mohao="+mohao
    			+"&zhushi="+zhushi+"&zhengshu_type="+zhengshu_type+"&zs_color="+zs_color+"&zs_clarity="+zs_clarity+"&jinshi_type="+jinshi_type+"&jintuo_type="+jintuo_type+"&jiejia="+jiejia+"&weixiu_status="+weixiu_status
    			+"&guiwei="+guiwei+"&chanpinxian="+chanpinxian+"&chanpinxian1="+chanpinxian1+"&jinzhong_begin="+jinzhong_begin+"&jinzhong_end="+jinzhong_end+"&zhushi_begin="+zhushi_begin+"&zhushi_end="+zhushi_end;
	  			
    url = "index.php?mod=warehouse&con=WarehouseGoods&act=search"+args;
	window.open(url);
}
//婚博会专用导出
function hbhdownload(){
	var hbh = 1;
	var down_info = 'down_info';
    var goods_id = $("#warehouse_goods_search_form [name='goods_id']").val();
    var style_sn = $("#warehouse_goods_search_form [name='style_sn']").val();
    var put_in_type = $("#warehouse_goods_search_form [name='put_in_type']").val();
    var weixiu_status = $("#warehouse_goods_search_form [name='weixiu_status']").val();
    var is_on_sale = $("#warehouse_goods_search_form [name='is_on_sale']").val();
    var zhuchengse = $("#warehouse_goods_search_form [name='zhuchengse']").val();
    var company_id = $("#warehouse_goods_search_form [name='company_id']").val();
    var warehouse_id = $("#warehouse_goods_search_form [name='warehouse_id']").val();
    var cat_type = $("#warehouse_goods_search_form [name='cat_type']").val();
    var cat_type1 = $("#warehouse_goods_search_form [name='cat_type1']").val();
    var zhengshuhao = $("#warehouse_goods_search_form [name='zhengshuhao']").val();
    var order_goods_ids = $("#warehouse_goods_search_form [name='order_goods_ids']").val();
    var shoucun = $("#warehouse_goods_search_form [name='shoucun']").val();
    var kucun_start = $("#warehouse_goods_search_form [name='kucun_start']").val();
    var kucun_end = $("#warehouse_goods_search_form [name='kucun_end']").val();
	var processor = $("#warehouse_goods_search_form [name='processor']").val();
    var buchan = $("#warehouse_goods_search_form [name='buchan']").val();
    var maohao = $("#warehouse_goods_search_form [name='maohao']").val();

    var zhushi = $("#warehouse_goods_search_form [name='zhushi']").val();
    var zhengshu_type = $("#warehouse_goods_search_form [name='zhengshu_type']").val();
    var zs_color = $("#warehouse_goods_search_form [name='zs_color']").val();
    var zs_clarity = $("#warehouse_goods_search_form [name='zs_clarity']").val();
    var jinshi_type = $("#warehouse_goods_search_form [name='jinshi_type']").val();
    var jintuo_type = $("#warehouse_goods_search_form [name='jintuo_type']").val();
    var jiejia = $("#warehouse_goods_search_form [name='jiejia']").val();
    var guiwei = $("#warehouse_goods_search_form [name='guiwei']").val();
    var chanpinxian = $("#warehouse_goods_search_form [name='chanpinxian']").val();
    var chanpinxian1 = $("#warehouse_goods_search_form [name='chanpinxian1']").val();
    var jinzhong_begin = $("#warehouse_goods_search_form [name='jinzhong_begin']").val();
    var jinzhong_end = $("#warehouse_goods_search_form [name='jinzhong_end']").val();
    var zhushi_begin = $("#warehouse_goods_search_form [name='zhushi_begin']").val();
    var zhushi_end = $("#warehouse_goods_search_form [name='zhushi_end']").val();
    var weixiu_status = $("#warehouse_goods_search_form [name='weixiu_status']").val();
	  /*if (warehouse_id == '' ){
		 alert("未选择婚博会柜面库房，不能导出！"); 
		 exit;
	  }  */   
	/*var url = 'index.php?mod=warehouse&con=WarehouseGoods&act=warehouseidinfo';
	 $.post(url,{'warehouse_id':warehouse_id},function(e){ */
		/* if(e.success == 1){ */
				var args = "&hbh="+hbh+"&goods_id="+goods_id+"&style_sn="+style_sn+"&put_in_type="+put_in_type+"&is_on_sale="+is_on_sale+"&zhuchengse="+zhuchengse+"&company_id="+company_id+"&warehouse_id="+warehouse_id+"&cat_type="+cat_type+"&cat_type1="+cat_type1
			+"&zhengshuhao="+zhengshuhao+"&order_goods_ids="+order_goods_ids+"&shoucun="+shoucun+"&kucun_start="+kucun_start+"&kucun_end="+kucun_end+"&processor="+processor+"&buchan="+buchan+"&maohao="+maohao
			+"&zhushi="+zhushi+"&zhengshu_type="+zhengshu_type+"&zs_color="+zs_color+"&zs_clarity="+zs_clarity+"&jinshi_type="+jinshi_type+"&jintuo_type="+jintuo_type+"&jiejia="+jiejia+"&weixiu_status="+weixiu_status
			+"&guiwei="+guiwei+"&chanpinxian="+chanpinxian+"&chanpinxian1="+chanpinxian1+"&jinzhong_begin="+jinzhong_begin+"&jinzhong_end="+jinzhong_end+"&zhushi_begin="+zhushi_begin+"&zhushi_end="+zhushi_end;
			url = "index.php?mod=warehouse&con=WarehouseGoods&act=search"+args;
			window.open(url);

/* 		}else{
			alert("仓库非婚博会柜面，不能导出！");
			die;
		} 
	})*/
}
