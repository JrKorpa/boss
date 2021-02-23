//分页
function pay_jxc_order_search_page(url){
	util.page(url);
}

function download(){
	var down_info = "down_info";
    var jxc_order = $("#pay_jxc_order_search_form [name='jxc_order']").val();
    var type = $("#pay_jxc_order_search_form [name='type']").val();
    var is_return = $("#pay_jxc_order_search_form [name='is_return']").val();
    var status = $("#pay_jxc_order_search_form [name='status']").val();
    var kela_sn = $("#pay_jxc_order_search_form [name='kela_sn']").val();
    var from_ad = $("#pay_jxc_order_search_form [name='from_ad']").val();
    var addtime_start = $("#pay_jxc_order_search_form [name='addtime_start']").val();
    var addtime_end = $("#pay_jxc_order_search_form [name='addtime_end']").val();
    var checktime_start = $("#pay_jxc_order_search_form [name='checktime_start']").val();
    var checktime_end = $("#pay_jxc_order_search_form [name='checktime_end']").val();
    var hexiaotime_start = $("#pay_jxc_order_search_form [name='hexiaotime_start']").val();
    var hexiaotime_end = $("#pay_jxc_order_search_form [name='hexiaotime_end']").val();
    
    var args = "&down_info="+down_info+"&jxc_order="+jxc_order+"&type="+type+"&is_return="+is_return+"&status="+status+"&kela_sn="+kela_sn+"&from_ad="+from_ad+"&addtime_start="+addtime_start+"&addtime_end="+addtime_end+"&checktime_start="+checktime_start+"&checktime_end="+checktime_end+"&hexiaotime_start="+hexiaotime_start+"&hexiaotime_end="+hexiaotime_end;
    location.href = "index.php?mod=finance&con=PayJxcOrder&act=search"+args;
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function(){
	util.setItem('orl','index.php?mod=finance&con=PayJxcOrder&act=search');//设定刷新的初始url
	util.setItem('formID','pay_jxc_order_search_form');//设定搜索表单id
	util.setItem('listDIV','pay_jxc_order_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉列表美化
            $('#pay_jxc_order_search_form select[name="status"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            $('#pay_jxc_order_search_form select[name="from_ad"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            $('#pay_jxc_order_search_form select[name="type"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            $('#pay_jxc_order_search_form select[name="is_return"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
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
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
            $("#pay_jxc_order_search_form :reset").on('click',function(){
                $("#pay_jxc_order_search_form select[name='type']").select2('val','').change();
                $("#pay_jxc_order_search_form select[name='status']").select2('val','').change();
                $("#pay_jxc_order_search_form select[name='is_return']").select2('val','').change();
                $("#pay_jxc_order_search_form select[name='from_ad']").select2('val','').change();
            });
			util.closeForm(util.getItem("formID"));
			pay_jxc_order_search_page(util.getItem("orl"));
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