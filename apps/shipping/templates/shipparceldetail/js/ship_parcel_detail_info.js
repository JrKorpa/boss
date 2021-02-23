$import(function(){
	var info_form_id = 'ship_parcel_detail_info';//form表单id
	var info_form_base_url = 'index.php?mod=shipping&con=ShipParcelDetail&act=';//基本提交路径
	var info_id= '<%$view->get_id()%>';//记录主键

	var obj = function(){
		var initElements = function(){
				
		};
		
		//表单验证和提交
		var handleForm = function(){
		$('#ship_parcel_detail_info button').click(function(){
				amountLim();
			});
			//回车提交
			$('#'+info_form_id+' input').keypress(function (e) {
				if (e.which == 13) {
					amountLim();
					return false;
				}
			});
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


function get_package()
{
	var info_form_id = 'ship_parcel_detail_info';//form表单id
	var _no = $('#'+info_form_id+' input[name="zhuancang_sn"]').val();
	var _id = $('#'+info_form_id+' input[name="_id"]').val();

	if(_no == ''){
		bootbox.alert('请输入调拨单号');
	}else{
		$('body').modalmanager('loading');
		$('#ship_parcel_detail_info button').attr('disabled','disabled');
		var url = 'index.php?mod=shipping&con=ShipParcelDetail&act=insert';
		var data = {'zhuancang_sn':_no,'_id':_id};
		$.post(url,data,function(res){
			if (res.success==1)
			{
				bootbox.alert('添加成功');
				util.retrieveReload();

			}
			else
			{
				bootbox.alert(res.error);
			}
			$('.modal-scrollable').trigger('click');
			$('#ship_parcel_detail_info button').removeAttr('disabled');
		});
		return false;
	}
}
function amountLim()
{
	var info_form_id = 'ship_parcel_detail_info';//form表单id
	var url = 'index.php?mod=shipping&con=ShipParcelDetail&act=amountMax';
	var _no = $('#'+info_form_id+' input[name="zhuancang_sn"]').val();
	var _id = $('#'+info_form_id+' input[name="_id"]').val();

	var data = {'zhuancang_sn':_no,'_id':_id};
		//验证是否超过40万
	$.post(url,data,function(res)
	{
		if (res.success==1)
		{
			get_package();
		}
		else
		{
			if (res.num==1)
			{
				bootbox.alert(res.error);	
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
		}
	});

}