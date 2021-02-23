util.hover();
var add_form_id  = 'batch_exchange_distribution_add_form';//提交换货form表单id
var info_form_base_url = 'index.php?mod=warehouse&con=BatchWaitDistribution&act=';//基本提交路径
$import(["public/js/select2/select2.min.js"],function(){
	var obj = function(){
		var initElements = function(){
			$('#'+add_form_id+' select').select2({
			    placeholder: "请选择",
			    allowClear: false,
			}).change(function(e){
                $(this).valid();
            });
			
		};	
		var handleForm = function(){
  			
		};
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
