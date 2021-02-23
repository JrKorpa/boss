$import(function(){
	var obj = function(){
			var initElements = function(){
				$('#style_attribute select').select2({
					placeholder: "请选择",
					allowClear: true
				
				}).change(function(e){
					$(this).valid();
				});	
				//初始化单选按钮组
				if (!jQuery().uniform) {
					return;
				}
				var test = $(".radio-inline input:not(.toggle, .star, .make-switch)");
				if (test.size() > 0) {
					test.each(function (){
						if ($(this).parents(".checker").size() == 0){
							$(this).show();
							$(this).uniform();
						}
					});
				}
			}
			var initData = function(){}
			var handleForm = function(){}
		
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
		
	}();
	obj.init();
				 
});