function app_order_details_search_page(url){
	util.page(url,1);
}

function app_order_invoice_search_page(url){
	util.page(url,2);
}

function app_order_address_search_page(url){
	util.page(url,3);
}

function app_order_action_search_page(url){
	util.page(url,4);
}

 var  order_id='<%$order.id%>';
function add_order_address(obj){
    var tObj = $(obj).parent().parent().parent().find('.flip-scroll>table>tbody>.tab_click');
    if (!tObj.length)
    {
        $('.modal-scrollable').trigger('click');
        util.xalert("�ܱ�Ǹ������ǰδѡ���κ�һ�У�");
        return false;
    }

    var url = $(obj).attr('data-url');
    var _id = tObj[0].getAttribute("data-id").split('_').pop();
    util._pop(url,{id:_id,'tab_id':$(obj).attr("list-id"),order_id:order_id});//tab-id������¼���б�
}

 var  member_id='<%$user_info.member_id%>';
 function add_member_address(o){
     var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
     util._pop($(o).attr('data-url'),{_id:_id,member_id:member_id});
}

function changeEx(o){
    var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
    util._pop($(o).attr('data-url'),{_id:_id});
}

function syc_address(o){
    var el = $(o).parent().parent().parent().find('.flip-scroll');
    if($(o).parent().parent().parent().find('.portlet .portlet-title').find("a.collapse").length>0){
        $(o).parent().parent().parent().find('.portlet .portlet-title').click();
    }
    var url = $(o).attr("data-url");
    var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
    url+='&_id='+_id;
    if (url) {
        App.blockUI({target: el, iconOnly: true});
        $.ajax({
            type: "POST",
            cache: false,
            data:{member_id:member_id},
            url: url,
            dataType: "html",
            success: function(res)
            {
                App.unblockUI(el);
                el.html(res);
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                App.unblockUI(el);
                var msg = 'Error on reloading the content. Please check your connection and try again.';
                util.xalert(msg);
                return false;
            }
        });
    } else {
        // for demo purpose
        App.blockUI({target: el, iconOnly: true});
        window.setTimeout(function () {
            App.unblockUI(el);
        }, 1000);
    }
}

function CopeOrderInfo(obj){
    $('body').modalmanager('loading');
    var url =$(obj).attr('data-url') ;
    var objid = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
    var _name = $(obj).attr('data-title');
    if (!_name)
    {
        _name='';
    }
    bootbox.confirm({
        buttons: {
            confirm: {
                label: 'ȷ��'
            },
            cancel: {
                label: '����'
            }
        },
        message: "ȷ��"+_name+"?",
        closeButton: false,
        callback: function(result) {
            if (result == true) {
                $('body').modalmanager('loading');
                setTimeout(function(){
                    $.post(url,{id:objid},function(data){

                        if(data.success==1)
                        {
                            $('.modal-scrollable').trigger('click');
                            util.xalert("�����ɹ� ���ɶ�����Ϊ "+data.error,function(){
                                util.retrieveReload(obj);
                            });
                        }
                        else
                        {
                            util.error(data);
                        }
                    });
                }, 0);
            }
        },
        title: "��ʾ��Ϣ",
    });
}



$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"],function(){
    var member_id ='<%$user_info.member_id%>';
    var order_sn ='<%$order.order_sn%>';
    var order_id ='<%$order.id%>';
	util.setItem('orl1','index.php?mod=sales&con=AppOrderDetails&act=search&order_id='+order_id+'&_id='+getID().split('-').pop());//�趨ˢ�µĳ�ʼurl
	util.setItem('listDIV1','app_old_order_detail');
	util.setItem('orl2','index.php?mod=sales&con=AppOrderInvoice&act=search&order_id='+order_id+'&_id='+getID().split('-').pop());//�趨ˢ�µĳ�ʼurl
	util.setItem('listDIV2','app_old_order_invoice_detail');
    
	util.setItem('orl4','index.php?mod=sales&con=BaseOrderInfo&act=showLogs&id='+order_id+'&appol=1');//�趨ˢ�µĳ�ʼurl
	util.setItem('listDIV4','app_old_order_search');


	var obj1 = function(){
		var initElements = function(){
            // ���ͼƬ������ͼ
	        $(".fancyboximg").fancybox({
                wrapCSS    : 'fancybox-custom',
                closeClick : true,
                openEffect : 'none',
                helpers : {
                    title : {
                        type : 'inside'
                    },
                    overlay : {
                        css : {
                            'background' : 'rgba(0,0,0,0.6)'
                        }
                    }
                }
            });				
		}

		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				initElements();//����������Ԫ�غ�����
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_order_details_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();

	var obj2 = function(){
		var handleForm1 = function(){
			util.search(2);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_order_invoice_search_page(util.getItem('orl2'));
			}
		}
	
	}();

	obj2.init();

	var obj3 = function(){
		var handleForm1 = function(){
			util.search(3);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_order_address_search_page(util.getItem('orl3'));
			}
		}
	
	}();

	obj3.init();
    
    //������־
	var obj4 = function(){
		var handleForm1 = function(){
			util.search(4);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_order_action_search_page(util.getItem('orl4'));
			}
		}
	
	}();

	obj4.init();


});