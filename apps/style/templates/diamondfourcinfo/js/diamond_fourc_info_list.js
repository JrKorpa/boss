//分页
function diamond_fourc_info_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js'],function(){
	util.setItem('orl','index.php?mod=style&con=DiamondFourcInfo&act=search');//设定刷新的初始url
	util.setItem('formID','diamond_fourc_info_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_fourc_info_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            $('#diamond_fourc_info_search_form select').select2({
                placeholder: "请选择",
                allowClear: true,
            }).change(function(e){
                $(this).valid();
            });
			$('#diamond_fourc_info_search_form button[type="reset"]').on('click',function(){
               $('#diamond_fourc_info_search_form select').select2('val','').change();
            });
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			diamond_fourc_info_search_page(util.getItem("orl"));
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