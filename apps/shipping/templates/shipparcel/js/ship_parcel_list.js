function checkUser(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var id = tObj[0].getAttribute("data-id").split('_').pop();
	$.get('index.php?mod=shipping&con=ShipParcel&act=checkUser&id='+id, '' , function(res){
		$('body').modalmanager('loading');//进度条和遮罩
		if(res.success == 1){
			var tObja = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
			util._pop($(obj).attr('data-url'),{id:tObja[0].getAttribute("data-id").split('_').pop()});
		}else{
			util.xalert(res.error);
		}
		$('.modal-scrollable').trigger('click');//关闭遮罩
		$('body').modalmanager('removeLoading');//关闭进度条
	})
}

//打印
function print_baoguo(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var id = tObj[0].getAttribute("data-id").split('_').pop();

	var url = $(obj).attr('data-url');
	var _name = $(obj).attr('data-title');

	var son = window.open($(obj).attr('data-url')+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);
	};

}

//分页
function ship_parcel_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js',
         "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
         "public/js/bootstrap-datepicker/js/bootstrap-datepicker.js"
],function(){
	util.setItem('orl','index.php?mod=shipping&con=ShipParcel&act=search');//设定刷新的初始url
	util.setItem('formID','ship_parcel_search_form');//设定搜索表单id
	util.setItem('listDIV','ship_parcel_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
                        //初始化下拉组件
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			$('#ship_parcel_search_form select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			ship_parcel_search_page(util.getItem("orl"));
			$('#ship_parcel_search_form :reset').on('click',function(){
				//下拉重置
				$('#ship_parcel_search_form select[name="express_id"]').select2("val", '');
				$('#ship_parcel_search_form select[name="send_status"]').select2("val", '');
				$('#ship_parcel_search_form select[name="is_print"]').select2("val", '');
			})
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
function mutiPrintParcelList11(obj)
{
    
    var url=$(obj).attr('data-url');
    var temp=''
    var ids=$('#ship_parcel_search_list input[name="_ids[]"]:checked').each(
                function(){
                    temp+=$(this).val()+',';
                }
            );
	if(temp.length==0)
	{
		util.xalert("请先选中你要打印的包裹！");
		return false;
	}
    url+='&ids='+temp.substr(0,temp.length-1);


    //location.href=url;


	var son = window.open(url,'','fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);
	};
}