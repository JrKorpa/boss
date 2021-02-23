//分页
function jxs_profit_order_search_page(url){
	util.page(url);
}

function open_sub_table(obj){
    if($(obj).find('i').hasClass('fa-plus')){
        $(obj).find('i').removeClass('fa-plus').addClass('fa-minus');
        $(obj).parent().parent().next().show();
    }else{
        $(obj).find('i').removeClass('fa-minus').addClass('fa-plus');
        $(obj).parent().parent().next().hide();
    }
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=JxsProfitOrder&act=search');//设定刷新的初始url
	util.setItem('formID','jxs_profit_order_search_form');//设定搜索表单id
	util.setItem('listDIV','jxs_profit_order_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            $('#jxs_profit_order_search_form select').select2({
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

            $('#jxs_profit_order_search_form :reset').on('click',function(){
                $('#jxs_profit_order_search_form select').select2("val","");
                $('#jxs_profit_order_search_form input[name="start_time"]').val("");
                $('#jxs_profit_order_search_form input[name="end_time"]').val("");
                $('#jxs_profit_order_search_form input[name="start_money"]').val("");                
                $('#jxs_profit_order_search_form input[name="end_money"]').val("");
            })
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			jxs_profit_order_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

function execHandler(act,ajax) {
	if (!act) return;
	var args ='';
	$('#jxs_profit_order_search_form').find('select').each(function(){
		var el = $(this);
		args += ('&'+ el.attr('name') + '=' + el.attr('value'));
	});
	$('#jxs_profit_order_search_form').find('input').each(function(){
		var el = $(this);
		if (el.attr('name')) args += ('&'+ el.attr('name') + '=' + el.attr('value'));
	});
	if (ajax) {
		bootbox.confirm({  
			buttons: {  
				confirm: {  label: '确认' },  
				cancel: {  label: '放弃' }  
			},  
			message: "确定继续?",
			closeButton: false,
			callback: function(result) {  
				if (result == true) {
					$.ajax({
			            type: 'POST',
			            url: "index.php?mod=finance&con=JxsProfitOrder&act="+act+args,
			            dataType: 'json',
			            success:function(data) {
			                //console.log(data);
			                if (data.success == 1) {
			                	util.xalert("操作成功");
			                	util.page("index.php?mod=finance&con=JxsProfitOrder&act=search"+args);
			                } else {
			                	util.xalert(data.error);
			                }
			            }
			        });
				}
			},  
			title: "提示信息", 
		});
	} else {
		location.href = "index.php?mod=finance&con=JxsProfitOrder&act="+act+args;
	}
}