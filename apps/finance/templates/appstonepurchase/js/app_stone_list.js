//分页
function app_stone_detail_search_page(url){
	util.page(url);
}
function bach_search_num(){

	var col = $("#bacheitem").attr('class');
	if(col=='col-sm-3'){
		$("#bacheitem").attr('class','col-sm-9');
		$("#bacheitem").attr('placeholder','输入多个单据编号,请用英文模式逗号分隔！');
	}
	if(col=='col-sm-9'){
		$("#bacheitem").attr('class','col-sm-3');
		$("#item_id").attr('placeholder','双击可批量输入单据编号');
	}
        
        
	
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=AppStonePurchase&act=search');//设定刷新的初始url
	util.setItem('formID','app_stone_search_form');//设定搜索表单id
	util.setItem('listDIV','app_stone_search_list');//设定列表数据容器id
	

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
			$('#app_stone_search_form select').select2({
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
			$('#app_stone_search_form :reset').on('click',function(){
				$('#app_stone_search_form select').select2("val","");
			});
			util.closeForm(util.getItem("formID"));
			app_stone_detail_search_page(util.getItem('orl'));
			
			$('#stone_export_btn').on('click', function(){
				var company = $('#app_stone_search_form select[name=company]').val();
				var prc_id = $('#app_stone_search_form select[name=prc_id]').val();
				var pay_apply_status = $('#app_stone_search_form select[name=pay_apply_status]').val();
				var item_type = $('#app_stone_search_form select[name=item_type]').val();
				var serial_number = $('#app_stone_search_form input[name=serial_number]').val();
				var pay_apply_number = $('#app_stone_search_form input[name=pay_apply_number]').val();
				var prc_num = $('#app_stone_search_form input[name=prc_num]').val();
				var item_id = $('#app_stone_search_form input[name=item_id]').val();
				var make_time_start = $('#app_stone_search_form input[name=make_time_start]').val();
				var make_time_end = $('#app_stone_search_form input[name=make_time_end]').val();
				var check_time_start = $('#app_stone_search_form input[name=check_time_start]').val();
				var check_time_end = $('#app_stone_search_form input[name=check_time_end]').val();

				var down_url = 'index.php?mod=finance&con=AppStonePurchase&act=downCsv&company='+company+'&prc_id='+prc_id+'&prc_num='+prc_num+'&pay_apply_status='+pay_apply_status+'&pay_apply_number='+pay_apply_number+'&serial_number='+serial_number+'&item_type='+item_type+'&item_id='+item_id+'&check_time_start='+check_time_start+'&check_time_end='+check_time_end+'&make_time_start='+make_time_start+'&make_time_end='+make_time_end;
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