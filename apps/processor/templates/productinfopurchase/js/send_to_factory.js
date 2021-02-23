var test = $("#product_info_purchase_send_to_factory input[type='checkbox']:not(.toggle, .make-switch)");
	if (test.size() > 0) {
	 	test.each(function () {
	   	if ($(this).parents(".checker").size() == 0) {
	     	$(this).show();
	     	$(this).uniform();
	    }
	  });
	}
  $('#product_info_purchase_send_to_factory .group-checkable').change(function () {
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
	$('#product_info_purchase_send_to_factory').on('change', 'tbody tr .checkboxes', function(){
  	$(this).parents('tr').toggleClass("active");
  });
  function ProductInfoSendToFactory(obj)
  {
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var ids='';
	$('#product_info_purchase_send_to_factory input[name="_ids[]"]:checked').each(function(){
	   ids+=$(this).val()+',';
	});
	ids=ids.substring(0,ids.length-1);
	var id="<%$id|default:0%>";
	$.post(url,{id:id,ids:ids},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
							    util.retrieveReload(obj)
								$('.modal-scrollable').trigger('click');
							});
							return true;
						}
						else{
							util.xalert(data.error);
							$('.modal-scrollable').trigger('click');
							return true;
						}
					});
					return false;
}