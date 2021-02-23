//分页
function base_order_info_search_page(url){
	util.page(url);
}

function bufa_func(obj){
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
    var _name = $(obj).attr('name');
    if (!_name)
    {
        _name='';
    }

    bootbox.confirm({
        buttons: {
            confirm: {
                label: '确认'
            },
            cancel: {
                label: '放弃'
            }
        },
        message: "确定"+_name+"?",
        closeButton: false,
        callback: function(result) {
            if (result == true) {
                $('body').modalmanager('loading');
                setTimeout(function(){
                    $.post(url,{id:objid},function(data){
                        if(data.success==1)
                        {
                            $('.modal-scrollable').trigger('click');
                            util.xalert("操作成功 生成的订单号为"+data.error,function(){
                                util.sync(obj);
                            });
                            return ;
                        }
                        else{
                            util.error(data);
                        }
                    });
                }, 0);
            }
        },
        title: "提示信息",
    });
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css"],function(){
	var url="index.php?mod=report&con=ReportBaseOrderInfo&act=search&start_time="+start_time+"&end_time="+end_time+"&time_type="+time_type+"&buchan_type="+buchan_type+"&order_type="+order_type+"&order_department="+order_department+"&is_delete="+is_delete+"&order_pay_status="+order_pay_status;
	url+='&send_good_status='+send_good_status+'&delivery_status='+delivery_status+'&diff_day='+diff_day+'&channel_class='+channel_class+'&department_id='+department_id;
	util.setItem('orl',url);//设定刷新的初始url
	util.setItem('formID','report_base_order_info_search_form');//设定搜索表单id
	util.setItem('listDIV','report_base_order_info_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#report_base_order_info_search_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });

				$('#report_base_order_info_search_form select[name="order_pay_status"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
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


			$('#report_base_order_info_search_form :reset').on('click',function(){
				$('#report_base_order_info_search_form select').select2("val","");
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			base_order_info_search_page(util.getItem("orl"));
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


function printorder(obj){

	var tObj = $(obj).parent().parent().next().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var order_id=$(tObj).attr('id');
		$.post("index.php?mod=sales&con=BaseOrderInfo&act=printorder",{id:order_id},function(res){
    				if(res.error){
    					alert(res.error);	
    				}else{

    					var id = tObj[0].getAttribute("data-id").split('_').pop();

						var url = $(obj).attr('data-url');
						var _name = $(obj).attr('data-title');

						var son = window.open(
						$(obj).attr('data-url')+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes'
						);
							son.onUnload = function(){
						util.sync(obj);
						};



    				}
 		 });


}


function printorder_dz(obj){

    var tObj = $(obj).parent().parent().next().find('table>tbody>.tab_click');
    if (!tObj.length)
    {
        $('.modal-scrollable').trigger('click');
        util.xalert("很抱歉，您当前未选中任何一行！");
        return false;
    }
    var order_id=$(tObj).attr('id');
        $.post("index.php?mod=sales&con=BaseOrderInfo&act=printorder_dz",{id:order_id},function(res){
                    if(res.error){
                        alert(res.error);   
                    }else{

                        var id = tObj[0].getAttribute("data-id").split('_').pop();

                        var url = $(obj).attr('data-url');
                        var _name = $(obj).attr('data-title');

                        var son = window.open(
                        $(obj).attr('data-url')+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes'
                        );
                            son.onUnload = function(){
                        util.sync(obj);
                        };



                    }
         });


}

//订单号 批量搜索
function bachstyle(){

    var col = $("#bachstyle4").attr('class');
    if(col=='col-sm-4'){
        $("#bachstyle4").attr('class','col-sm-9');
        $("#order_sn").attr('placeholder','输入多个订单号时,请用英文模式逗号分隔！');
    }
    if(col=='col-sm-9'){
        $("#bachstyle4").attr('class','col-sm-4');
        $("#order_sn").attr('placeholder','双击可批量输入订单号');
    }
    
}