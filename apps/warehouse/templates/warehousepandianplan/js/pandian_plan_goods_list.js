
//导出结果
function download(){
	var goods_id = $('#pandian_plan_search_goods_form input[name=goods_id]').val();
	var id = $('#pandian_plan_search_goods_form input[name=id]').val();
	var opt_admin = $('#pandian_plan_search_goods_form input[name=opt_admin]').val();
	var verify_admin = $('#pandian_plan_search_goods_form input[name=verify_admin]').val();
	var create_time_start = $('#pandian_plan_search_goods_form input[name=create_time_start]').val();
	var create_time_end = $('#pandian_plan_search_goods_form input[name=create_time_end]').val();
	var start_time_start = $('#pandian_plan_search_goods_form input[name=start_time_start]').val();
	var start_time_end = $('#pandian_plan_search_goods_form input[name=start_time_end]').val();
	var type = $('#pandian_plan_search_goods_form select[name="type"]').val();
	var status = $('#pandian_plan_search_goods_form select[name="status"]').val();
	var search_type = $('#pandian_plan_search_goods_form  .radio .checked input').attr('value');

	var down_url = 'index.php?mod=warehouse&con=WarehousePandianPlan&act=download&goods_id='+goods_id+'&id='+id+'&opt_admin='+opt_admin+'&verify_admin='+verify_admin+'&create_time_start='+create_time_start+'&create_time_end='+create_time_end+'&start_time_start='+start_time_start+'&start_time_end='+start_time_end+'&type='+type+'&status='+status+'&search_type='+search_type;
	window.open(down_url);
}


//分页
function pandian_plan_search_goods_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehousePandianPlan&act=searchType1');//设定刷新的初始url
	util.setItem('formID','pandian_plan_search_goods_form');//设定搜索表单id
	util.setItem('listDIV','pandian_plan_search_goods_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
			$('#pandian_plan_search_goods_form select').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});

			//单选美化
			var test = $("#pandian_plan_search_goods_form input[type='radio']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			//时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_pandian_plan_search_page(util.getItem("orl"));
			$('#pandian_plan_search_goods_form :reset').on('click',function(){
				//下拉置空
				$('#pandian_plan_search_goods_form select[name="type"]').select2('val','').change();//single
				$('#pandian_plan_search_goods_form select[name="status"]').select2('val','').change();//single
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