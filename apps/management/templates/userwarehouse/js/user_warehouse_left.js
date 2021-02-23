$import(function(){
	util.hover();
	$('#user_warehouse_search_form table').dataTable( {
		"language": {
			"lengthMenu": "每页 _MENU_ 条",
			"zeroRecords": "无匹配",
			"infoEmpty": "无匹配",
			"infoFiltered": "",
			"sSearch":'搜',
			"info": "合计：_TOTAL_",
			"sSearchPlaceholder":"仓库名称/编号",
			"paginate":{
				"first": "|<<",
				"previous": "<",
				"next": ">",
				"last": ">>|"
			}
		},
		"paging":true,//分页
		"ordering": false,//排序
		"info":true,//统计
		"classes":{
			"sFilterInput":"form-control yangfuyou",
			"sLengthSelect":"yangfy"
		}
	} );

	$('#user_warehouse_search_form table tbody').on('click', 'tr', function () {
		var id = parseInt($('td', this).eq(0).text());
		if(id)
		{
			util.setItem('orl','index.php?mod=management&con=UserWarehouse&act=search&house_id='+$('td', this).eq(0).text());//设定刷新的初始url
                        $('#user_warehouse_search_result_form input[name=house_id]').val($('td', this).eq(0).text());
			user_warehouse_search_page(util.getItem('orl'));
		}
    } );
});