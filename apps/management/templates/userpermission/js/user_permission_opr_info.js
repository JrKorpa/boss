var user_button_info_user_id = '<%$user_id%>';
var user_button_info_parent_id = '<%$parent_id%>';

function user_permission_opr_selectall(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_opr_list').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
	$('#user_permission_opr_info1>div>div').each(function(){
		if ($(this).hasClass('checkbuttonNo'))
		{
			$(this).removeClass('checkbuttonNo').addClass('checkbuttonOk');
			$(this).children('.triangleNo').removeClass('triangleNo').addClass('triangleOk');
		}
	});
}

function user_permission_opr_save(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_opr_list').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
        var ids=[];
	$('#user_permission_opr_info1 .checkbuttonOk .checktext').each(function(){
		ids.push($(this).attr('id'))
	});
        App.blockUI({target: $('#user_permission_opr_list'), iconOnly: true});
	$.post('index.php?mod=management&con=UserPermission&act=saveOpr',{user_id:user_button_info_user_id,parent_id:user_button_info_parent_id,ids:ids},function(data){
		if (data.success==1)
		{
			util.xalert('操作完成',function(){
                                App.unblockUI($('#user_permission_opr_list'));
                        });
		}
		else
		{
			util.error(data);
                        App.unblockUI($('#user_permission_opr_list'));
		}
	});
}

function user_permission_opr_sync(o){
        var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
        if (!parseInt(user_id))
        {
                $('.modal-scrollable').trigger('click');
                $('#user_permission_opr_list').html('');
                util.xalert('很抱歉，请选择用户！');
                return false;
        }
	$('#user_permission_opr_info1>div>div').each(function(){
		if ($(this).hasClass('checkbuttonNo'))
		{
			if ($(this).children('.checktext').children('span').length>0)
			{
				$(this).removeClass('checkbuttonNo').addClass('checkbuttonOk');
				$(this).children('.triangleNo').removeClass('triangleNo').addClass('triangleOk');
			}
		}
	});
}

$import(function(){
	$('#user_permission_opr_info1').disableSelection();
	$("#user_permission_opr_info1>div>div").click(
		function(event) {
			event.preventDefault();
			if ($(this).hasClass('checkbuttonOk'))
			{
				$(this).removeClass('checkbuttonOk').addClass('checkbuttonNo');
				$(this).children('.triangleOk').removeClass('triangleOk').addClass('triangleNo');
			}
			else
			{
				$(this).removeClass('checkbuttonNo').addClass('checkbuttonOk');
				$(this).children('.triangleNo').removeClass('triangleNo').addClass('triangleOk');
			}
		}
	);
});
