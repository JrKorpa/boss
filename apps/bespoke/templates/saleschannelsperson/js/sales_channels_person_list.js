//分页
function sales_channels_person_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=bespoke&con=SalesChannelsPerson&act=search');//设定刷新的初始url
	util.setItem('formID','sales_channels_person_search_form');//设定搜索表单id
	util.setItem('listDIV','sales_channels_person_search_list');//设定列表数据容器id
    
    var id = '<%$def_id%>';

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            $('#sales_channels_person_search_form select[name="order_department"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
            $('#sales_channels_person_search_form :reset').on('click',function(){
                $('#sales_channels_person_search_form select[name="order_department"]').select2('val',id);
            })
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			//util.closeForm(util.getItem("formID"));
            $('#'+util.getItem("formID")).submit();
			//sales_channels_person_search_page(util.getItem("orl"));
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