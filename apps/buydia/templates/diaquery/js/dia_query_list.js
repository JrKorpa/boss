//分页
function dia_query_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js'],function(){
	util.setItem('orl','index.php?mod=buydia&con=DiaQuery&act=search');//设定刷新的初始url
	util.setItem('formID','dia_query_search_form');//设定搜索表单id
	util.setItem('listDIV','dia_query_search_list');//设定列表数据容器id
    var search_form_id = 'dia_query_search_form';

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉美化 需要引入"public/js/select2/select2.min.js"
          $('#'+search_form_id+' select').select2({
              placeholder: "请选择",
              allowClear: true,
          }).change(function(e){
              $(this).valid();
          }); 
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			dia_query_search_page(util.getItem("orl"));
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

//导出
function download(){
    var down_info = 'down_info';
    var dia_package = $("#dia_query_search_form [name='dia_package']").val();
    var status = $("#dia_query_search_form [name='status']").val();
    var processors_id = $("#dia_query_search_form [name='processors_id']").val();
    var args = "&down_info="+down_info+"&dia_package="+dia_package+"&status="+status+"&processors_id="+processors_id;
    location.href = "index.php?mod=buydia&con=DiaQuery&act=search"+args;
}