//分页
function app_receive_apply_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=AppReceiveApply&act=search');//设定刷新的初始url
	util.setItem('formID','app_receive_apply_search_form');//设定搜索表单id
	util.setItem('listDIV','app_receive_apply_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#app_receive_apply_search_form select[name="cash_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            });

			$('#app_receive_apply_search_form select[name="from_ad"]').select2({
                placeholder: "请选择",
                allowClear: true
            });

			$('#app_receive_apply_search_form select[name="status"]').select2({
                placeholder: "请选择",
                allowClear: true
            });

			$('#app_receive_apply_search_form select[name="storage_mode[]"]').select2({
                placeholder: "请选择",
                allowClear: true
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


			$('#app_receive_apply_search_form :reset').on('click',function(){
				$('#app_receive_apply_search_form select[name="cash_type"]').select2('val','');
				$('#app_receive_apply_search_form select[name="from_ad"]').select2('val','');
				$('#app_receive_apply_search_form select[name="status"]').select2('val','');
				$('#app_receive_apply_search_form select[name="storage_mode[]"]').select2('val','');
				
			})
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_receive_apply_search_page(util.getItem("orl"));
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


function download() {
	var formdata = $("#app_receive_apply_search_form").serialize();
    location.href = "index.php?mod=finance&con=AppReceiveApply&act=downLoad&"+formdata;
}

function checkShould(obj)
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
	
	var url = '?mod=finance&con=AppReceiveShould&act=shouldAddCheck';
	var tab_id = $(obj).attr('list-id');
	$.post(url, {ids:ids},function(data){//第一次调用ajax先检查数据
		if(!data.success)
		{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}else{
			
			bootbox.confirm("应付金额总计："+data.total+"元，请确认是否生成财务应付单？", function(result) {
				if (result == true) {
					//第二次调用ajx正式提交
					var url1 = '?mod=finance&con=AppReceiveShould&act=shouldAddSub';
					setTimeout(function(){
						$.post(url1,{ids:ids},function(res){
							$('.modal-scrollable').trigger('click');
							bootbox.alert(res.error);
						});
					}, 0);
				}
			});
			
		}
	  });
	
}