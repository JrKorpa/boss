function productInfoBatchStartToFactory(obj)
{
	var _ids = [];
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
		_ids.push($(this).val());
	});
	if (!_ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}
	var url = $(obj).attr('data-url');
	url+='&rand=';
	$.post(url,{'_ids':_ids},function(data){
						if(data.success==1)
						{
								
								$(obj).attr('data-url','index.php?mod=processor&con=ProductInfo&act=StartProductionEdit');
								util.pop3(obj);
								$(obj).attr('data-url',url);
								return;
						}
						else
						{
							$(obj).attr('data-url','index.php?mod=processor&con=ProductInfo&act=to_factory_pl');
							util.batchConfirm(obj);
							$(obj).attr('data-url',url);
							return;

						}

	});
}
