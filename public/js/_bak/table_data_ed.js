// 表格初始化
var from_table_data = function (id, tdata, ttitle, ttype) {
  var getData = (function () {
    var data = tdata;
    return function () {
      var page  = parseInt(window.location.hash.replace('#', ''), 5) || 1
        , limit = 5
        , row   = (page - 1) * limit
        , count = page * limit
        , part  = [];
      for(;row < count;row++) {
        part.push(data[row]);
      }
      return part;
    }
  })();
  var $container = $(id);
  //var autosaveNotification;
  //表格基本内容初始化
  $container.handsontable({
    data: getData(),//加载初始数据
    startRows:true,//初始化表格行数,可以具体数字
    colHeaders:ttitle,//是否显示序号,标题
    //rowHeaders:true,
    columns: ttype,//设定类型
    columnSorting: true,//是否排序
    //minSpareRows: 1,//预留新行
    contextMenu: true//初始化菜单
   // manualColumnResize: false,//列宽是否能拖动
    // afterChange: function (change, source) {
    //   if (source === 'loadData') {
    //     return; //don't save this change
    //   }
    //   console.log(change[0][1]);
    //   if (change[0][1] = 0) {
    //     clearTimeout(autosaveNotification);
    //     var d = change[0][3];
        
    //     newsave(d,change[0][0]);
    //   }
    // }
  });
  var pp = tdata.length;
  if (pp > 5) {
    var text1 = '<ul class="pagination pull-right">';
    for (var i = 1; i <= Math.ceil(pp/5); i++) {
      text1 +='<li><a href="#'+i+'">'+i+'</a></li>';
    };
    text1 +='<li class="disabled"><a href="#">'+pp+'条</a></li></ul>';
    //console.log($container.next('div.pager').html());
    $container.next('div.pager').append(text1);
  };
  $(window).on('hashchange',  function (event) {
    $container.handsontable('loadData', getData());
  });
  // var newsave = function (x,y) {
  //   var arr = table_save();
  //   var x = arr.length;
  //   var $newarr = [];
  //   for (var i = 1; i <= x; i++) {
  //   var b = arr[i-1][0];
  //     $newarr.push(b);
  //   }
  //   $newarr.splice(y,1);
  //   if ($.inArray('x',$newarr)>=0) {
  //     alert(23);
  //   }
  // }
}
var table_save = function () {
  return $("#from_table_data").handsontable('getData');
}
