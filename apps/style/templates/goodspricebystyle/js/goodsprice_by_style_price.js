function update_price(){
	var url = "index.php?mod=style&con=GoodsPriceByStyle&act=updateAttrPrice";
	var data = "";
	$("#register_express_form .freight_no").each(function(){
		var key = $(this).attr("name");
		var val = $(this).val();
		data += "&"+key+"="+val;
	});
	$.post(url,data,function(res){
		if(res.success){
			util.xalert("操作成功",function(){
			    util.retrieveReload();							
			});	
		}else if(res.error){
			util.xalert(res.error);	
		}else{
		   util.xalert(res.toString());	
		}							 
    },'json');
}