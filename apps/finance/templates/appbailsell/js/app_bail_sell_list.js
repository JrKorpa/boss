//分页
function app_bail_sell_detail_search_page(url){
	util.page(url);
}
function bach_search_num(){
	var col = $("#bacheitem").attr('class');
	if(col=='col-sm-3'){
		$("#bacheitem").attr('class','col-sm-9');
		$("#bacheitem").attr('placeholder','输入多个证书号时,请用英文模式逗号分隔！');
	}
	if(col=='col-sm-9'){
		$("#bacheitem").attr('class','col-sm-3');
		$("#item_id").attr('placeholder','双击可批量输入证书号');
	}
        
        
	
}
//匿名回调

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('formID','app_bail_sell_detail_search_form');//设定搜索表单id
	util.setItem('listDIV','app_bail_sell_detail_search_list');//设定列表数据容器id
	util.setItem('orl','index.php?mod=finance&con=AppBailSell&act=search');//设定刷新的初始url

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
			$('#app_bail_sell_detail_search_form select').select2({
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
			$('#app_bail_sell_detail_search_form :reset').on('click',function(){
				$('#app_bail_sell_detail_search_form select').select2("val","");
			});
			
			$("#bail_sell_export_btn").on('click',function(){
				var company = $("#company").val();
				var prc_id = $("#prc_id").val();
				//var goods_status = $("#goods_status").val();
				//var pay_apply_status = $("#pay_apply_status").val();
				var storage_mode = $("#storage_mode").val();
				var pay_apply_number = $("#pay_apply_number").val();
				var serial_number = $("#serial_number").val();
				var zhengshuhao = $("#zhengshuhao").val();
				var item_id = $("#item_id").val();
				var check_time_start = $("#check_time_start").val();
				var check_time_end = $("#check_time_end").val();
				var make_time_start = $("#make_time_start").val();
				var make_time_end = $("#make_time_end").val();
				var item_type = $("#item_type").val();
				var down_url = 'index.php?mod=finance&con=AppBailSell&act=search&download=download&company='+company+'&prc_id='+prc_id+'&storage_mode='+storage_mode+'&pay_apply_number='+pay_apply_number+'&serial_number='+serial_number+'&zhengshuhao='+zhengshuhao+'&item_id='+item_id+'&check_time_start='+check_time_start+'&check_time_end='+check_time_end+'&make_time_start='+make_time_start+'&make_time_end='+make_time_end+'&item_type='+item_type;
				window.open(down_url) 
			});
			
			util.closeForm(util.getItem("formID"));
			app_bail_sell_detail_search_page(util.getItem('orl'));
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
