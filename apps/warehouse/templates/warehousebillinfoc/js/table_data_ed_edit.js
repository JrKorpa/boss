// 表格初始化
var from_table_data_c_edit = function (id, tdata, ttitle, ttype , bill_id) {
  var $container = $(id);
  //表格基本内容初始化
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
           if (source != "loadData" && change[0][1] == 0 && change[0][2] != change[0][3] && change[0][3] != 0) { //跳过第一次/且第一列执行
                //重复的数组
                var newarry = repeat(),megrep = '',obj = this;
                //获取最新填入的信息数组
                var changarry = [],batch_x = change[0][0],changelength = change.length;
                for (var i = 0; i < changelength; i++) {
                    changarry.push(change[i][3]);
                }
                //没有重复信息
                if (newarry.length < 1) {
                    getBatchInfo(changarry,batch_x,changelength,this);
                } else { //有重复信息
                    //将重复信息添加到megrep中,方便提示；
                    for(i=0; i < newarry.length; i++){
                        megrep += newarry[i][1] + ",";
                    }
                    //判断是否有重复，有，进行提示
                    if (megrep !== '') {
                        util.xalert('货号重复 <br/><span style="color:red;padding:5px 0;">' + megrep + '</span><br/>',function(){
                            for (var i = 0 ; i < changelength ; i++) {
                                //将表格中本次添加的信息删除掉
                                obj.alter("remove_row", batch_x, 1);
                            }
                        });
                    }
                }
            } else {
                return false;
            }
        }
    });

 // 批量获取添加输入值
    function getBatchInfo(idArry,x,n,obj){  //id 查询条件， table的x行数
        //如果用户没有输入
        if(idArry.length < 1 ){return false;}
        var bill_id = '<%$view->get_id()%>';
        $.get("index.php?mod=warehouse&con=WarehouseBillInfoC&act=getGoodsInfos", {'g_ids':idArry,'bill_id':bill_id}, function(data){
            //alert(data);return false;
            var data = JSON.parse(data),text = '';
            if (data.error != '') {
                data.error.unique = '';
                for(i in data.error){
                    text += data.error[i]+'<br/>';
                }
                util.xalert('批量添加,以下货号错误：<br/><br/><span class="text-danger">'+text+'</span>',function(){
                    for(i=0;i<n;i++){
                        obj.alter("remove_row",x,1);
                    }
                });
            }else{
                var sudata = data.success;
                for (var i = 0; i < sudata.length; i++) {
                    $.each(sudata[i], function(e, val) {
                        $container.handsontable('getData')[x+i][e + 1] = val;
                    });
                };
            }
        });
    }
  //输入信息获取剩余信息
  function obtain_info(id, x,obj,from_company_id){  //id 查询条件， table的x列数
    $.get("index.php?mod=warehouse&con=WarehouseBillInfoC&act=getGoodsInfoByGoodsID", {goods_id: id , from_company_id:from_company_id}, function(data){
        if(JSON.parse(data).success == 0){
            util.xalert(JSON.parse(data).error,function(){
            obj.alter("remove_row",x,1);//删除本行
            });
        }else{
            $.each(JSON.parse(data), function(i, val) {
            $container.handsontable('getData')[x][i+1] = val;
            });
            $('#warehouse_bill_c_info select[name="from_company_id_c"]').prop("disabled", true);
        }
    });
  }

 //遍历判断重复
    function repeat() {
        //获取当前表格中第一列的信息加入到数组中
        var arr = $container.handsontable('getData'),
            newarr = [];
        for (var i = 0; i < arr.length; i++) {
            newarr.push(arr[i][0]);
        };
        return newarr.unique();
    }
    //判断数组中是否有重复并返回被重复数组
    Array.prototype.unique = function() {
        var res = [],
            hash = {},
            repeatarry = [],
            rArray = [];
        for (var i = 0, elem;
             (elem = this[i]) != null; i++) {
            if (!hash[elem]) {
                res.push(elem);
                hash[elem] = true;
            } else {
                repeatarry.push([i, elem]);
            }
        }
        return repeatarry;
    }
}