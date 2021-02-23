//查看全部
function showall(obj){
	//关闭容易引起js冲突的页签
	var txt = $('#nva-tab li a');
	txt.each(function(i){
		if($.trim($(this).text()).indexOf('布产监控')  >= 0 ){
			$(this).parent().children('i').trigger('click');
		}
	});

	var url = $(obj).attr('data-url');
	var listid = $(obj).attr('list-id');
	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";
	var title = $(obj).attr('data-title');
	if (!title)
	{
		title=params['con'];
	}
	//不能同时打开两个添加页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '前往查看'
					},
					cancel: {
						label: '点错了'
					}
				},
				closeButton: false,
				message: "发现同类数据的页签已经打开。",
				callback: function(result) {
					if (result == true) {
						$(that).children('a').trigger("click");
					}
				},
				title: "提示信息",
			});
			return false;
		}
	});
	if (!flag)
	{
		var id = prefix+"-0";
		new_tab(id,title,url+'&tab_id='+listid);
	}

}

//分页
function ClothProductionTracking_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){

    var xx_url = '';
    var xx_self = '<%$self%>';
    if(xx_self == true){
    	xx_url = 'index.php?mod=processor&con=ClothProductionTracking&act=search&self='+xx_self;
    }else{
    	xx_url = 'index.php?mod=processor&con=ClothProductionTracking&act=search';
    }
	util.setItem('orl',xx_url);//设定刷新的初始url

	util.setItem('formID','ClothProductionTracking_search_form');//设定搜索表单id
	util.setItem('listDIV','ClothProductionTracking_search_list');//设定列表数据容器id
	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
                     //下拉列表美化
                            $('#ClothProductionTracking_search_form select').select2({
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
                            $('#ClothProductionTracking_search_form :reset').on('click',function(){
				$('#ClothProductionTracking_search_form select').select2("val","");
			    });
				$('#ClothProductionTracking_search_form input[name=checkAll]').click(function(){
					var status=$(this).attr('checked');
					var arr=new Array();
					if(status=='checked'||status==true)
					{

						$('#ClothProductionTracking_search_form select[name="buchan_fac_opra[]"] option').each(function(key,v){
							arr[key]=$(this).val();
						})
						$('#ClothProductionTracking_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);
					}
					$('#ClothProductionTracking_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);

				});

                };
		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			ClothProductionTracking_search_page(util.getItem("orl"));
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
