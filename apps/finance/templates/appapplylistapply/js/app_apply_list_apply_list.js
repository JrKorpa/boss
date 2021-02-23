//分页
function app_apply_list_apply_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=AppApplyListApply&act=search');//设定刷新的初始url
	util.setItem('formID','app_apply_list_apply_search_form');//设定搜索表单id
	util.setItem('listDIV','app_apply_list_apply_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#app_apply_list_apply_search_form select[name="company"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_apply_list_apply_search_form select[name="status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_apply_list_apply_search_form select[name="prc_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_apply_list_apply_search_form select[name="payType"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
				//时间控件
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
			util.closeForm(util.getItem("formID"));
			app_apply_list_apply_search_page(util.getItem("orl"));
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

function OnReal()//生成应付单
{
	var ids = Array();
	$("input[name='_ids[]']").each(function(i, o){
		if($(o).attr("checked") == 'checked')
		{
			ids.push($(o).val())
		}
	});
	if(ids.length == 0)
	{
		bootbox.alert("没有选中数据");
	}
	id = ids.join();

	$.post('?mod=finance&con=AppApplyListApply&act=shouldAddCheck',{id:id},function(data){
		$('.modal-scrollable').trigger('click');
		if(data.success==1){
			bootbox.confirm("应付金额总计："+data.total+"元，请确认是否生成财务应付单？", function(result) {
				if (result == true) {
					setTimeout(function(){
						$.post('?mod=finance&con=AppApplyListApply&act=shouldAddSub',{id:id},function(res){
							$('.modal-scrollable').trigger('click');
							bootbox.alert(res.error);
							if(res.success==1){
								bootbox.alert('提交成功');
								$('.modal-scrollable').trigger('click');
								util.retrieveReload();
								util.syncTab(tab_id);
							//	PurchaseInfoObj.init();
							}
							else{
								bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
							}
						});
					}, 0);
				}
			});
		}
		else{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}
	});


/*	var url = '?mod=cash&con=payshould&act=shouldAddCheck';
	$.post(url, {ids:ids},function(res){//第一次调用ajax先检查数据
		if(!res.success)
		{
			alert(res.error);
		}else{
			if(confirm("应付金额总计："+res.total+"元，请确认是否生成财务应付单？"))
			{
				//第二次调用ajx正式提交
				var url1 = '?mod=cash&con=payshould&act=shouldAddSub';
				$.post(url1, {ids:ids},function(res){
					alert(res.error);
					if(res.success)
					{
						location='index.php?mod=cash&con=payshould&act=should_info&info_id='+res.id;
					}
				});
					
			}
		}
	  });
*/	
}