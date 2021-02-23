//分页
function material_order_goods_add_list_page(url){
	util.page(url);
}
$import(["public/js/select2/select2.min.js"],function(){
	var info_form_id = 'material_order_goods_add_list_form';//form表单id
	util.setItem('formID',info_form_id);//设定搜索表单id
	util.setItem('orl','index.php?mod=sales&con=MaterialOrder&act=addOrderGoodsSearch&_id='+getID().split('-').pop());
	util.setItem('listDIV','material_order_goods_add_list');
	var obj = function(){
		var initElements = function(){
			/*
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});	*/	
			var test = $("#"+info_form_id+" input[type='radio']");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			
			$("#"+info_form_id+" input[name='goods_type']").change(function(){
				 $("#"+info_form_id).submit();													
			});
		};
		
		//表单验证和提交
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			material_order_goods_add_list_page(util.getItem("orl"));
			
			$('#'+info_form_id+' :reset').on('click',function(){
			    //$('#'+info_form_id+' select').select2('val','').change();//single
			});		
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