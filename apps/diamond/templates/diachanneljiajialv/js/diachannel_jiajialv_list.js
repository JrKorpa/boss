//分页
function diachannel_jiajialv_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js', 'public/js/jquery.validate.extends.js'], function() {
	util.setItem('orl','index.php?mod=diamond&con=DiaChannelJiajialv&act=search');//设定刷新的初始url
	util.setItem('formID','diachannel_jiajialv_search_form');//设定搜索表单id
	util.setItem('listDIV','diachannel_jiajialv_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){

		var initElements = function(){
			

			$('#diachannel_jiajialv_search_form select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });

            $('#diachannel_jiajialv_search_form :reset').on('click',function(){
                $('#diachannel_jiajialv_search_form select[name="good_type"]').select2('val','');
                $('#diachannel_jiajialv_search_form select[name="channel"]').select2('val','');
                $('#diachannel_jiajialv_search_form select[name="status"]').select2('val','');
            })

		};

		var handleForm = function(){
			util.search();
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			diachannel_jiajialv_search_page(util.getItem("orl"));
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