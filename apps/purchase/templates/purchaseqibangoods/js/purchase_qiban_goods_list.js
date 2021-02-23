//停用
function stop(obj){
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	
	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	$.get(url+'&id='+objid , '' , function(res){
		$('.modal-scrollable').trigger('click');
		if(res.success == 1){
			util.xalert(
					res.error,
					function(){
						util.sync(obj);
			});
		}else{
			util.error(res);
		}
	})
}
//启用
function start(obj){
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	
	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	$.get(url+'&id='+objid , '' , function(res){
		$('.modal-scrollable').trigger('click');
		if(res.success == 1){
			util.xalert(
					res.error,
					function(){
						util.sync(obj);
			});
		}else{
			util.error(res);
		}
		
	})
}

//分页
function purchase_qiban_goods_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function(){
	util.setItem('orl','index.php?mod=purchase&con=PurchaseQibanGoods&act=search');//设定刷新的初始url
	util.setItem('formID','purchase_qiban_goods_search_form');//设定搜索表单id
	util.setItem('listDIV','purchase_qiban_goods_search_list');//设定列表数据容器id

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
            
			$('#purchase_qiban_goods_search_form select').select2({
				placeholder: "请选择",
				allowClear: true,
//				minimumInputLength: 2
			}).change(function(e){
				$(this).valid();
			});	
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			purchase_qiban_goods_search_page(util.getItem("orl"));

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

//导出
function qiban_download(){
    var qiban_download = 'qiban_download';
    var addtime = $("#purchase_qiban_goods_search_form [name='addtime']").val();
    var order_sn = $("#purchase_qiban_goods_search_form [name='order_sn']").val();
    var customer = $("#purchase_qiban_goods_search_form [name='customer']").val();
    var status = $("#purchase_qiban_goods_search_form [name='status']").val();
    var price_min = $("#purchase_qiban_goods_search_form [name='price_min']").val();
    var price_max = $("#purchase_qiban_goods_search_form [name='price_max']").val();
    var xiangkou_min = $("#purchase_qiban_goods_search_form [name='xiangkou_min']").val();
    var xiangkou_max = $("#purchase_qiban_goods_search_form [name='xiangkou_max']").val();
    var shoucun_min = $("#purchase_qiban_goods_search_form [name='shoucun_min']").val();
    var shoucun_max = $("#purchase_qiban_goods_search_form [name='shoucun_max']").val();
    var fuzhu = $("#purchase_qiban_goods_search_form [name='fuzhu']").val();
    var gongchang = $("#purchase_qiban_goods_search_form [name='gongchang']").val();
    var kuanhao = $("#purchase_qiban_goods_search_form [name='kuanhao']").val();
    var zhengshu = $("#purchase_qiban_goods_search_form [name='zhengshu']").val();
    var start_time = $("#purchase_qiban_goods_search_form [name='start_time']").val();
    var end_time = $("#purchase_qiban_goods_search_form [name='end_time']").val();
    var xuqiu = $("#purchase_qiban_goods_search_form [name='xuqiu']").val();
    var kuan_type = $("#purchase_qiban_goods_search_form [name='kuan_type']").val();
    var qiban_type = $("#purchase_qiban_goods_search_form [name='qiban_type']").val();
    var jinliao = $("#purchase_qiban_goods_search_form [name='jinliao']").val();
    var jinse = $("#purchase_qiban_goods_search_form [name='jinse']").val();
    var gongyi = $("#purchase_qiban_goods_search_form [name='gongyi']").val();
    var opt = $("#purchase_qiban_goods_search_form [name='opt']").val();
    var info = $("#purchase_qiban_goods_search_form [name='info']").val();

    if(!addtime && !order_sn && !customer && !status && !price_min && !price_max && !xiangkou_min && !xiangkou_max && !shoucun_min && !shoucun_max && !fuzhu && !gongchang && !kuanhao && !zhengshu && !start_time && !end_time && !xuqiu && !kuan_type && !qiban_type && !jinliao && !jinse && !gongyi && !opt && !info){
        if(!confirm('没有导出限制可能会消耗较长的时间，点击‘确定’继续！')){
            return false;
        }   
    }
    var args = "&qiban_download="+qiban_download+"&addtime="+addtime+"&order_sn="+order_sn+"&customer="+customer+"&status="+status+"&price_min="+price_min+"&price_max="+price_max+"&xiangkou_min="+xiangkou_min+"&xiangkou_max="+xiangkou_max+"&shoucun_min="+shoucun_min+"&shoucun_max="+shoucun_max+"&fuzhu="+fuzhu+"&gongchang="+gongchang+"&kuanhao="+kuanhao+"&start_time="+start_time+"&end_time="+end_time+"&xuqiu="+xuqiu+"&kuan_type="+kuan_type+"&qiban_type="+qiban_type+"&jinliao="+jinliao+"&jinse="+jinse+"&gongyi="+gongyi+"&opt="+opt+"&info="+info;
    location.href = "index.php?mod=purchase&con=PurchaseQibanGoods&act=search"+args;

}