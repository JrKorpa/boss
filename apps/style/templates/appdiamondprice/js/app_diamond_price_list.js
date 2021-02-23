//分页
function app_diamond_price_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=style&con=AppDiamondPrice&act=search');//设定刷新的初始url
	util.setItem('formID','app_diamond_price_search_form');//设定搜索表单id
	util.setItem('listDIV','app_diamond_price_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var Obj = function(){

		var initElements = function(){

			//初始化下拉组件
			$('#app_diamond_price_search_form select[name="guige"]').select2({
				placeholder: "全部",
				allowClear: true,

			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			//初始化下拉组件
			$('#app_diamond_price_search_form select[name="guige_status"]').select2({
				placeholder: "全部",
				allowClear: true,

			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			$('#app_diamond_price_search_form :reset').on('click',function(){
				$('#app_diamond_price_search_form select[name="guige"]').select2("val","");
			})
		};

		var handleForm = function(){
			var options1 = {
				url:util.getItem("orl"),
				target:'#'+util.getItem("listDIV"),
				error:function ()
				{
					alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');
					var jsondata = {};
					var _url = '';
					$(frm).each(function(i,e){
						jsondata[e.name] = e.value;
						_url+="&"+e.name+"="+e.value;
					});
					util.setItem("data",JSON.stringify(jsondata));
					util.setItem("url",util.getItem("orl")+_url);
				},
				success: function(data) {
					$('.modal-scrollable').trigger('click');
					util.closeForm(util.getItem("formID"));
				},
				error:function(){
					alert("数据加载失败");
				}
			};

			$("#"+util.getItem("formID")).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					guige_a: {
						number:true
					},
					guige_b: {
						number:true
					},
					price: {
						number:true
					},
				},
				messages: {
					guige_a: {
						number: "必须输入合法的数字."
					},
					guige_b: {
						number: "必须输入合法的数字."
					},
					price: {
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

				submitHandler: function (form) {
					$("#"+util.getItem("formID")).ajaxSubmit(options1);
				}
			});
		};

		var initData = function(){
			util.closeForm(util.getItem("formID"));
			app_diamond_price_search_page(util.getItem("orl"));
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