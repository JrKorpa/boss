function user_permission_check_user(o){
    if(o.value==''){
       $("#user_permission_user_search_form input[name='user_id']").val(0); 
    }
}

function user_permission_menu_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_menu_list').html(data);
                App.unblockUI($('#user_permission_search_list'));
                
	})
}

function user_permission_opr_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_opr_list').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

function user_permission_button_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_button_list').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

function user_permission_view_button_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_view_button_list').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

function user_permission_rel_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_rel_list').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}
	
function user_permission_rel_button_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_rel_button').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

function user_permission_rel_opr_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_rel_opr').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

function user_permission_scope_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_scope').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

function user_permission_copy_add(o){
	var user_id = $("#user_permission_user_search_form input[name='user_id']").val();
	if (!parseInt(user_id))
	{
		$('.modal-scrollable').trigger('click');
                $('#user_permission_menu_list').html('');
		util.xalert('很抱歉，请选择用户！');
		return false;
	}
        App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
	$.post($(o).attr('data-url'),{user_id:user_id},function(data){
		$('#user_permission_copy_list').html(data);
                App.unblockUI($('#user_permission_search_list'));
	})
}

$import(['public/js/jquery-autocomplete/jquery.autocomplete.css','public/js/jquery-autocomplete/jquery.autocomplete.js'],function(){
	$("#user_permission_user_search_form input[name='name']").autocomplete('index.php?mod=management&con=UserPermission&act=search', {
		matchContains: true,
		formatItem: function(row){return row[0]},
		formatResult: function(row) {return row[0].replace(/(<.+?>)/gi, '')}
	}).result(function(event,item) {
		$("#user_permission_user_search_form input[name='user_id']").val(item[1]);
		$('#user_permission_menu_list').html('');
                App.blockUI({target: $('#user_permission_search_list'), iconOnly: true});
		$("#user_permission_search_list ul li:eq(0)").addClass('active').siblings().removeClass('active');
		$.post($("#user_permission_search_list ul li a:eq(0)").attr('data-url'),{user_id:item[1]},function(data){
			$('#user_permission_menu_list').html(data);
                        App.unblockUI($('#user_permission_search_list'));
			$('#user_permission_menu_list').addClass('active');
		});
	});
});