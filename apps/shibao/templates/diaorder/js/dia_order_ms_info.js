$import("public/js/select2/select2.min.js",function(){
	var info_form_base_url = 'index.php?mod=shibao&con=DiaOrder&act=';//基本提交路径

	var obj = function(){
		var initElements = function(){

			$('#dia_order_stone_search select').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});

		};

		var initData = function(){
			var pro_sn_input = $('#dia_order_stone_search input[name="pro_sn"]');
			pro_sn_input.next("span").on('click',function(){
				var pro_sn = pro_sn_input.val();
				if(pro_sn == ''){
					util.xalert('请填写采购单号');return;
				}

				$.ajax({
					url:info_form_base_url+'checkProSN' ,
					data:{'pro_sn':pro_sn},
					//dataType:"json",
					type:"POST",
					success:function(data) {
						if(data.success == 1 ){
							$('#dia_order_stone_add_info').empty().append(data.info);
							var is_batch = $('#dia_order_stone_search select[name="is_batch"]');
							if(data.pro.is_batch != NULL){
								is_batch.select2('val',data.pro.is_batch,true).attr('disabled','disabled');
							}
						}
						else
						{
							$('#dia_order_stone_add_info').empty()
							util.error(data);//错误处理
						}
					}
				});
			})
		};
		return {
			init:function(){
				initData();
				initElements();
			}
		}
	}();
	obj.init();
});