//分页
function app_adj_apply_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=AppAdjApply&act=search');//设定刷新的初始url
	util.setItem('formID','app_adj_apply_search_form');//设定搜索表单id
	util.setItem('listDIV','app_adj_apply_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#app_adj_apply_search_form select[name="company"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});		
			$('#app_adj_apply_search_form select[name="status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});		
			$('#app_adj_apply_search_form select[name="payType"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});	
			$('#app_adj_apply_search_form select[name="prc_id"]').select2({
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
			app_adj_apply_search_page(util.getItem("orl"));
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