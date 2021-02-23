
//分页
function factoryLated_last_detail_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){

    var xx_url = '';
    var xx_self = '';
    if(xx_self == true){
    	xx_url = 'index.php?mod=report&con=FactoryLatedReport&act=last_detail_list_ajax&self='+xx_self;
    }else{
    	xx_url = 'index.php?mod=report&con=FactoryLatedReport&act=last_detail_list_ajax';
    }
    xx_url=xx_url+'&start_time='+start_time+'&end_time='+end_time+'&from_type='+from_type+'&opra_uname='+opra_uname+'&style_sn='+style_sn+'&prc_ids_string='+prc_ids_string;
    util.setItem('orl',xx_url);//设定刷新的初始url

	util.setItem('formID','factoryLated_last_detail_list_search_form');//设定搜索表单id
	util.setItem('listDIV','factory_last_detail_search_list');//设定列表数据容器id
	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
                     //下拉列表美化
                            $('#factoryLated_last_detail_list_search_form select').select2({
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
                            $('#factoryLated_last_detail_list_search_form :reset').on('click',function(){
				$('#factoryLated_last_detail_list_search_form select').select2("val","");
			    });
				$('#factoryLated_last_detail_list_search_form input[name=checkAll]').click(function(){
					var status=$(this).attr('checked');
					var arr=new Array();
					if(status=='checked'||status==true)
					{

						$('#factoryLated_last_detail_list_search_form select[name="buchan_fac_opra[]"] option').each(function(key,v){
							arr[key]=$(this).val();
						})
						$('#factoryLated_last_detail_list_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);
					}
					$('#factoryLated_last_detail_list_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);

				});

                };
		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			factoryLated_last_detail_search_page(util.getItem("orl"));
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
