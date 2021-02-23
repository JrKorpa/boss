util.hover();
$(function(){
	$('table.flip-content tbody tr').each(function(){
		if ($(this).attr('del')==1)
		{
			$(this).children().each(function(){
				$(this).attr('style',"position:relative;");
				$(this).append('<div style="width:100%;position:absolute;top:14px;left:-1px;border-bottom:solid 1px red;"></div><div style="width:100%;position:absolute;top:19px;left:-1px;border-bottom:solid 1px red;"></div>');
			});
		}
	});
});