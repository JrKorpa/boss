//分页
function warehouse_goods_search_page(url){
	util.page(url);
}
function validateInput(){
   var goods_id = $("#goods_id").val();
   var bill_no  = $("#bill_no").val();
   if (goods_id != '') {
       $("#bill_no").attr("disabled",true);
   }else{
      $("#bill_no").attr("disabled",false); 
   }
   if (bill_no != '') {
       $("#goods_id").attr("disabled",true);
   }else{
       $("#goods_id").attr("disabled",false)
   }
   
}

//打印条码
function printcode(){
    var jiajialv = $("#jiajialv").val();
    var jiajianum = $("#jiajianum").val();
	/*
    var type = document.getElementById('type');
    if(type.checked){
        type = 1;
    }else{
        type = 0;
    }
   */
    var down_info = 'down_info';
    var bill_no = $("#bill_no").val();
    var goods_id = $("#goods_id").val();
	var type = $("#warehouse_goods_search_form_s input[name='type']:checked").val();
    var type_t = $("#warehouse_goods_search_form_s input[name='type_t']:checked").val();
    var label_price = $("#warehouse_goods_search_form_s input[name='label_price']:checked").val();
    var daying_type = $("#daying_type").val();
    if(goods_id.length){
    	var temp = goods_id.split("\n");
    	goods_id = temp.join(',');
    }
    var args = "&down_info="+down_info+"&goods_id="+goods_id+"&bill_no="+bill_no+"&jiajialv="+jiajialv+"&jiajianum="+jiajianum+"&type="+type+"&daying_type="+daying_type+"&type_t="+type_t+"&label_price="+label_price;
   
    //add form validate
    
    if (bill_no == '' && goods_id == '') {
        util.xalert("单据号和货号至少有一个不为空");
        return false;
    }
    if(jiajialv == '' || jiajianum == ''){
            util.xalert("加价率和加价系数信息不能为空");
            return false;
    }
    var num =jiajialv.replace(/(\d*\.?)/,"").length;
    if(!/^\d+(\.\d+)?$/.test(jiajialv) || num >2) {
            util.xalert("加价率只能输入数字小数且小数点后保留2位！");
            $("#jiajialv").focus();
            return false;
    }
    if(!/^[+-]?\d*\.?\d{0,2}$/.test(jiajianum)) {
            util.xalert("系数只能输入正负数字小数且小数点后保留2位！");
            $("#jiajianum").focus();
            return false;
    }
    
  
    location.href = "index.php?mod=warehouse&con=WarehouseHuodongPrintCode&act=printCode"+args;
  
  
    

}

//打印条码
function printbzhcode(){
   
    var down_info = 'down_info_bzh';
    var goods_id = $("#goods_id_bzh").val();
    if(goods_id.length){
        var temp = goods_id.split("\n");
        goods_id = temp.join(',');
    }
    if (goods_id == '') {
        util.xalert("货号不能为空！");
        return false;
    }
    var args = "&down_info="+down_info+"&goods_id="+goods_id;
    location.href = "index.php?mod=warehouse&con=WarehouseHuodongPrintCode&act=printBzhCode"+args;
}

//匿名回调
$import("public/js/select2/select2.min.js", function(){
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseHuodongPrintCode&act=#');//设定刷新的初始url
	util.setItem('formID','warehouse_goods_search_form_s');//设定搜索表单id
	util.setItem('listDIV','warehouse_goods_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
                $('#warehouse_goods_search_form_s select').select2({
                    placeholder: "请选择",
                    allowClear: true
                }).change(function(e){
                    $(this).valid();
                });
            };
		
		var handleForm = function(){
//                    $('#warehouse_goods_search_form_s button').click(function(){
//                        
//                        var jiajialv = $('#warehouse_goods_search_form_s input[name="jiajialv"]').val();
//                        var jiajianum = $('warehouse_goods_search_form_s input[name="jiajianum"]').val();
//                        if (jiajialv == '' || jiajianum =='') {
//                            $('warehouse_goods_search_form_s').empty().append("<div class='alert alert-info'>请填写加价率和加价系数信息！</div>");
//                        }
//                        
//                    });
                    util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			//warehouse_goods_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});