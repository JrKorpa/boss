//导出csv
function month_pan_export_cxv_index(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['time_start']=$("#monthpandianreport_search_form #time_start").val();
	param['time_end']=$("#monthpandianreport_search_form #time_end").val();
	param['company_id']=$("#monthpandianreport_search_form #company_id").val();
	param['warehouse']=$("#monthpandianreport_search_form #warehouse").val();
	param['to_company_id']=$("#monthpandianreport_search_form #to_company_id").val();
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
//分页
function warehouse_pandian_plan_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=report&con=MonthPandianReport&act=search');//设定刷新的初始url
	util.setItem('formID','monthpandianreport_search_form');//设定搜索表单id
	util.setItem('listDIV','monthpandianreport_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
			 if ($.datepicker) {
	                $('.date-picker').datepicker({
	                    format: 'yyyy-mm-dd',
	                    rtl: App.isRTL(),
	                    autoclose: true,
	                    clearBtn: true
	                });
	                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
	            }
			 $('#monthpandianreport_search_form select').select2({
                 placeholder: "全部",
                 allowClear: true,
             }).change(function(e) {
               //  $(this).valid();
             });
			 $('#monthpandianreport_search_form :reset').on('click',function(){
					$('#monthpandianreport_search_form select').select2("val","");
			 });
			 $('#monthpandianreport_search_form select[name="to_company_id"]').select2({
					placeholder: "请选择",
					allowClear: true,
				}).change(function (e){
	  				$(this).valid();
					var _t = $(this).val();
					if (_t) {
						$.post('index.php?mod=warehouse&con=WarehouseGoods&act=getTowarehouseId', {'id': _t}, function (data) {
							$('#monthpandianreport_search_form select[name="warehouse"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
							$('#monthpandianreport_search_form select[name="warehouse"]').change();
						});
					}else{
						$('#monthpandianreport_search_form select[name="warehouse"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
					}
				});
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_pandian_plan_search_page(util.getItem("orl"));
			
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}
	}();

	obj.init();
});