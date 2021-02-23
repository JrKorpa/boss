//分页
function app_apply_bills_search_page(url){
	util.page(url);
}

//导出
function exportData(){
	var down_info = 'down_info';
    var process_list = $("#app_apply_bills_search_form [name='process_list']").val();
    var applyorderdostatus = $("#app_apply_bills_search_form [name='applyorderdostatus']").val();
    
    var pay_apply_number = $("#app_apply_bills_search_form [name='pay_apply_number']").val();
    var totalpaytype = $("#app_apply_bills_search_form [name='totalpaytype']").val();
    var pay_number = $("#app_apply_bills_search_form [name='pay_number']").val();
    var fapiao = $("#app_apply_bills_search_form [name='fapiao']").val();
    var start_make_date = $("#app_apply_bills_search_form [name='start_make_date']").val();
    var end_make_date = $("#app_apply_bills_search_form [name='end_make_date']").val();
    var start_check_date = $("#app_apply_bills_search_form [name='start_check_date']").val();
    var end_check_date = $("#app_apply_bills_search_form [name='end_check_date']").val();
    var record_type = $("#app_apply_bills_search_form [name='record_type']").val();
    var style_type = $("#app_apply_bills_search_form [name='style_type']").val();
    var args = "&down_info="+down_info+"&applyorderdostatus="+applyorderdostatus+"&process_list="+process_list+"&pay_apply_number="+pay_apply_number+"&totalpaytype="+totalpaytype+"&pay_number="+pay_number+
    "&start_make_date="+start_make_date+"&end_make_date="+end_make_date+"&start_check_date="+start_check_date+"&end_check_date="+end_check_date+
    "&fapiao="+fapiao+"&record_type="+record_type+"&style_type="+style_type					
    location.href = util.getItem("orl")+args;
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=PayApply&act=search');//设定刷新的初始url
	util.setItem('formID','app_apply_bills_search_form');//设定搜索表单id
	util.setItem('listDIV','app_apply_bills_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
			$('#app_apply_bills_search_form select').select2({
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
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			//下拉重置
			$('#app_apply_bills_search_form :reset').on('click',function(){
				$('#app_apply_bills_search_form select').select2("val","");
			});
			util.closeForm(util.getItem("formID"));
			app_apply_bills_search_page(util.getItem("orl"));
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

function select_checkbox(sel)
{
	$("input[name='checkboxs[]']").each(function(i, o){
		$(o).attr("checked",sel);
	});
}

function OnReal(obj)//生成应付单
{
	var ids = Array();
	$("input[name='checkboxs[]']").each(function(i, o){
		if($(o).attr("checked") == 'checked')
		{
			ids.push($(o).val())
		}
	});
	if(ids.length == 0)
	{
		bootbox.alert("没有选中任何数据");return;
	}
	ids = ids.join();
	
	var url = '?mod=finance&con=PayShould&act=shouldAddCheck';
	var tab_id = $(obj).attr('list-id');
	$.post(url, {ids:ids},function(data){//第一次调用ajax先检查数据
		if(!data.success)
		{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}else{
			
			bootbox.confirm("应付金额总计："+data.total+"元，请确认是否生成财务应付单？", function(result) {
				if (result == true) {
					//第二次调用ajx正式提交
					var url1 = '?mod=finance&con=PayShould&act=shouldAddSub';
					setTimeout(function(){
						$.post(url1,{ids:ids},function(res){
							$('.modal-scrollable').trigger('click');
							bootbox.alert(res.error);
							util.syncTab(tab_id);
						});
					}, 0);
				}
			});
			
		}
	  });
	
}