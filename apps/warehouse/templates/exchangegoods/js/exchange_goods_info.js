

$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
		],function(){


	var obj = function(){
		var initElements = function(){
			if (!jQuery().uniform) {
				return;
			}
			$('select[name="change_reason"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});

		};

		//表单验证和提交
		var handleForm = function(){

			}
		var initData = function(){

		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});


function change_goods(){
	$('body').modalmanager('loading');//进度条和遮罩
		var change_reason =$("#change_reason").val();
		var change_goods_id =$("#change_goods_id").val();
		var details_info =$("[name='details_info']:checked").val();
		var order_id=$("#order_id").val();
		var order_sn = $("#order_sn_val").val();
	$('.modal-scrollable').trigger('click');// 关闭遮罩
		if(!change_reason){
			util.xalert("请选择换货原因!"); return false;
		}
		if(!change_goods_id){
			util.xalert("请输入要替换的货号!");return false;
		}
		if(!details_info){
			util.xalert("请选择要被替换的货品!");return false;
		}
		var url = 'index.php?mod=warehouse&con=ExchangeGoods&act=changegoods';
					var data = {'change_reason':change_reason,'change_goods_id':change_goods_id,'details_info':details_info,'order_id':order_id,'order_sn':order_sn};
					$.post(url,data,function(e){
						if(e.error){
							util.xalert(e.error);
						}else{
							util.xalert("换货成功!");
							$("#search").trigger("submit")
						}

					});
	}
