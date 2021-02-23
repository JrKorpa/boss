//分页
function batch_express_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
    util.setItem('orl','index.php?mod=shipping&con=BatchExpress&act=search');//设定刷新的初始url
	util.setItem('formID','batch_express_search_form');//设定搜索表单id
	util.setItem('listDIV','batch_express_search_list');//设定列表数据容器id

	//匿名函数+闭包  
	var ListObj = function(){
		
		var initElements = function(){
			
			$('#batch_express_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#batch_express_search_form :reset').on('click',function(){
				$('#batch_express_search_form select').select2("val","");
			});
			
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					//rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
            util.closeForm(util.getItem("formID"));
            $('#'+util.getItem('formID')).submit();
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();
			}
		}	
	}();

	ListObj.init();
});
//全选 反选
function checkboxes(obj) {
	var test =$(obj).attr('checked');
	//选中 都选中
	if(test=='checked'){
		var chk_value =[]; 
		$("#batch_express_search_list [name='_ids[]']").each(function(){ 
			chk_value.push($(this).attr('checked',true)); 
		}); 

	}else{
		var chk_value =[]; 
		$("#batch_express_search_list [name='_ids[]']").each(function(){ 
			chk_value.push($(this).attr('checked',false));
		}); 
	}

}


//批量操作处理
function batchConfirms(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}

	var chk_value =[]; 
	$("#batch_express_search_list [name='_ids[]']:checked").each(function(){ 
		chk_value.push($(this).val()); 
	}); 
	if(chk_value.length<=0){
		util.xalert("最少选择一条数据！");return false;
	}
	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	util._pop(url,{id:_id,'chk_value':chk_value,'tab_id':$(obj).attr("list-id")});//tab-id是主记录的列表
}
