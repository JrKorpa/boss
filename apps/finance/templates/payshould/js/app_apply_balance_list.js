//分页
function pay_should_search_page(url){
	util.page(url);
}
function download(){
	var down_info = 'down_info';
    var status = $("#status").val();
    var pay_status = $("#pay_status").val();
    var prc_id = $("#prc_id").val();
    var pay_type = $("#pay_type").val();
    var pay_should_all_name = $("#pay_should_all_name").val();
    var make_time_s = $("#make_time_s").val();
    var make_time_e = $("#make_time_e").val();
    var check_time_s = $("#check_time_s").val();
    var check_time_e = $("#check_time_e").val();


    var args = "&down_info="+down_info+"&status="+status+"&pay_status="+pay_status+"&prc_id="+prc_id+"&pay_type="+pay_type+"&pay_should_all_name="+pay_should_all_name+"&make_time_s="+make_time_s+"&make_time_e="+make_time_e+"&check_time_s="+check_time_s+"&check_time_e="+check_time_e;
    location.href = "index.php?mod=finance&con=PayShould&act=search"+args;

}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=PayShould&act=search');//设定刷新的初始url
	util.setItem('formID','app_apply_balance_search_form');//设定搜索表单id
	util.setItem('listDIV','app_apply_balance_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#app_apply_balance_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
			//日期
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true,
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			pay_should_search_page(util.getItem("orl"));
			//重置按钮
			$('#app_apply_balance_search_form :reset').on('click',function(){
				$('#app_apply_balance_search_form select').select2("val","").change();
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

