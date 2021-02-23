//表格初始化
var from_table_data_d = function(id , tdata , ttitle , ttype){
	var $container = $(id);
	$container.handsontable({
		data: tdata,
		rowHeaders:true,//是否显示序号
		startRows: 1, //初始化默认行数
		colHeaders:ttitle,//是否显示表头
		columns: ttype,
		columnSorting: true,//是否排序
		minSpareRows: 1,//预留新行
		contextMenu: true,//初始化右键菜单
		afterChange: function (change, source) {//改变内容之后执行的方法
			if (source != "loadData" && change[0][1] == 0 && change[0][2] != change[0][3]) {//跳过第一次/且第一列执行
				if(repeat(change[0][0],change[0][3])!= -1){
					util.xalert("录入信息重复");
					this.alter("remove_row", change[0][0],1);
				}else if($container.find("td").hasClass("htInvalid") == true){
					util.xalert("录入信息错误！");
					this.alter("remove_row", change[0][0], 1);
				}else{
	                var changarry = [],batch_x = change[0][0],changelength = change.length;
	                for (var i = 0; i < changelength; i++) {
	                    changarry.push(change[i][3]);
	                }					
					obtain_info(changarry,change[0][0],this);
				}
			}
		}
	});
	//输入信息获取剩余信息
	function obtain_info(idArry, x,obj){  //id 查询条件， table的x列数
		if (!id)
		{
			return false;
		}
		$.get("index.php?mod=warehouse&con=WarehouseBillInfoD&act=getGoodsInfoByGoodsID", {g_ids: idArry}, function(data){
			if(JSON.parse(data).success == 0){
				util.xalert(JSON.parse(data).error,function(){
					obj.alter("remove_row",x,1);//删除本行
				});
			}else{
				
				var datas = JSON.parse(data);
				for(var j = 0; j < datas.length; j++){
					$.each(datas[j], function(i, val) {
						$container.handsontable('getData')[x+j][i+1] = val;
					});
			    }
			}
		});
	}
	//遍历判断重复
	function repeat(x,y){
		var arr = $container.handsontable('getData');
		var newarr = [];
		for (var i = 0; i < arr.length; i++) {
			if (i != x) {
				newarr[i] = arr[i][0];
			}
		};
		return $.inArray(y, newarr);
	}

}