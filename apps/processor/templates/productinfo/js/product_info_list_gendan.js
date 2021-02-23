//查看全部布产单
function showall(obj){
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


function product_info_search_page_gendan(url){
	util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=ProductInfo&act=search');
	util.setItem('listDIV','product_info_search_list_gendan');
	util.setItem('formID','product_info_search_form_gendan');

	var ProductInfoObjgendan = function(){
		var initElements = function(){

			//日期
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			$('#product_info_search_form_gendan select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});

			$('#product_info_search_form_gendan input[name=checkAll]').click(function(){
				var status=$(this).attr('checked');
				var arr=new Array();
				if(status=='checked'||status==true)
				{
					
					$('#product_info_search_form_gendan select[name="buchan_fac_opra[]"] option').each(function(key,v){
						arr[key]=$(this).val();
					})
					$('#product_info_search_form_gendan select[name="buchan_fac_opra[]"]').select2("val",arr);	
				}
				$('#product_info_search_form_gendan select[name="buchan_fac_opra[]"]').select2("val",arr);	
				
			});

            $('#product_info_search_form_gendan select[name="channel_class"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function (e){
                $(this).valid();
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=ProductInfo&act=getChannelIdByClass', {'channel_class': _t}, function (data) {
                        $('#product_info_search_form_gendan select[name="channel_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                        $('#product_info_search_form_gendan select[name="channel_id"]').change();
                    });
                }else{
                    $('#product_info_search_form_gendan select[name="channel_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                }
            });

		};
		var handleForm = function(){
			util.search();
			util.closeForm(util.getItem("formID"));
		};
		var initData = function(){
			product_info_search_page_gendan(util.getItem('orl'));
			$('#product_info_search_form_gendan :reset').on('click',function(){
				$('#product_info_search_form_gendan select').select2('val','');
			});
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	ProductInfoObjgendan.init();
});

