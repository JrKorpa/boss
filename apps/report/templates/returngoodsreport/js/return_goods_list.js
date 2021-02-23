
//导出csv
function export_cxv_index(obj){
	var url=$(obj).attr('data-url');
	var param=new Array();
	param['start_time']=document.getElementById("start_time").value;
	param['end_time']=document.getElementById("end_time").value;
	param['order_type']=document.getElementById("order_type").value;
	param['department_id']=document.getElementById("department_id").value;
	for(index in param){
		if(index!='contains'){
			url+='&'+index+'='+param[index];
		}
	}
	window.open(url);
	return false;
}
//分页
function return_goods_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'], function(){
	util.setItem('orl','index.php?mod=report&con=ReturnGoodsReport&act=search');//设定刷新的初始url
	util.setItem('formID','return_goods_search_form');//设定搜索表单id
	util.setItem('listDIV','return_goods_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		var initElements = function(){
			 $('#return_goods_search_form select').select2({
                 placeholder: "全部",
                 allowClear: true,
             }).change(function(e) {
                 $(this).valid();
             });//validator与select2冲突的解决方案是加change事件
			 $('#return_goods_search_form :reset').on('click',function(){
					$('#return_goods_search_form select').select2("val","");
				});
		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			return_goods_search_page(util.getItem("orl"));
			
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