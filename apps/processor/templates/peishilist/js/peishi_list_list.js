//分页
function peishi_list_search_page(url){
	util.page(url);
}
var formID= 'peishi_list_search_form';
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=PeishiList&act=search');//设定刷新的初始url
	util.setItem('formID',formID);//设定搜索表单id
	util.setItem('listDIV','peishi_list_search_list');//设定列表数据容器id

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
			 $('#'+formID+' select').select2({
				placeholder: "请选择",
				allowClear: true

		    }).change(function(e){
				$(this).valid();
		    });
			
			
			$('#peishi_list_search_form select[name="channel_class"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function (e){
                $(this).valid();
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=sales&con=BaseOrderInfo&act=getChannelIdByClass', {'channel_class': _t}, function (data) {
                        $('#peishi_list_search_form select[name="department_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                        $('#peishi_list_search_form select[name="department_id"]').change();
                    });
                }else{
                    $('#base_order_info_search_form select[name="department_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                }
            });
			
		};
		   
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			peishi_list_search_page(util.getItem("orl"));
			$('#'+formID+' :reset').on('click',function(){
				$('#peishi_list_search_form select').select2('val','');
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
