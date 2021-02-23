// 表格初始化
var from_table_data_stone_p = function (id,tdata,title,tcolumns) {
    var $container = $(id);
    //自定义验证
    function negativeValueRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        //if (col === 0 || col===1) {
        if (value < 0) { //if row contains negative number
            td.className = 'htInvalid'; //add class "negative"
        }
        //}
    }
    Handsontable.renderers.registerRenderer('negativeValueRenderer', negativeValueRenderer);
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
        cells: function (row, col, prop) {
            var cellProperties = {};
            cellProperties.renderer = "negativeValueRenderer";
            return cellProperties;
        }
    });
}