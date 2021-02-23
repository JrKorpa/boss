$import(function(){
	$('#menu_group_sort').disableSelection();
	$("#menu_group_sort ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {} });


	$('#menu_group_sort button').click(function(){
		var order = $('#menu_group_sort ul').sortable("serialize");
		if (order)
		{
			$.post("index.php?mod=management&con=MenuGroup&act=saveSort", order, function(data){
				if (data.success==1)
				{
					$('.modal-scrollable').trigger('click');//关闭遮罩
					util.xalert('操作完成'); 
				}
				else
				{
					util.error(data);
				}
			});
		}
		else
		{
			util.xalert("好像用左手点击才可能发现可排序菜单\r\n亲，你要试试吗？");
		}
	});
});