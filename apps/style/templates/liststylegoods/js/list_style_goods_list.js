//分页
function list_style_goods_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js",'public/js/jquery.validate.extends.js'],function(){
	util.setItem('orl','index.php?mod=style&con=ListStyleGoods&act=search');//设定刷新的初始url
	util.setItem('formID','list_style_goods_search_form');//设定搜索表单id
	util.setItem('listDIV','list_style_goods_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			
			//初始化下拉组件
			$('#list_style_goods_search_form select').select2({
				placeholder: "全部",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件	

            $('#list_style_goods_search_form button[type="reset"]').on('click',function(){
                $('#list_style_goods_search_form select[name="status"]').select2('val','').change();
                $('#list_style_goods_search_form select[name="caizhi"]').select2('val','').change();
                $('#list_style_goods_search_form select[name="yanse"]').select2('val','').change();
            });
		};
		
		//表单验证和提交
		var handleForm = function(){
			var options1 = {
				url:util.getItem('orl'),
				target:'#'+util.getItem("listDIV"),
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert({   
						message: "请求超时，请检查链接",
						buttons: {  
								   ok: {  
										label: '确定'  
									}  
								},
						animate: true, 
						closeButton: false,
						title: "提示信息" 
					});
					return false;
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
					//util.closeForm(util.getItem("formID"));
				}
			};

			$('#'+util.getItem('formID')).validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					xiangkou1:{
						//remote:'index.php?mod=style&con=ListStyleGoods&act=check'
						isFloat:true
					},
					xiangkou2:{
						//remote:'index.php?mod=style&con=ListStyleGoods&act=check'
						isFloat:true
					},
					finger1:{
						//remote:'index.php?mod=style&con=ListStyleGoods&act=check'
						isFloat:true
					},
					finger2:{
						//remote:'index.php?mod=style&con=ListStyleGoods&act=check'
						isFloat:true
					}
				},
				messages: {
					//xiangkou1:{
						//remote:'123'	
					//}
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
					$("#"+util.getItem('formID')).ajaxSubmit(options1);
				}
			});
			//回车提交
			$('#'+util.getItem('formID')+' input').keypress(function (e) {
				if (e.which == 13) {
					$('#'+util.getItem('formID')).validate().form()
				}
			});
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			list_style_goods_search_page(util.getItem("orl"));
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

function downloads() {
	var formdata = $("#list_style_goods_search_form").serialize();
    location.href = "index.php?mod=style&con=ListStyleGoods&act=downloads&"+formdata;
}