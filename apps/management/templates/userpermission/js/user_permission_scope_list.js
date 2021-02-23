function user_permission_scope_expandall(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
	$('#user_permission_scope_info>ul>li>div>div.title').each(function(){
		var obj = $(this).next();
		obj[0].style.display = 'block';
	});
}

function user_permission_scope_collapseall(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        $('#user_permission_scope_info>ul>li>div>div.title').each(function(){
		var obj = $(this).next();
		obj[0].style.display = 'none';
	});
}

function user_permission_scope_reload(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        $("#user_permission_search_list ul li a:eq(7)").click();
}

function user_permission_scope_set0(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        App.blockUI({target: $('#user_permission_scope'), iconOnly: true});
        $('#user_permission_scope_info select').each(function(){
		$(this).val(0).change();
	});
        App.unblockUI($('#user_permission_scope'));
}
function user_permission_scope_set1(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        App.blockUI({target: $('#user_permission_scope'), iconOnly: true});
        $('#user_permission_scope_info select').each(function(){
		$(this).val(1).change();
	});
        App.unblockUI($('#user_permission_scope'));
}
function user_permission_scope_set2(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        App.blockUI({target: $('#user_permission_scope'), iconOnly: true});
        $('#user_permission_scope_info select').each(function(){
		$(this).val(3).change();
	});
        App.unblockUI($('#user_permission_scope'));
}
function user_permission_scope_save(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_scope').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        var flag = false;
	var url = 'index.php?mod=management&con=UserPermission&act=saveScope';

	var options1 = {
		url: url,
		error:function ()
		{
			$('.modal-scrollable').trigger('click');
			util.xalert("请求超时，请检查链接");
		},
		beforeSubmit:function(frm,jq,op){
			flag=true;
                        App.blockUI({target: $('#user_permission_scope'), iconOnly: true});
		},
		success: function(data) {
			flag=false;
                        App.unblockUI($('#user_permission_scope'));
			if(data.success == 1 ){
				$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
				//$('body').modalmanager('removeLoading');//关闭进度条
				util.xalert("授权成功!");
			}
			else
			{
				util.error(data);
			}
		}
	};
	if (!flag)
	{
		$('#user_permission_scope_info').ajaxSubmit(options1);;
	}
}
$import("public/js/select2/select2.min.js",function(){
	var obj = function(){
		var initElements = function(){
			util.hover();
			$('#user_permission_scope_info select').select2({
				placeholder: "请选择",
				allowClear: true
			});	
			$('#user_permission_scope_info>ul>li>div>div.title').on('click',function(e){
				e.preventDefault();//禁止冒泡
				var obj = $(this).next();
				obj[0].style.display = obj[0].style.display=='block' ? 'none': 'block';
			});
		}
		var handleForm=function(){
		
		
		}
		var initData = function(){}
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