//批量送钻
function allsongzuan(obj){
	$('body').modalmanager('loading');
	var _ids = [];
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
		_ids.push($(this).val());
	});
	if (!_ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}

	var url = "index.php?mod=processor&con=ProductFactoryOpra&act=songZuan";
	bootbox.confirm({
		buttons: {
			confirm: {
				label: '确认'
			},
			cancel: {
				label: '放弃'
			}
		},
		message: "确定批量操作送钻吗?",
		closeButton: false,
		callback: function(result) {
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{id:_ids},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
								util.sync(obj);
							});
						}
						else
						{
							util.error(data);
						}
					});
				}, 0);
			}
		},
		title: "提示信息",
	});

}



//批量送钻
function allconfirm(obj){
	$('body').modalmanager('loading');
	var _ids = [];
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
		_ids.push($(this).val());
	});
	if (!_ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}

	var url = "index.php?mod=processor&con=ProductInfo&act=allConfirm";
	bootbox.confirm({
		buttons: {
			confirm: {
				label: '确认'
			},
			cancel: {
				label: '放弃'
			}
		},
		message: "确定批量操作确认吗?",
		closeButton: false,
		callback: function(result) {
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{id:_ids},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
								util.sync(obj);
							});
						}
						else
						{
							util.error(data);
						}
					});
				}, 0);
			}
		},
		title: "提示信息",
	});

}


function product_info_search_page(url){
	util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=ProductInfo&act=searchProduct');
	util.setItem('listDIV','product_info_search_list');
	util.setItem('formID','product_info_search_form');

	var ProductInfoObj = function(){
		var initElements = function(){
			$('#product_info_search_form select').select2({
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
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}

				$('#product_info_search_form input[name=checkAll]').click(function(){
					var status=$(this).attr('checked');
					var arr=new Array();
					if(status=='checked'||status==true)
					{
						
						$('#product_info_search_form select[name="buchan_fac_opra[]"] option').each(function(key,v){
							arr[key]=$(this).val();
						})
						$('#product_info_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);	
					}
					$('#product_info_search_form select[name="buchan_fac_opra[]"]').select2("val",arr);	
					
				});

            $('#product_info_search_form select[name="channel_class"]').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function (e){
                $(this).valid();
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=processor&con=ProductInfo&act=getChannelIdByClass', {'channel_class': _t}, function (data) {
                        $('#product_info_search_form select[name="channel_id"]').attr('disabled', false).empty().append('<option value=""></option>').append(data);
                        $('#product_info_search_form select[name="channel_id"]').change();
                    });
                }else{
                    $('#product_info_search_form select[name="channel_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                }
            });

		};
		var handleForm = function(){
			util.search();
			util.closeForm(util.getItem("formID"));
		};
		var initData = function(){
			product_info_search_page(util.getItem('orl'));
			$('#product_info_search_form :reset').on('click',function(){
				$('#product_info_search_form select').select2('val','');
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
	ProductInfoObj.init();
});
