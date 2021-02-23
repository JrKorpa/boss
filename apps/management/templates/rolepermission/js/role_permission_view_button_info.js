var role_view_button_list_role_id_20141225 = '<%$role_id%>';
var role_view_button_list_parent_id = '<%$parent_id%>';

function role_permission_view_button_selectall(o){
	$('#role_permission_view_button_info1>div>div').each(function(){
		if ($(this).attr('class')=='checkbuttonNo')
		{
			$(this).removeClass('checkbuttonNo').addClass('checkbuttonOk');
			$(this).children('.triangleNo').removeClass('triangleNo').addClass('triangleOk');
		}
	});
}

function role_permission_view_button_save(o){
	var ids=[];
	$('#role_permission_view_button_info1 .checkbuttonOk .checktext').each(function(){
		ids.push($(this).attr('id'))
	});
	$.post('index.php?mod=management&con=RolePermission&act=saveViewButton',{role_id:role_view_button_list_role_id_20141225,parent_id:role_view_button_list_parent_id,ids:ids},function(data){
		if (data.success==1)
		{
			util.xalert('操作完成');
		}
		else
		{
			util.xalert(data.error ? data.error : (data ? data :'程序异常'));
		}
	});
}

$import(function(){
	$('#role_permission_view_button_info1').disableSelection();
	$("#role_permission_view_button_info1>div>div").click(
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
