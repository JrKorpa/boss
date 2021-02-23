//分页
function app_style_baoxianfee_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=style&con=AppStyleBaoxianfee&act=search');//设定刷新的初始url
	util.setItem('formID','app_style_baoxianfee_search_form');//设定搜索表单id
	util.setItem('listDIV','app_style_baoxianfee_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var Obj = function(){

		var initElements = function(){

			//初始化下拉组件
			$('#app_style_baoxianfee_search_form select').select2({
				placeholder: "全部",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
		};
		var handleForm = function(){
			$('#app_style_baoxianfee_search_form').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					min: {
						number:true
					},
					max: {
						number:true
					},
					price_min: {
						number:true
					},
					price_max: {
						number:true
					},
				},
				messages: {
					min: {
						number: "必须输入合法的数字."
					},
					max: {
						number: "必须输入合法的数字."
					},
					price_min: {
						number: "必须输入合法的数字."
					},
					price_max: {
						number: "必须输入合法的数字."
					},
				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},
			});
			util.search();
		};
		var initData = function(){
			//下拉组件重置
			$('#app_style_baoxianfee_search_form :reset').on('click',function(){
				$('#app_style_baoxianfee_search_form select').select2("val",'');
			})
			util.closeForm(util.getItem("formID"));
			app_style_baoxianfee_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}
	}();

	Obj.init();
});