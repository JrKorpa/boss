//匿名回调
$import(function(){

		$("#batch_under_carriage_form button[name=carriage]").click(
			function(){
				$('body').modalmanager('loading');//进度条和遮罩
				var ids=$('#batch_under_carriage_form textarea[name=ids]').val();
				if(ids.length==0)
				{
					util.xalert('请输入要下架的货！');
					return false;
				}
				ids=ids.replace(/\s+/g,',');
				var data = {goods:ids};
				var url = "index.php?mod=warehouse&con=GoodsWarehouse&act=BatchUndercarriage&get=1&tab_id=<%$tab_id%>";

				$.post(url , data, function(res){
					$('#error_list').html(res.error);
					$('#num span').html(res.num);
					$('#ids').val('').focus();
					$('.modal-scrollable').trigger('click');// 关闭遮罩
				})
				return false;
			}
		);

});
