var test = $("#product_info_send_to_factory input[type='checkbox']:not(.toggle, .make-switch)");
	if (test.size() > 0) {
	 	test.each(function () {
	   	if ($(this).parents(".checker").size() == 0) {
	     	$(this).show();
	     	$(this).uniform();
	    }
	  });
	}
  $('#product_info_send_to_factory .group-checkable').change(function () {
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
	$('#product_info_send_to_factory').on('change', 'tbody tr .checkboxes', function(){
  	$(this).parents('tr').toggleClass("active");
  });
  function ProductInfoApiSendToFactory(obj)
  {
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var ids='';
	$('#product_info_send_to_factory input[name="_ids[]"]:checked').each(function(){
	   ids+=$(this).val()+',';
	});
	ids=ids.substring(0,ids.length-1);
	var _ids="<%$ids%>";
	_ids=_ids.split(',');
	
	$.post(url,{_ids:_ids,send_ids:ids},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
								

							    util.sync(obj);
							});
							return true;
						}
						else{
							$('.modal-scrollable').trigger('click');
							util.xalert(data.error,function(){
//								debugger;
							    util.sync($('#'+getID()).find('.table-toolbar .btn-group button:eq(0)'));
							});
							return true;
						}
					});
					return false;
}