//分页   
function wait_diamond_lated_search_page_second(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){

    var xx_url = 'index.php?mod=report&con=WaitDiamondLatedReport&act=search_second';
	util.setItem('orl',xx_url);//设定刷新的初始url

	util.setItem('formID','wait_diamond_lated_search_form_second');//设定搜索表单id
	util.setItem('listDIV','wait_diamond_lated_search_list_second');//设定列表数据容器id
	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
                     //下拉列表美化
                            $('#wait_diamond_lated_search_form_second select').select2({
                                placeholder: "全部",
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
                            $('#wait_diamond_lated_search_form_second :reset').on('click',function(){
				$('#wait_diamond_lated_search_form_second select').select2("val","");
			    });
				$('#wait_diamond_lated_search_form_second input[name=checkAll]').click(function(){
					var status=$(this).attr('checked');
					var arr=new Array();
					if(status=='checked'||status==true)
					{

						$('#wait_diamond_lated_search_form_second select[name="buchan_fac_opra[]"] option').each(function(key,v){
							arr[key]=$(this).val();
						})
						$('#wait_diamond_lated_search_form_second select[name="buchan_fac_opra[]"]').select2("val",arr);
					}
					$('#wait_diamond_lated_search_form_second select[name="buchan_fac_opra[]"]').select2("val",arr);

				});

                };
		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			wait_diamond_search_page_second(util.getItem("orl"));
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

function show_wait_diamond_lated_list_second(obj){
	var data = $('#wait_diamond_lated_search_form_second').serialize();
	var url = $(obj).attr('data-url');
	var param=new Array();
	param['start_time']=$("#wait_diamond_lated_search_form_second start_time").val();
	param['end_time']=$("#wait_diamond_lated_search_form_second end_time").val();
	param['style_sn']=$("#wait_diamond_lated_search_form_second style_sn").val();
	param['prc_ids']=$("#wait_diamond_lated_search_form_second prc_ids").val();
	param['opra_unames']=$("#wait_diamond_lated_search_form_second opra_unames").val();
	param['from_type']=$("#wait_diamond_lated_search_form_second from_type").val();
	param['diamondtype']=$("#wait_diamond_lated_search_form_second diamondtype").val();
	for(index in param){
		if(index!='contains'){
			//url+='&'+index+'='+param[index];
		}
	}
	url += "&"+data;
	window.open(url);
	return false;
}

