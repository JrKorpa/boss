$import(["public/js/jquery-datatables/js/jquery.dataTables.min.js",
	"public/js/jquery-datatables/css/jquery.dataTables.css"],function(){
	util.hover();
	$('#product_has_goods_result_list table').dataTable( {
		"aLengthMenu": [[5, 10, 25], [5, 10, 25]],
		//"iDisplayLength":5,
		"bLengthChange": false,
		"bFilter":false,
		"sPaginationType": "full_numbers",
		"bAutoWidth": true, //自适应宽度
		"language": {
			"sProcessing": "正在加载中......",
			"lengthMenu": "每页 _MENU_ 条",
			"zeroRecords": "对不起，查询不到相关数据",
			"sInfoEmpty": "没有数据",
			"infoFiltered": "",
			"sSearch":'搜索',
			"sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
			"sSearchPlaceholder":"货号",
			"oPaginate": {
				"sFirst": "首页",
				"sPrevious": "上一页",
				"sNext": "下一页",
				"sLast": "末页"
			}
		},
		"paging":true,//分页
		"ordering": false,//排序
		"info":true,//统计
		"classes":{
			"sFilterInput":"form-control js_input",
			"sLengthSelect":"js_select"
		}
	} );

	$('#product_has_goods_result_list table tbody').on('click', 'tr', function () {
		var id = parseInt($('td', this).eq(0).text());
		if(id)
		{
			$('#product_has_goods_result_list input[name="select_goods_id"]').val(id)
		}
     } );

});