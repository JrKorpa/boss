util.hover();
$('table.flip-content tbody tr').each(function(){
	if ($(this).attr('del')==1)
	{
		$(this).children().each(function(){
			$(this).attr('style',"position:relative;");
			$(this).append('<div style="width:100%;position:absolute;top:14px;left:-1px;border-bottom:solid 1px red;"></div><div style="width:100%;position:absolute;top:19px;left:-1px;border-bottom:solid 1px red;"></div>');
		});
	}
});

	//复选框组美化
var test = $("#batch_express_search_list input[type='checkbox']:not(.toggle, .make-switch)");
if (test.size() > 0) {
	test.each(function () {
	if ($(this).parents(".checker").size() == 0) {
		$(this).show();
		$(this).uniform();
	}
  });
}
// table 复选框全选
$('#batch_express_search_list .group-checkable').change(function () {
  var set = $(this).attr("data-set");
	var checked = $(this).is(":checked");
	$(set).each(function () {
		if (checked) {
			$(this).attr("checked", true);
			$(this).parents('tr').addClass("active");
		} else {
			$(this).attr("checked", false);
			$(this).parents('tr').removeClass("active");
		}                    
	});
	$.uniform.update(set);
});
$('#batch_express_search_list').on('change', 'tbody tr .checkboxes', function(){
	$(this).parents('tr').toggleClass("active");
});