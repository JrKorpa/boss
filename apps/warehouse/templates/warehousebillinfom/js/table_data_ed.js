// 表格初始化
var from_table_data_bill_m = function(id, tdata, title, tcolumns) {
    var $container = $(id),
        obtain_error = [];
    //表格基本内容初始化
    $container.handsontable({
        data: tdata,
        rowHeaders:true,//是否显示序号
        startRows: 1, //初始化默认行数
        colHeaders: title, //是否显示表头
        columns: tcolumns,
        columnSorting: true, //是否排序
        minSpareRows: 1, //预留新行
        contextMenu: true, //初始化右键菜单
        afterChange: function(change, source) { 
		   
		    //改变内容之后执行的方法
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
                                			//统计
                                				var data = $container.handsontable('getData');
								                var goods_num =data.length-1;
								                var price_all = 0;
                                                var label_price_total = 0;
												for (i=0;i<data.length-1;i++ ){
													if(data[i][2]!='null'){
														price_all =parseFloat(price_all)+parseFloat(data[i][2]);
													}
                                                    if(data[i][17]!='null'){
                                                        label_price_total =parseFloat(label_price_total)+parseFloat(data[i][17]);
                                                    }
													  
												}
								                //数量总计	
								                $("#goods_num").val(goods_num);
								          		//价格总计
                                                price_all = parseFloat(price_all).toFixed(2);
								                $("#mingyijia").val(price_all);
                                                //展厅标签价格总计
                                                label_price_total = parseFloat(label_price_total).toFixed(2);
                                                $("#label_price_total").val(label_price_total);
												
											//显示总数和总价 
											 var str='货品数量：<font id="nums">'+goods_num+'</font>，价格总计：<font id="prices">'+price_all+'</font>';
											 $("#Statistics").show().html(str);
                            }
                        });
                    }
                }
            } else {
                return false;
            }
        },
        beforeRemoveRow: function(index,amount) {
        	     var row=this.getDataAtRow(index); 
				 if(typeof(row[2])=='undefined'){
					 var price_all=$("#mingyijia").val();
					 var goods_num=$("#goods_num").val();
				 }else{
					//计算删除后的数量总计	
		           var goods_num_old = $("#goods_num").val();
		           var amount;
		           var goods_num=parseFloat(goods_num_old)-parseFloat(amount);
		           if(goods_num_old > amount){
                   	   $("#goods_num").val(goods_num);
					   $("#nums").val(goods_num);
                   }
      			   //计算删除后的价格总计
      			   var mingyijia=$("#mingyijia").val(); 
                   var label_price = $("#label_price_total").val(); 
      			                 	
                   var price_all = parseFloat(mingyijia)- parseFloat(row[2]);
                   var label_price_total = parseFloat(label_price)- parseFloat(row[17]);
				   price_all=price_all.toFixed(2);
                   label_price_total=label_price_total.toFixed(2);
                  // alert(price_all);
                   if(!isNaN(price_all)){
                   	$("#mingyijia").val(price_all);
					$("#prices").val(price_all);
                   } 
                   if(!isNaN(label_price_total)){
                    $("#label_price_total").val(label_price_total);
                   } 
				 }
			
        			
				   
				  //显示总数和总价 
                 var str='货品数量：<font id="nums">'+goods_num+'</font>，价格总计：<font id="prices">'+price_all+'</font>';
		         $("#Statistics").show().html(str);
                    
        }
    });
    // 批量获取添加输入值
    function getBatchInfo(idArry,x,n,obj){  //id 查询条件， table的x行数
        //如果用户没有输入
        if(idArry.length < 1 ){return false;}
        var bill_id = '<%$view->get_id()%>';
        var from_company_id = $('#warehouse_bill_info_m_info select[name="from_company_id"]').val();
        var to_company_id = $('#warehouse_bill_info_m_info select[name="to_company_id"]').val();

        $.get("index.php?mod=warehouse&con=WarehouseBillInfoM&act=getGoodsInfos", {'g_ids':idArry,'bill_id':bill_id, 'from_company_id':from_company_id, 'to_company_id':to_company_id}, function(data){
            //alert(data);return false;
            var data = JSON.parse(data),text = '';
            if (data.error != '') {
                data.error.unique = '';
                for(i in data.error){
					if(typeof data.error[i]!='function')
					{
						text += data.error[i]+'<br/>';
					}
                }
                util.xalert('批量添加,以下货号错误：<br/><br/><span class="text-danger">'+text+'</span>',function(){
                    for(i=0;i<n;i++){
						
                        obj.alter("remove_row",x,1);
                    }
                });
            }else{
                var sudata = data.success;
                //var datas = $("#from_table_data").handsontable('getData');                                     
                for (var i = 0; i < sudata.length; i++) {
                    $.each(sudata[i], function(e, val) {
                        $container.handsontable('getData')[x+i][e + 1] = val;
                    });
                };

                //获取tab所有数据 二位数组 最后一行为空 去掉
                var data = $container.handsontable('getData');
                //debugger;
                var goods_num =data.length-1;
                var price_all = 0;
                var label_price_total = 0;
				for (i=0;i<data.length-1;i++ ){
					var price_all1=price_all;
					if(data[i][2]!='null'){
						price_all =parseFloat(price_all)+parseFloat(data[i][2]);
					}					  
                    if(data[i][17]!='null'){
                        label_price_total =parseFloat(label_price_total)+parseFloat(data[i][17]);
                    }
				}
				 price_all=price_all.toFixed(2);
                 label_price_total=label_price_total.toFixed(2);
				//return false;
                //获取数量总计	
                $("#goods_num").val(goods_num);		                  
                //获取价格总计
                $("#mingyijia").val(price_all);
                //获取展厅标签价总计
                $("#label_price_total").val(label_price_total);
				if(price_all > 400000 && price_all1 <= 400000 ){
						util.xalert('此调拨单商品总金额已超过：<font color="red">'+40+'</font>万',function(){});
				  }else if(price_all > 250000 && price_all1 <= 250000 ){
						util.xalert('此调拨单商品总金额已超过：<font color="red">'+25+'</font>万',function(){});
					}else if(price_all > 150000 && price_all1 <= 150000){
						util.xalert('此调拨单商品总金额已超过：<font color="red">'+15+'</font>万',function(){});
				  }
				
				 //显示总数和总价 
                 var str='货品数量：<font id="nums">'+goods_num+'</font>，价格总计：<font id="prices">'+price_all+'</font>';
		         $("#Statistics").show().html(str);

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
    ////输入信息获取剩余信息
    //function obtain_info(id, x, obj) { //id 查询条件， table的x行数， obj当前表格本身
    //    //如果用户没有输入
    //    if (id == '') {
    //        return false;
    //    }
    //    $.get("index.php?mod=warehouse&con=WarehouseBillInfoM&act=getGoodsInfoByGoodsID", {goods_id: id}, function(data) {
    //        console.log(x + "" + id + " - " );
    //        if (JSON.parse(data).success == 0) {
    //            console.log(JSON.parse(data).error);
    //            return JSON.parse(data).error;
    //        } else {
    //            //循环往列里写入数据
    //            console.log(data);
    //            $.each(JSON.parse(data), function(i, val) {
    //                $container.handsontable('getData')[x][i + 1] = val;
    //            });
    //            return '';
    //        }
    //    });
    //
    //}
   


}
