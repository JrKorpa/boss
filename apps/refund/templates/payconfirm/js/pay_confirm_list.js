//分页
function pay_confirm_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=refund&con=PayConfirm&act=search');//设定刷新的初始url
	util.setItem('formID','pay_confirm_search_form');//设定搜索表单id
	util.setItem('listDIV','pay_confirm_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
			
			var initElements = function(){
				$('#pay_confirm_search_form select[name="return_type"]').select2({
					placeholder: "请选择",
					allowClear: true
				}).change(function(e) {
					$(this).valid();
				});
				$('#pay_confirm_search_form select[name="department"]').select2({
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
					$('body').removeClass("modal-open");
				}
			};
			
			var handleForm = function(){
				util.search();
			};
			
			var initData = function(){
				util.closeForm(util.getItem("formID"));
                pay_confirm_search_page(util.getItem("orl"));
				$('#pay_confirm_search_form button[type="reset"]').on('click',function(){
                    $('#pay_confirm_search_form input').empty();
                    $('#pay_confirm_search_form select').select2('val','');
	            });

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