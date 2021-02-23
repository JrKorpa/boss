//打印商品条码
//自动根据一口价和销售政策的加价率来算的
function printgoodscode(){
    var down_info = 'down_info_goods';
    var goods_id = $("#goods_id_pt").val();
	var departmentid = $("#department_id").val();
    if(goods_id.length){
        var temp = goods_id.split("\n");
        goods_id = temp.join(',');
    }
    if (goods_id == '') {
        util.xalert("货号不能为空！");
        return false;
    }
    var args = "&departmentid="+departmentid+"&down_info="+down_info+"&goods_id="+goods_id;
    //location.href = "index.php?mod=warehouse&con=WarehouseGoodsPrintCode&act=printCode"+args;
	window.open("index.php?mod=warehouse&con=WarehouseGoodsPrintCode&act=printCode"+args  );
}

//分页
function warehouse_goods_search_page(url){
	util.page(url);
}


//打印条码
function printcode(){
	
	//销售渠道名称
	var departmentname = $("#department_id").select2('data').text;
	//销售渠道id
	var departmentid = $("#department_id").select2('val');
	
	var is_activity = $("#printcode_search_form input[name='is_activity']:checked").val();
	var policy_id = $("#policy_id").select2('val');
	if(is_activity == 1 && !policy_id){
		util.xalert("勾选活动后，必须选择销售政策");
        return false;
	}
	
	//自定义的加价率
    var jiajialv = $("#jiajialv").val();
	//自定义的累加系数
    var jiajianum = $("#jiajianum").val();
	//打印标签文件的类型(1:csv,2:xls)
	var daying_type = $("#daying_type").val();
	//打标类型(根据什么来打标 0:自定义打标 2:根据销售政策打标 3:指定价打标)
	var type_t = $("#printcode_search_form input[name='type_t']:checked").val();
	//console.log(type_t);
	//是否有活动价
	var isactive = $("#printcode_search_form input[name='nine_price']:checked").val();
    var label_price = $("#printcode_search_form input[name='label_price']:checked").val();
	if(isactive > 0 )
	{
		isactive = 1;
	}else{
		isactive = 0;	
	}
    var down_info = 'down_info';
    var goods_id = $("#goods_id").val();
	//验证参数
	if(!goods_id)
	{
		util.xalert("货号不能为空");
        return false;
	}
	//
	if(type_t < 1)
	{
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
	}
	
    if(goods_id.length){
    	var temp = goods_id.split("\n");
    	goods_id = temp.join(',');
    }
	var args = "&down_info="+down_info+"&departmentid="+departmentid+"&goods_id="+goods_id+"&jiajialv="+jiajialv+"&jiajianum="+jiajianum+"&isactive="+isactive+"&daying_type="+daying_type+"&type_t="+type_t+"&policy_id="+policy_id+"&label_price="+label_price;
    location.href = "index.php?mod=warehouse&con=WarehouseGoodsPrintCode&act=printCode"+args;
}



//匿名回调
$import("public/js/select2/select2.min.js", function(){
	
	//设定刷新的初始url
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseGoodsPrintCode&act=index#');
	util.setItem('formID','printcode_search_form');//设定搜索表单id
	util.setItem('listDIV','channel_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
                $('#printcode_search_form select').select2({
                    placeholder: "请选择",
                    allowClear: true
                }).change(function(e){
                    $(this).valid();
                });
				
				$('#printcode_search_form input[name="is_activity"]').click(function(e){
                    $(this).valid();
					var is_activity = $("#printcode_search_form input[name='is_activity']:checked").val();
					if(is_activity == 1){
						$("#policy_id_div").show();
					}else{
						$('#printcode_search_form select[name="policy_id"]').select2('val','');
						$("#policy_id_div").hide();
					}
                });
				
			$('#printcode_search_form select[name="department_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
  				$(this).valid();
				var _t = $(this).val();
			
				if (_t) {
					$.post('index.php?mod=salepolicy&con=AppSalepolicyChannel&act=getBaseSalepolicyOption', {'change_id': _t}, function (data) {
						$('#printcode_search_form select[name="policy_id"]').attr('disabled', false).empty().append('<option value="">请选择</option>').append(data).select2('val','');
						$('#printcode_search_form select[name="policy_id"]').change();
					});
				}else{
					$('#printcode_search_form select[name="policy_id"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
				}
			});
            };
		var handleForm = function(){
			util.search();
		};
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			warehouse_goods_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}
	}();
	obj.init();
});