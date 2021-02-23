//分页
function dia_bill_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js','public/js/bootstrap-datepicker/js/bootstrap-datepicker.js','public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'],function(){
	util.setItem('orl','index.php?mod=buydia&con=DiaBillSearch&act=search');//设定刷新的初始url
	util.setItem('formID','dia_bill_search_form');//设定搜索表单id
	util.setItem('listDIV','dia_bill_search_list');//设定列表数据容器id
    info_form_id_search = 'dia_bill_search_form';

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
          if ($.datepicker) {
              $('.date-picker').datepicker({
                  format: 'yyyy-mm-dd',
                  rtl: App.isRTL(),
                  autoclose: true
              });
              $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
          }
          //下拉美化 需要引入"public/js/select2/select2.min.js"
          $('#'+info_form_id_search+' select').select2({
              placeholder: "请选择",
              allowClear: true,
//                minimumInputLength: 2
          }).change(function(e){
              $(this).valid();
          }); 
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			dia_bill_search_page(util.getItem("orl"));
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