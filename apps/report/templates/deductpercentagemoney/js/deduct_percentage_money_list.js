//分页
function deduct_percentage_money_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=DeductPercentageMoney&act=search');//设定刷新的初始url
	util.setItem('formID','deduct_percentage_money_search_form');//设定搜索表单id
	util.setItem('listDIV','deduct_percentage_money_search_list');//设定列表数据容器id
    var info_form_id = 'deduct_percentage_money_search_form';
	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

            $('#'+info_form_id+' select[name="salse[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
            
            $('#'+info_form_id+' select[name="shop_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#'+info_form_id+' select[name="shop_type"]').change(function(e){
                //$(this).valid();
                $('#'+info_form_id+' select[name="shop_id"]').empty();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=DeductPercentageMoney&act=getShops',{shop_type:t_v},function(data){
                        $('#'+info_form_id+' select[name="shop_id"]').empty();
                        $('#'+info_form_id+' select[name="shop_id"]').append(data);
                    });
                }
                else
                {
                    $('#'+info_form_id+' select[name="shop_id"]').select2('val','').attr('readOnly',false).change();
                }
            });

            $('#'+info_form_id+' select[name="shop_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
                $('#'+info_form_id+' select[name="salse[]"]').empty();
                $('#'+info_form_id+' select[name="salse[]"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=report&con=DeductPercentageMoney&act=getCreateuser', {department: _t}, function(data) {
                        $('#'+info_form_id+' select[name="salse[]"]').append(data.content);
                        $('#'+info_form_id+' select[name="salse[]"]').change();
                    });
                }else{
                    $('#'+info_form_id+' select[name="salse[]"]').change();
                }
            }); 

            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm',
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
			deduct_percentage_money_search_page(util.getItem("orl"));
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