util.hover();
var formID= 'vip_delivery_search_form';
var storage_no = '<%$delivery_info.storage_no%>';
//分页
function vip_delivery_goods_search_page(url){
	util.page(url);
}
function vip_delivery_traceinfo_search(storage_no){
	$.ajax({
			type:"POST",
			url: 'index.php?mod=warehouse&con=VipDelivery&act=searchDeliveryTraceInfo&storage_no='+storage_no,
			data: {},
			dataType: "text",
			async:true,
			success: function(res){
				$("#vip_delivery_traceinfo").html(res);								
			}
		});
}

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=VipDelivery&act=searchDeliveryGoodsList&storage_no='+storage_no);
	util.setItem('formID',formID);//设定搜索表单id
	util.setItem('listDIV','vip_delivery_goods_list');//设定列表数据容器id

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
			 $('#'+formID+' select').select2({
				placeholder: "请选择",
				allowClear: true

		    }).change(function(e){
				$(this).valid();
		    });
		};
		   
		var handleForm = function(){
			//util.search();
		};
		
		var initData = function(){
			vip_delivery_goods_search_page(util.getItem("orl"));
			vip_delivery_traceinfo_search(storage_no);
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
