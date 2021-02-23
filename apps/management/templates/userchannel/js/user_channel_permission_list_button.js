function user_channel_permission_list_button_expandall(o){
	$('#user_channel_permission_list_button_info ol').each(function(){
		this.style.display='block';
	});
}

function user_channel_permission_list_button_collapseall(o){
	$('#user_channel_permission_list_button_info ol').each(function(){
		this.style.display='none';
	});
}

function user_channel_permission_list_button_selectall(o){
	user_channel_permission_list_button_expandall(o);
	$('#user_channel_permission_list_button_info :checkbox').attr('checked',true);
}

function user_channel_permission_list_button_selectnone(o){
	user_channel_permission_list_button_expandall(o);
	$('#user_channel_permission_list_button_info :checkbox').attr('checked',false);
}


function user_channel_permission_list_button_reload(o){
	$("#user_channel_permission_search_list>ul>li:eq(2)>a").click();
}


function user_channel_permission_list_button_save(o){
	$("#user_channel_permission_list_button_info").submit();
}

$import(function(){
	var _obj = function(){
		var initElements = function(){
			$('#user_channel_permission_list_button_info>ul>li>div>div>span').click(function(){
				var obj = $(this).parent().next();
				obj[0].style.display = obj[0].style.display=='block' ? 'none': 'block';
			});

			$('#user_channel_permission_list_button_info>ul>li>div>div>input').click(function(){
				var obj = $(this).parent().next();
				obj[0].style.display='block';
				if (this.checked)
				{
					$(this).parent().next().find(':checkbox').attr('checked',true);
				}
				else
				{
					$(this).parent().next().find(':checkbox').attr('checked',false);
				}
			});		
		
		}

		var handleForm = function(){
			var options1 = {
				url: "index.php?mod=management&con=UserChannel&act=saveListButton",
				beforeSubmit:function(frm,jq,op){
					frm.push({name:'user_id',value:util.getItem('user_id')});
					frm.push({name:'channel_id',value:util.getItem('channel_id')});
				},
				error:function ()
				{
					util.xalert('网络异常');
				},
				success:function(data){
					if(data.success == 1 ){
						util.xalert("授权成功!");
					}
					else
					{
						util.xalert("授权失败!");
					}				
				}
			};

			$("#user_channel_permission_list_button_info").ajaxForm(options1);		
		
		}

		return {
			init:function(){
				initElements();
				handleForm();
			}
		}
	
	}();
	_obj.init();
});