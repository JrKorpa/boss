//分页
function app_receive_real_search_page(url){
	util.page(url);
}

function download(){
    var from_ad = $("#app_receive_real_search_form select[name='from_ad']").val();
    var real_number = $("#app_receive_real_search_form input[name='real_number']").val();
    var should_number = $("#app_receive_real_search_form input[name='should_number']").val();
    var pay_tiime_start = $("#app_receive_real_search_form input[name='pay_tiime_start']").val();
    var pay_tiime_end = $("#app_receive_real_search_form input[name='pay_tiime_end']").val();
    var start_year = $("#app_receive_real_search_form select[name='start_year']").val();
    var start_qihao = $("#app_receive_real_search_form select[name='start_qihao']").val();
    var end_year = $("#app_receive_real_search_form select[name='end_year']").val();
    var end_qihao = $("#app_receive_real_search_form select[name='end_qihao']").val();
    
    var args = "&from_ad="+from_ad+"&real_number="+real_number+"&should_number="+should_number+"&pay_tiime_start="+pay_tiime_start+"&pay_tiime_end="+pay_tiime_end+"&start_year="+start_year+"&start_qihao="+start_qihao+"&end_year="+end_year+"&end_qihao="+end_qihao;
    location.href = "index.php?mod=finance&con=AppReceiveReal&act=download"+args;
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function(){
	util.setItem('orl','index.php?mod=finance&con=AppReceiveReal&act=search');//设定刷新的初始url
	util.setItem('formID','app_receive_real_search_form');//设定搜索表单id
	util.setItem('listDIV','app_receive_real_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            $('#app_receive_real_search_form select').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e) {
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
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			var qh_url = '?mod=finance&con=AppReceiveReal&act=return_content';
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
			
            $('#app_receive_real_search_form :reset').on('click',function(){
				$('#app_receive_real_search_form select[name="start_year"]').select2('val','');
				$('#app_receive_real_search_form select[name="end_year"]').select2('val','');
				$('#app_receive_real_search_form select[name="from_ad"]').select2('val','');
				$('#app_receive_real_search_form select[name="start_qihao"]').select2('val','');
				$('#app_receive_real_search_form select[name="end_qihao"]').select2('val','');
				
			})
			util.closeForm(util.getItem("formID"));
			app_receive_real_search_page(util.getItem("orl"));
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