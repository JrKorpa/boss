function user_channel_permission_menu_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_menu_list').html(data);
	});	
}

function user_channel_permission_opr_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_opr_list').html(data);
	});	
}

function user_channel_permission_button_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_button_list').html(data);
	});	
}

function user_channel_permission_button_view_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_view_button_list').html(data);
	});	
}

function user_channel_permission_rel_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_rel_list').html(data);
	});	
}

function user_channel_permission_rel_button_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_rel_button').html(data);
	});	
}

function user_channel_permission_rel_opr_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_rel_opr').html(data);
	});	
}
 
function user_channel_permission_scope_add(o)
{
	$.post($(o).attr('data-url'),{user_id:util.getItem('user_id'),channel_id:util.getItem('channel_id')},function(data){
		$('#user_channel_permission_scope').html(data);
	});	
}

$import(function(){
	util.setItem('user_id',parseInt('<%$data.user_id%>'));
	util.setItem('channel_id',parseInt('<%$data.channel_id%>'));
	$("#user_channel_permission_search_list ul li a:eq(0)").click();
});