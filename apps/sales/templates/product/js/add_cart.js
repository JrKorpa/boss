$import(function(){
	var obj = function(){
        var ids ='<%$ids%>';
			var initElements = function(){
				var url = "index.php?mod=sales&con=Product&act=getChannelprice";
				var data = {qudao:'<%$channel_id%>',ids:ids};
				$.post(url,data,function(data){
					$('#qudao_cart_list').empty().html(data);
				});
						
                $('#add_cart_form select[name=qudao]').select2({
                        placeholder: "请选择销售渠道",
                        allowClear: true
                    }).change(function(e){
                        var url = "index.php?mod=sales&con=Product&act=getChannelprice";
                        var data = {qudao:$(this).val(),ids:ids};
                        $.post(url,data,function(data){
                            $('#qudao_cart_list').empty().html(data);
                        })
                    });

               var GetCartGoodsurl = "index.php?mod=sales&con=Product&act=GetCartGoods";
                $.get(GetCartGoodsurl,function(data){
                    $('#cart_goods_list').empty().html(data);
                });

            }
			var initData = function(){}
			var handleForm = function(){}
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
	}();
	obj.init();
				 
});


function product_add_cart(obj){
    var id = $(obj).parent().parent().attr('data-title');
	var goodsid = $(obj).parent().parent().attr('data-goodsid');
	var isxianhuo = $(obj).parent().parent().attr('data-xh');
	var keys = $(obj).parent().parent().attr('data-keys');
    var type = $(obj).next().val();
    var department = $('#add_cart_form select[name=qudao]').val();
	var chengpindingzhi = $('#add_cart_form select[name=qudao]').val();
	var url = "index.php?mod=sales&con=Product&act=SaveCartGoods";
	var data = {type:type,id:id,department:department,goodsid:goodsid,isxianhuo:isxianhuo,keys:keys};
	$.post(url,data,function(data){
		if(data.error>0){
			util.xalert(data.content);
			return false;
		}else{
			$('#cart_goods_list').empty().html(data);
		}
		
	})
}