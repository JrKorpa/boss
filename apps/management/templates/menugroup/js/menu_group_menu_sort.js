$import(function(){
	$('#menu_group_menu_sort').disableSelection();
	$("#menu_group_menu_sort ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {} });


	$('#menu_group_menu_sort button').click(function(){
		var order = $('#menu_group_menu_sort ul').sortable("serialize");
		if (order)
		{
			$.post("index.php?mod=management&con=MenuGroup&act=saveMenuSort", order, function(data){
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
			util.xalert( "听说换个姿势就能找到可排序元素\r\n亲，你要不要试试？");
		}
	});
});