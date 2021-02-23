//分页
function app_bespoke_info_search_page(url){
	util.page(url);
}

function redircrm() {
	window.open('<%$crm_url%>');
	return false;
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){

	util.setItem('orl','index.php?mod=bespoke&con=AppBespokeInfo&act=search');
	util.setItem('formID','app_bespoke_info_search_form');//设定搜索表单id
	util.setItem('listDIV','app_bespoke_info_search_list');//设定列表数据容器id

	var baseMemberInfoObj = function(){
		var initElements = function(){
			$('#app_bespoke_info_search_form select[name="cause_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
                $(this).valid();
                $('#app_bespoke_info_search_form select[name="department_id"]').empty();
                $('#app_bespoke_info_search_form select[name="department_id"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=bespoke&con=AppBespokeInfo&act=getDepartment', {dep: _t}, function(data) {
                        $('#app_bespoke_info_search_form select[name="department_id"]').append(data);
                        $('#app_bespoke_info_search_form select[name="department_id"]').change();
                    });
                }
                else {
                    $('#app_bespoke_info_search_form select[name="department_id"]').change();
                }
			});

			$('#app_bespoke_info_search_form select[name="member_name"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_search_form select[name="customer_source_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_search_form select[name="re_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_search_form select[name="deal_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
            $('#app_bespoke_info_search_form select[name="queue_status"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

			$('#app_bespoke_info_search_form select[name="department_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#app_bespoke_info_search_form select[name="bespoke_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

			$('#app_bespoke_info_search_form select[name="goshop_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_search_form select[name="re_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#app_bespoke_info_search_form select[name="hf_status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
				//时间控件
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
            
            
            //重置操作
            $('#app_bespoke_info_search_form :reset').on('click',function(){
                $('#app_bespoke_info_search_form select[name="hf_status"]').select2('val','').change();
				$('#app_bespoke_info_search_form select[name="re_status"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="goshop_status"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="bespoke_status"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="department_id"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="member_name"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="customer_source_id"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="queue_status"]').select2('val','').change();
                $('#app_bespoke_info_search_form select[name="deal_status"]').select2('val','').change();
            });
            
        };
		var handleForm = function(){
			$('#tomorrow').on('click',function(){
				var tomorrow=$('#app_bespoke_info_search_form input[name="yincang"]').val();
				$('#app_bespoke_info_search_form input[name="bespoke_inshop_time_start"]').val(tomorrow);
				$('#app_bespoke_info_search_form input[name="bespoke_inshop_time_end"]').val(tomorrow);
			})


			$('#today').on('click',function(){
			  if($('#app_bespoke_info_search_form input[name="bespoke_inshop_time_start"]').val()=='' || $('#app_bespoke_info_search_form input[name="bespoke_inshop_time_end"]').val()==''){
				var jintian=$('#app_bespoke_info_search_form input[name="jintian"]').val();
				$('#app_bespoke_info_search_form input[name="bespoke_inshop_time_start"]').val();
				$('#app_bespoke_info_search_form input[name="bespoke_inshop_time_end"]').val();
			  }
			})

			util.search();
        };
        var initData = function(){
            util.closeForm(util.getItem("formID"));
            $('#'+util.getItem("formID")).submit();
        };
		return {
			init:function(){
				initElements();
				handleForm();
                //initData();
				// add by geng
                var todo = $('#app_bespoke_info_search_form [name="todo"]:checked').val();
                if (todo==1) {
                    initData();
                }
			}
		}
	}();
	baseMemberInfoObj.init();
});
