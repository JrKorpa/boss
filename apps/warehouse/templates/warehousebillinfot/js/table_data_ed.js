 // 表格初始化
var from_table_data_t = function (id,tdata,title,tcolumns) {
  var $container = $(id);
  //表格基本内容初始化
  $container.handsontable({
        data: tdata,
        //rowHeaders:true,//是否显示序号
        startRows: 1, //初始化默认行数
        colHeaders:title,//是否显示表头
        columns: tcolumns,
        columnSorting: true,//是否排序
        minSpareRows: 1,//预留新行
        contextMenu: true,//初始化右键菜单
        afterChange: function (change, source) {//改变内容之后执行的方法
          if (source != "loadData" && change[0][1] == 0) {//跳过第一次/且第一列执行
              if(repeat(change[0][0],change[0][3])!= -1){
                 bootbox.alert("录入信息重复");
                 this.alter("remove_row", change[0][0],1);//参数设定，remove_row方法名，change[0][0]删除的行数，1，删除1行
              }else{
                  obtain_info(change[0][3],change[0][0]);
              }
          }
        }
    });
  //输入信息获取剩余信息
  function obtain_info(id, x){  //id 查询条件， table的x列数
    $.get("index.php?mod=warehouse&con=WarehouseBillInfoT&act=getGoodsInfoByGoodsID", {goods_id: id}, function(data){
        if(JSON.parse(data).success == 0){
          bootbox.alert(JSON.parse(data).error);
        }else{
          $.each(JSON.parse(data), function(i, val) {
            $container.handsontable('getData')[x][i+1] = val;
          });
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