//分页
function processor_in_account_search_page(url){
	util.page(url);
}


//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=ProcessorInAccount&act=search');//设定刷新的初始url
	util.setItem('formID','processor_in_account_search_form');//设定搜索表单id
	util.setItem('listDIV','processor_in_account_search_list');//设定列表数据容器id
	

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			//日期
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			//下拉列表美化
			$('#processor_in_account_search_form select').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e) {
				$(this).valid();
			});

		};
		var handleForm = function(){ 
			util.search()
		};
		var initData = function(){
			//下拉重置
			$('#processor_in_account_search_form :reset').on('click',function(){
				$('#processor_in_account_search_form select').select2("val","");
			});
			util.closeForm(util.getItem("formID"));
			processor_in_account_search_page(util.getItem('orl'));
			
			$('#processor_in_account_export_btn').on('click', function(){
				//var company = $('#processor_in_account_search_form select[name=company]').val();
                var pay_channel = $('#processor_in_account_search_form select[name=pay_channel]').val();
				var fin_status = $('#processor_in_account_search_form select[name=fin_status]').val();
                var account_type = $('#processor_in_account_search_form select[name=account_type]').val();
				var make_time_start = $('#processor_in_account_search_form input[name=make_time_start]').val();
				var make_time_end = $('#processor_in_account_search_form input[name=make_time_end]').val();
				var check_time_start = $('#processor_in_account_search_form input[name=check_time_start]').val();
				var check_time_end = $('#processor_in_account_search_form input[name=check_time_end]').val();
                var fin_check_time_start = $('#processor_in_account_search_form input[name=finance_check_time_start]').val();
                var fin_check_time_end = $('#processor_in_account_search_form input[name=finance_check_time_end]').val();
                var put_in_type = $('#processor_in_account_search_form select[name=put_in_type]').val();
				var down_url = 'index.php?mod=finance&con=ProcessorInAccount&act=downCsv&pay_channel='+pay_channel+'&fin_status='+fin_status+'&account_type='+account_type+'&check_time_start='+check_time_start+'&check_time_end='+check_time_end+'&make_time_start='+make_time_start+'&make_time_end='+make_time_end+'&fin_check_time_start='+fin_check_time_start+'&fin_check_time_end='+fin_check_time_end+'&put_in_type='+put_in_type;
                                    
                            window.open(down_url) 
			})
		};
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