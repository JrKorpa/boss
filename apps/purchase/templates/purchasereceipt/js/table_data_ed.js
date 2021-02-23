// 表格初始化
var from_table_data = function (id,tdata,title,tcolumns) {
  var $container = $(id);
  //var autosaveNotification;
  //表格基本内容初始化
  $container.handsontable({
    data: tdata,
    //rowHeaders:true,//是否显示序号
    startRows: 1, //初始化默认行数
    colHeaders:title,//是否显示表头
    columns: tcolumns,
    columnSorting: false,//是否排序
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
}
