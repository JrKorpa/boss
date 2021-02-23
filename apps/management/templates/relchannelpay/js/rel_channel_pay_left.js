$import(function(){
	util.hover();
	$('#rel_sale_channels_search_form table').dataTable( {
		"language": {
			"lengthMenu": "每页 _MENU_ 条",
			"zeroRecords": "无匹配",
			"infoEmpty": "无匹配",
			"infoFiltered": "",
			"sSearch":'搜',
			"info": "合计：_TOTAL_",
			"sSearchPlaceholder":"销售渠道/序号",
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

	$('#rel_sale_channels_search_form table tbody').on('click', 'tr', function () {
		var id = parseInt($('td', this).eq(0).text());
		if(id)
		{
			util.setItem('orl','index.php?mod=management&con=RelChannelPay&act=search&channel_id='+$('td', this).eq(0).text());//设定刷新的初始url
            $('#rel_channel_pay_search_form input[name=house_id]').val($('td', this).eq(0).text());
			rel_channel_pay_search_page(util.getItem('orl'));
		}
    } );
});