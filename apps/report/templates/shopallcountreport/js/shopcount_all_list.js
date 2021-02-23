function shopcount_search_page (url)
{
	util.page(url);
}
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=ShopallcountReport&act=search');
	util.setItem('formID','shopallcount_search_form');
	util.setItem('listDIV','shopallcount_search_list');

    var info_form_id = 'shopallcount_search_form';
	
	var ShopCfgObj = function(){
		var initElements=function(){
            $('#shopallcount_search_form select[name="fenlei"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
			
			$('#shopallcount_search_form select[name="shop_id"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#shopallcount_search_form select[name="shop_type"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#shopallcount_search_form select[name="salse[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#shopallcount_search_form select[name="shop_type"]').change(function(e){
                $(this).valid();
                $('#shopallcount_search_form select[name="shop_id"]').empty();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=ShopallcountReport&act=getShops',{shop_type:t_v},function(data){
                        $('#shopallcount_search_form select[name="shop_id"]').empty();
                        $('#shopallcount_search_form select[name="shop_id"]').append(data);
                    });
                }
                else
                {
                    $('#shopallcount_search_form select[name="shop_id"]').select2('val','').attr('readOnly',false).change();
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
                    $.post('index.php?mod=report&con=ShopallcountReport&act=getCreateuser', {department: _t}, function(data) {
                        $('#'+info_form_id+' select[name="salse[]"]').append(data.content);
                        $('#'+info_form_id+' select[name="salse[]"]').change();
                    });
                }else{
                    $('#'+info_form_id+' select[name="salse[]"]').change();
                }
            }); 
			
			$('#shopallcount_search_form select[name="source_name"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });
			
			$('#shopallcount_search_form select[name="orderenter"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
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
			
			$('#shopallcount_search_form :reset').on('click',function(){
				$('#shopallcount_search_form select').select2("val","");
                $('#shopallcount_search_form select[name="salse[]"]').select2('val',[]).change();//multiple
			})


        };
		var handleForm=function(){
			util.search();
		};
		var initData=function(){
			util.closeForm(util.getItem("formID"));
			shopcount_search_page(util.getItem("orl"));
		};
	
		return {
			init:function(){
				initElements();
				handleForm();
				//initData();
			}
		}
	}();
	ShopCfgObj.init();
});