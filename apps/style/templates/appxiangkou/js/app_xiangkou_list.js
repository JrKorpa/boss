

function app_xiangkou_create_goods(style_id,stone,key_stone){

    $("#sub_mat"+key_stone).button('loading');
	data = {style_id:style_id,stone:stone};
	url = 'index.php?mod=style&con=AppXiangkou&act=createGoods';
	formdata = $("#app_xiangkou_form").serialize();
	
	$.ajax({
			type: 'POST',
			url: url+"&style_id="+style_id+"&stone_select="+stone,
			data: formdata,
			dataType: 'json',
			async: false,
			success: function (res) {
				if(res.success >0){
                    alert(res.error);
                    $("#sub_mat"+key_stone).button('reset');
        			$('#1111111').trigger('click');
        		}else{
                    $("#sub_mat"+key_stone).button('reset');
        			 alert(res.error);
                     $('#1111111').trigger('click');
        		}
			},
			error:function(res){
                $("#sub_mat"+key_stone).button('reset');
				alert('Ajax出错!');
                $('#1111111').trigger('click');
			}
	});

}


