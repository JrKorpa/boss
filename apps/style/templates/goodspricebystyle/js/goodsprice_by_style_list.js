util.hover();
function deleteSalepolicy(obj){
    var url = $(obj).attr("data-url");
    var style_sn = $('#goodsprice_by_style_edit input[name="style_sn"]').val();
	var ids = [];

	$('#goodsprice_by_style_salepolicy input[name="_ids[]"]:checked').each(function(){
		ids.push($(this).val());
	});	
	if(ids.toString()==""){
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	if(!confirm("是否确定删除选中的销售政策？")){
	    return false;	
	}
     $.post(url,{'_ids':ids},function(res){
		if(res.success==1){
			util.xalert("删除成功！");	    
			//util.retrieveReload();
			 $.post(     'index.php?mod=style&con=GoodsPriceByStyle&act=getAttrSalepolicyList',"style_sn="+style_sn,function(res){
					$("#goodsprice_by_style_salepolicy").html(res);
			 });

		}else if(res.error){
			util.xalert(res.error);	
		}else{
			util.xalert(res);	
		}
	});
}
function deleteSalepolicyAll(obj){
    var url = $(obj).attr("data-url");
    var style_sn = $('#goodsprice_by_style_edit input[name="style_sn"]').val();
	if(style_sn==""){
		util.xalert("很抱歉，没有指定款号！");
		return false;
	}
	if(!confirm("确定清空该款下所有销售政策？删除不可恢复!")){
	    return false;	
	}
	$.post(url,{'style_sn':style_sn},function(res){
		if(res.success==1){
			util.xalert("删除成功！");	    
    		$.post('index.php?mod=style&con=GoodsPriceByStyle&act=getAttrSalepolicyList',"style_sn="+style_sn,function(res){
					$("#goodsprice_by_style_salepolicy").html(res);
			});	
		}else if(res.error){
			util.xalert(res.error);	
		}else{
			util.xalert(res);	
		}
	});
}

	//复选框组美化
var test = $("#goodsprice_by_style_salepolicy input[type='checkbox']:not(.toggle, .make-switch)");
if (test.size() > 0) {
	test.each(function () {
	if ($(this).parents(".checker").size() == 0) {
		$(this).show();
		$(this).uniform();
	}
  });
}
// table 复选框全选
$('#goodsprice_by_style_salepolicy .group-checkable').change(function () {
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
$('#goodsprice_by_style_salepolicy_list').on('change', 'tbody tr .checkboxes', function(){
	$(this).parents('tr').toggleClass("active");
});


