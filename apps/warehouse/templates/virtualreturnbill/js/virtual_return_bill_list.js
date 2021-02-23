//分页
function virtual_return_bill_search_page(url){
	util.page(url);
}
var info_form_id = 'virtual_return_bill_search_form';
//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'],function(){
	util.setItem('orl','index.php?mod=warehouse&con=VirtualReturnBill&act=search');//设定刷新的初始url
	util.setItem('formID','virtual_return_bill_search_form');//设定搜索表单id
	util.setItem('listDIV','virtual_return_bill_search_list');//设定列表数据容器id
    var info_form_id = "virtual_return_bill_search_form";
	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉美化 需要引入"public/js/select2/select2.min.js"
            $('#'+info_form_id+' select').select2({
                placeholder: "请选择",
                allowClear: true,
//              minimumInputLength: 2
            }).change(function(e){
                $(this).valid();
            }); 

            //时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
          if ($.datepicker) {
              $('.date-picker').datepicker({
                  format: 'yyyy-mm-dd',
                  rtl: App.isRTL(),
                  autoclose: true
              });
              $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
          }
		  
		   $('#'+info_form_id+' select[name="place_company_id"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function (e){
                $(this).valid();
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=warehouse&con=VirtualReturnBill&act=getTowarehouseId', {'id': _t}, function (data) {
                        $('#'+info_form_id+' select[name="place_warehouse_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                        $('#'+info_form_id+' select[name="place_warehouse_id"]').change();
                    });
                }else{
                    $('#'+info_form_id+' select[name="place_warehouse_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                }
            });
            
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			virtual_return_bill_search_page(util.getItem("orl"));
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