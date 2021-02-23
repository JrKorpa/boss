function show_user_power(o){
	if($('#user_info_tab_5').html()==''){
		var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();

		$.post('index.php?mod=management&con=User&act=showPower',{id:_id},function(data){
			$('#user_info_tab_5').html(data);
		});
	}
}

$import(function(){

});