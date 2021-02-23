$import(function(){
	$('#menu_buttons').disableSelection();
	$("#menu_buttons>div>div>div>div").click(
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

	$('#menu_buttons button').click(function(){
		var menu_id = $('#menu_buttons input[type="hidden"][name="menu_id"]').val();
		var ids=[];
		$('#menu_buttons .checkbuttonOk .checktext').each(function(){
			ids.push($(this).attr('id'))
		});
		$.post('index.php?mod=management&con=Menu&act=saveButton',{menu_id:menu_id,ids:ids.join()},function(data){
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

	});

});