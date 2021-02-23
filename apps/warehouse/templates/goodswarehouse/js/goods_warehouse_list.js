//分页
function goods_warehouse_search_page(url){
	util.page(url);
}
//匿名回调
$import(["public/js/select2/select2.min.js"], function(){
	util.setItem('orl','index.php?mod=warehouse&con=GoodsWarehouse&act=search&wh_id='+wh_id);//设定刷新的初始url
	util.setItem('formID','goods_warehouse_search_form');//设定搜索表单id
	util.setItem('listDIV','goods_warehouse_search_list');//设定列表数据容器id
	var warehouse_warehouse_id = '<%$view->get_warehouse_id()%>';

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
                        $('#goods_warehouse_search_form select[name="company_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#goods_warehouse_search_form select[name="warehouse_id[]"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			$('#goods_warehouse_search_form select[name="status"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
                        $('#goods_warehouse_search_form select[name="company_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e){
                                
  				var _t = $("#goods_warehouse_search_form select[name='company_id']").val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=WarehouseGoods&act=getTowarehouseId', {'id': _t}, function (data) {
                                                $('#goods_warehouse_search_form select[name="warehouse_id[]"]').attr('disabled', false).empty().append(data);
						$('#goods_warehouse_search_form select[name="warehouse_id[]"]').change();
                                                $("#goods_warehouse_search_form input[name='checkAll']").removeAttr("checked");
					});
				}else{
					$('#goods_warehouse_search_form select[name="warehouse_id[]"]').attr('disabled', 'disabled').empty().append('<option value=""></option>').select2('val','');
                                        
                                        $("#goods_warehouse_search_form input[name='checkAll']").removeAttr("checked");
                                       
				}
			});
                        
                        $('#goods_warehouse_search_form input[name=checkAll]').click(function(){
                                var status=$(this).attr('checked');
                                var arr=new Array();
                                if(status=='checked'||status==true)
                                {

                                        $('#goods_warehouse_search_form select[name="warehouse_id[]"] option').each(function(key,v){
                                                arr[key]=$(this).val();
                                        })
                                        $('#goods_warehouse_search_form select[name="warehouse_id[]"]').select2("val",arr);	
                                }
                                $('#goods_warehouse_search_form select[name="warehouse_id[]"]').select2("val",arr);	
					
			});

			//checkbox 默认选中
			// $('#close').attr('checked', true);

		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			goods_warehouse_search_page(util.getItem("orl"));
			//下拉重置
			$('#goods_warehouse_search_form :reset').on('click',function(){
				$('#goods_warehouse_search_form select[name="status"]').select2("val",'');
				$('#goods_warehouse_search_form select[name="warehouse_id[]"]').select2("val",'');
                                $('#goods_warehouse_search_form select[name="company_id"]').select2("val",'');
			})
                       
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