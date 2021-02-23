//匿名回调
$import(['public/js/select2/select2.min.js'],function(){

	//匿名函数+闭包
	var obj = function(){
		var handleForm = function(){
			//sreach button
			$('#zhanting_search_form button').click(function(){
				amountLim();
                 $('#zhanting_search_form button').attr('disabled','disabled');
			});
			//回车提交
			$('#zhanting_search_form input[name="zhuancang_sn"]').keypress(function (e) {
				if (e.which == 13) {
				     
					amountLim();
                    $('#zhanting_search_form button').attr('disabled','disabled');    
					/*
					var _no = $('#zhanting_search_form input[name="zhuancang_sn"]').val();
					if(_no == ''){
						// bootbox.alert('请输入调拨单号');
						$('#notes').empty().append("<div class='alert alert-info'>请填写调拨单号！</div>");
						return false;
					}else{
						$('body').modalmanager('loading');
						$('#notes').empty();
						var url = 'index.php?mod=shipping&con=ZhantingFahuo&act=search';
						var data = {'zhuancang_sn':_no};
						$.post(url,data,function(res){
							$('.modal-scrollable').trigger('click');
							$('#zhanting_search_list').append(res);
							$('#zhanting_search_form input[name="zhuancang_sn"]').val('').focus();
						});
					return false;		//防止js往下运行，导致页面跳到首页
					}
					*/
					return false;	
				}
			});
		};		//end handleForm()

		return {
			init:function(){
				handleForm();//处理表单验证和提交
			}
		}
	}();
	obj.init();
});

function get_package()
{

	var _no = $('#zhanting_search_form input[name="zhuancang_sn"]').val();
	if(_no == ''){
		//$('#zhanting_search_list').empty().append("<div class='alert alert-info'>请输入调拨单号！</div>");
		bootbox.alert('请输入调拨单号');
	}else{
		$('body').modalmanager('loading');
		 $('#zhanting_search_form button').attr('disabled','disabled');
		var url = 'index.php?mod=shipping&con=ZhantingFahuo&act=search';
		var data = {'zhuancang_sn':_no};
		$.post(url,data,function(res){
			$('.modal-scrollable').trigger('click');
			$('#zhanting_search_list').prepend(res);
			$('#zhanting_search_form input[name="zhuancang_sn"]').val('').focus();
			$('#zhanting_search_form button').removeAttr('disabled');
		});
		return false;
	}
}
function amountLim()
{
	var url = 'index.php?mod=shipping&con=ZhantingFahuo&act=amountMax';
	var _no = $('#zhanting_search_form input[name="zhuancang_sn"]').val();
	var data = {'zhuancang_sn':_no};
    
		//验证是否超过40万
	$.post(url,data,function(res)
	{
	   $('#zhanting_search_form button').removeAttr('disabled');
		if (res.success==1)
		{
			get_package();
            	
		}
		else
		{
			bootbox.confirm(res.error+"确定继续执行?", function(result) 
			{
				if (result == true) 
				{
					get_package();
				}
			});
		}
	});

}
