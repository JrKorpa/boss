// 表格初始化
var from_table_data_e_edit = function (id,tdata,title,tcolumns) {
  var $container = $(id);
  //表格基本内容初始化
  $container.handsontable({
        data: tdata,
        rowHeaders:true,//是否显示序号
        startRows: 1, //初始化默认行数
        colHeaders:title,//是否显示表头
        columns: tcolumns,
        columnSorting: true,//是否排序
        minSpareRows: 1,//预留新行
        contextMenu: true,//初始化右键菜单
        afterChange: function (change, source) {//改变内容之后执行的方法
          if (source != "loadData" && change[0][1] == 0) {//跳过第一次/且第一列执行
              if(repeat(change[0][0],change[0][3])!= -1){
                  util.xalert("录入信息重复");
                  this.alter("remove_row", change[0][0],1);//参数设定，remove_row方法名，change[0][0]删除的行数，1，删除1行
              }else if($container.find("td").hasClass("htInvalid") == true){
                  util.xalert("录入信息错误！");
                  this.alter("remove_row", change[0][0], 1);
              }else{
                  var from_company_id = $("#from_company_id").val();
                obtain_info(change[0][3],change[0][0],this,from_company_id);
              }
          }
        }
    });
  //输入信息获取剩余信息
  function obtain_info(id, x,obj,from_company_id){  //id 查询条件， table的x列数
  	var bill_no =$("#bill_no").val();
    $.get("index.php?mod=warehouse&con=WarehouseBillInfoE&act=getGoodsInfoByGoodsID", {goods_id: id,from_company_id:from_company_id,bill_no:bill_no}, function(data){
        if(JSON.parse(data).success == 0){
          util.xalert(JSON.parse(data).error,function(){
            obj.alter("remove_row",x,1);//删除本行
          });
        }else{
            $.each(JSON.parse(data), function(i, val) {
            $container.handsontable('getData')[x][i+1] = val;
            });
            $('#warehouse_bill_e_info select[name="from_company_id"]').prop("disabled", true);
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