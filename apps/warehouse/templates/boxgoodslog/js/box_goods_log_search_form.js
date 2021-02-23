function download(obj){
	var goods_id = $('#box_goods_log_search_form input[name="goods_id"]').val();
	var type = $('#box_goods_log_search_form [name="type"]').val();
	var create_user = $('#box_goods_log_search_form [name="create_user"]').val();
	var time_start = $('#box_goods_log_search_form [name="time_start"]').val();
	var time_end = $('#box_goods_log_search_form [name="time_end"]').val();
	location.href = "index.php?mod=warehouse&con=BoxGoodsLog&act=search&down=1&goods_id="+goods_id+"&type="+type+"&create_user="+create_user+"&time_start="+time_start+"&time_end="+time_end;

}

function box_goods_log_search_page(url){
	util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=BoxGoodsLog&act=search');
	util.setItem('listDIV','box_goods_log_search_list');
	util.setItem('formID','box_goods_log_search_form');

	var BoxGoodsLogObj = function(){
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
			$('#box_goods_log_search_form select').select2({
				placeholder: "请选择",
				allowClear: true

			}).change(function(e){
				$(this).valid();
			});

			$('#box_goods_log_search_form :reset').on('click',function(){
				$('#box_goods_log_search_form select').select2("val","");
			})
		};
		var handleForm = function(){
			util.search()
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));		//合上搜索框
			box_goods_log_search_page(util.getItem('orl'));
		};
		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	BoxGoodsLogObj.init();
});
