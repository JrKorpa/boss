function warehouse_bill_info_b_b_search_page(url){
    util.page(url);
}


function printBill_b(obj){
	var bill_id=<%$view_bill->get_id()%>;	
	$.post("index.php?mod=warehouse&con=WarehouseBillInfoB&act=printBill",{id:bill_id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = bill_id;
			var url = "index.php?mod=warehouse&con=WarehouseBillInfoB&act=printBill";
			var _name = "打印单据";
			var son = window.open(url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){

			};
		}
	});
}

/** 审核单据 **/
function checkBillB(obj){
    $('body').modalmanager('loading');
    var url =$(obj).attr('data-url') ;
    var id = '<%$view_bill->get_id()%>';
    var bill_no = '<%$view_bill->get_bill_no()%>';

    bootbox.confirm("确定审核吗?", function(result) {
        if (result == true) {
            $.post(url,{id:id,bill_no:bill_no},function(data){
                $('.modal-scrollable').trigger('click');
                if(data.success==1){
                    bootbox.alert({
                        message: data.error,
                        buttons: {
                            ok: {
                                label: '确定'
                            }
                        },
                        animate: true,
                        closeButton: false,
                        title: "提示信息" ,
                    });
                    util.retrieveReload();
                    return false;
                }
                else{
                    bootbox.alert({
                        message: data.error,
                        buttons: {
                            ok: {
                                label: '确定'
                            }
                        },
                        animate: true,
                        closeButton: false,
                        title: "提示信息" ,
                    });
                    return false;
                }
            });
        }
    });
}
var  bill_b_goods_id='<%$view_bill->get_id()%>';
       // debugger;
$import("public/js/jquery-zero/ZeroClipboard.min.js",function(){
    util.setItem('orl','index.php?mod=warehouse&con=WarehouseBillInfoB&act=search&id='+bill_b_goods_id);//设定刷新的初始url
    //util.setItem('formID','button_search_form');//设定搜索表单id
    util.setItem('listDIV','warehouse_info_b_goods_list');//设定列表数据容器id
    var Obj = function(){
        var initElements = function(){
            
        };
        var handleForm = function(){
            util.search();
        }
        var initData = function(){
            warehouse_bill_info_b_b_search_page(util.getItem("orl"));
                var url = 'index.php?mod=warehouse&con=WarehouseBillPay&act=show';
                $.post(url,{bill_id:bill_b_goods_id},function(data){
                        if(data.success ==1){
                                $('#warehouse_bill_pay_s_b').html(data.content);
                        }
                        else{
                                bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
                        }
            });
            //批量复制货号
            util.batchCopyGoodsid('<%$view_bill->get_id()%>','batch_copy_goodsid_b');
            warehouse_bill_info_b_b_search_page(util.getItem("orl"));
        };
        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();

    Obj.init();
});