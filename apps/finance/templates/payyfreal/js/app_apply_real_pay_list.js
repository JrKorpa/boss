//分页
function pay_yf_real_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=finance&con=PayYfReal&act=search');//设定刷新的初始url
	util.setItem('formID','app_apply_real_pay_search_form');//设定搜索表单id
	util.setItem('listDIV','app_apply_real_pay_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			$('#app_apply_real_pay_search_form select').select2({
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
			var qh_url = '?mod=finance&con=PayYfReal&act=return_content';
			$('#start_year').on('change',function(){
				var year = $(this).val();
				$.post(qh_url,{years:year},function(data){
					if(data.success==1){
						$('#start_qihao').select2("val","");
						$('#start_qihao').html(data.html);
					}
				});
			});
			
			$('#end_year').on('change',function(){
				var year = $(this).val();
				$.post(qh_url,{years:year},function(data){
					if(data.success==1){
						$('#end_qihao').select2("val","");
						$('#end_qihao').html(data.html);
					}
				});
			});
			
			
			$('#app_apply_real_pay_search_form :reset').on('click',function(){
				$('#app_apply_real_pay_search_form select').select2("val","");
			});
			util.closeForm(util.getItem("formID"));
			pay_yf_real_search_page(util.getItem("orl"));
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

function download(){

	var down_info = 'down_info';
    var prc_id = $("#app_apply_real_pay_search_form [name=prc_id]").val();
    var pay_type = $("#app_apply_real_pay_search_form [name=pay_type]").val();
    var pay_real_number = $("#app_apply_real_pay_search_form [name=pay_real_number]").val();
    var pay_number = $("#app_apply_real_pay_search_form [name=pay_number]").val();
    var make_name = $("#app_apply_real_pay_search_form [name=make_name]").val();
    var start_year = $("#app_apply_real_pay_search_form [name=start_year]").val();
    var start_qihao = $("#app_apply_real_pay_search_form [name=start_qihao]").val();
    var end_year = $("#app_apply_real_pay_search_form [name=end_year]").val();
    var end_qihao = $("#app_apply_real_pay_search_form [name=end_qihao]").val();
    var make_time_s = $("#app_apply_real_pay_search_form [name=make_time_s]").val();
    var make_time_e = $("#app_apply_real_pay_search_form [name=make_time_e]").val();



    var args = "&down_info="+down_info+"&prc_id="+prc_id+"&pay_type="+pay_type+"&pay_real_number="+pay_real_number+"&pay_number="+pay_number+"&make_name="+make_name+"&start_year="+start_year+"&start_qihao="+start_qihao+"&end_year="+end_year+"&end_qihao="+end_qihao+"&make_time_s="+make_time_s+"&make_time_e="+make_time_e;
    location.href = "index.php?mod=finance&con=PayYfReal&act=search"+args;

}