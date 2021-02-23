//分页
function diamond_list_4c_search_page(url){
	util.page(url);
}
//匿名回调
$import("public/js/select2/select2.min.js",function(){
    util.setItem('orl','index.php?mod=sales&con=DiamondListFourC&act=search');//设定刷新的初始url
	util.setItem('formID','diamond_list_4c_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_list_4c_search_list');//设定列表数据容器id

	//匿名函数+闭包

  
	var ListObj = function(){
		
		var initElements = function(){
			var test = $("#diamond_list_4c_search_form input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
			 	test.each(function () {
			   	if ($(this).parents(".checker").size() == 0) {
			     	$(this).show();
			     	$(this).uniform();
			    }
			  });
			}
			//初始化下拉组件
			$('#diamond_list_4c_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});//validator与select2冲突的解决方案是加change事件	
			
			$('#diamond_list_4c_search_form :reset').on('click',function(){
				$('#diamond_list_4c_search_form :checkbox').each(function(){
					$(this).parent().removeClass("active");
				})
			});
			//石重最多选一个，且选中值再次点击取消
			var carat_arr = [];
			$("input[type='checkbox'][name='carat']").change(function(){
				if($.inArray($(this).val(),carat_arr)>=0){
    				   return false;	
				}
				carat_arr.push($(this).val());
				if(carat_arr.length >1){				   
				   var pop_value = carat_arr.shift();
				   var pop_obj   = $("input[type='checkbox'][name='carat'][value='"+pop_value+"']");
				   pop_obj.parent().removeClass("active");
				   pop_obj.attr("checked",false);
				}
				
				
			});
			/*
			//切工最多选一个,且选中值再次点击取消
			var cut_arr = [];
			$("input[type='checkbox'][name='cut[]']").change(function(){
				if($.inArray($(this).val(),cut_arr)>=0){
					return false;	
				}
				cut_arr.push($(this).val());
				if(cut_arr.length >1){				   
				   var pop_value = cut_arr.shift();
				   var pop_obj   = $("input[type='checkbox'][name='cut[]'][value='"+pop_value+"']");
				   pop_obj.parent().removeClass("active");
				   pop_obj.attr("checked",false);
				}
				
			});
			//形状最多选一个,且选中值再次点击取消
			var shape_arr = [];
			$("input[type='checkbox'][name='shape[]']").change(function(){
				if($.inArray($(this).val(),shape_arr)>=0){
					return false;	
				}
				shape_arr.push($(this).val());
				if(shape_arr.length >1){				   
				   var pop_value = shape_arr.shift();
				   var pop_obj   = $("input[type='checkbox'][name='shape[]'][value='"+pop_value+"']");
				   pop_obj.parent().removeClass("active");
				   pop_obj.attr("checked",false);
				}
				
			});
			//净度最多选2个，且再次点击取消
			var clarity_arr = [];
			$("input[type='checkbox'][name='clarity[]']").change(function(){
				if($.inArray($(this).val(),clarity_arr)>=0){
					return false;	
				}
				clarity_arr.push($(this).val());
				if(clarity_arr.length > 2){				   
				   var pop_value = clarity_arr.shift();
				   var pop_obj   = $("input[type='checkbox'][name='clarity[]'][value='"+pop_value+"']");
				   pop_obj.parent().removeClass("active");
				   pop_obj.attr("checked",false);
				}
				
			});
			//颜色最多选2个，且再次点击取消
			var color_arr = [];
			$("input[type='checkbox'][name='color[]']").change(function(){
				if($.inArray($(this).val(),color_arr)>=0){
					return false;	
				}
				color_arr.push($(this).val());
				if(color_arr.length > 2){				   
				   var pop_value = color_arr.shift();
				   var pop_obj   = $("input[type='checkbox'][name='color[]'][value='"+pop_value+"']");
				   pop_obj.parent().removeClass("active");
				   pop_obj.attr("checked",false);
				}
				
			});*/
			
			//打开默认进行一次搜索
			diamond_list_4c_search_page(util.getItem("orl"));
			//4C搜索更多跳转链接，跳转
			$(".js_more_diamond_list").click(function(){
			   var url = "index.php?mod=sales&con=DiamondList&act=index&"+$('#diamond_list_4c_search_form').serialize();
			   $(this).attr('data-url',url);
			});
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){

		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
			}
		}	
	}();

	ListObj.init();
});

