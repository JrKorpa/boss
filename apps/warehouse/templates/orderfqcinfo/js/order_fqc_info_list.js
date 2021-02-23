
function change_pro()
{
	var v = $("#qc_type").val();
	if (v == 0)
	{
		$("#qc2").html("<option value='0'>请选择</option>");
		return false;
	}
        
	$.post("index.php?mod=warehouse&con=OrderFqcInfo&act=get_protype",{id:v},function(data){
		//$("#qc2").html(data);
		$('#qc2').attr('disabled', false).empty().append('<option value=""></option>').append(data);
	$('#qc2').change();	
	})

}


//分页
function order_fqc_info_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js", "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=OrderFqcInfo&act=search');//设定刷新的初始url
	util.setItem('formID','order_fqc_info_search_form');//设定搜索表单id
	util.setItem('listDIV','order_fqc_info_search_list');//设定列表数据容器id

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
                    
                    $('#order_fqc_info_search_form select').select2({
                            placeholder: "请选择",
                            allowClear: true

                    }).change(function(e){
                            $(this).valid();
                    });
                    $('#order_fqc_info_search_form :reset').on('click',function(){
                            $('#order_fqc_info_search_form select').select2("val","");
                    })

		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			order_fqc_info_search_page(util.getItem("orl"));
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