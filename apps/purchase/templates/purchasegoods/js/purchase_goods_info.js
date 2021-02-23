$import(function(){
	var id = '<%$view->get_id()%>';
	var pinfo_id = '<%$pinfo_id%>';
	var obj = function(){
			var initElements = function(){
				$('#purchase_goods_info select[name="product_type_id"]').select2({
					placeholder: "请选择",
					allowClear: true

				}).change(function(e){
					$(this).valid();
				});	
				
				$('#purchase_goods_info select[name="cat_type_id"]').select2({
					placeholder: "请选择",
					allowClear: true

				}).change(function(e){
					$(this).valid();
				});

			}
			var initData = function(){
				if(id){//编辑
					var product_type_id = '<%$view->get_product_type_id()%>';
					var cat_type_id = '<%$view->get_cat_type_id()%>';
					var url = "index.php?mod=purchase&con=PurchaseGoods&act=editAttr_product_cat";
					$.post(url,{product_type_id:product_type_id,cat_type_id:cat_type_id,id:id},function(data){
						$("#style_attrs").html(data.content);//把属性值的页面加载进来					
					});
				}	
			}
			var handleForm = function(){
					var url = id ? 'index.php?mod=purchase&con=PurchaseGoods&act=update' : 'index.php?mod=purchase&con=PurchaseGoods&act=insert';	
					
					var options1 = {
						url: url,
						error:function ()
						{
							alert('请求超时，请检查链接');
						},
						beforeSubmit:function(frm,jq,op){
							$('body').modalmanager('loading');//进度条和遮罩
						},
						success: function(data) {
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩
								alert(id ? "修改成功!": "添加成功!");
								util.retrieveReload();
								//$("#s_num").html(data.s_num);//修改总数量
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								alert(data.error ? data.error : (data ? data :'程序异常'));
							}
						}, 
						error:function(){
							$('.modal-scrollable').trigger('click');
							alert("数据加载失败");  
						}
					};
					
					$('#product_type_id').change(function(e){
						check_emp();
					});
					$('#cat_type_id').change(function(e){
						check_emp();
					});
					
					//款号文本框失去焦点的时候调用接口取属性值
					/*$("#purchase_goods_style_info").on('blur','#style_sn',function(){
						var url = "index.php?mod=purchase&con=PurchaseGoods&act=checkStyleSn";
						var element = $('#style_sn');
						var style_sn = $(this).val();
						$(element).closest('.form-group').removeClass('has-error');
						if(style_sn == $("#style_sn_s").val())//失去焦点的时候如果还是上次的值就不需要去调用接口
						{
							return;	
						}
						$.post(url,{style_sn:style_sn},function(data){
							if(data.success==1){
								$("#style_attrs").html(data.content);//把属性值的页面加载进来
							}
							else{
								bootbox.alert(data.error);
							}
						});
						$("#style_sn_s").val(style_sn);
					});*/
					
					$('#purchase_goods_info').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							product_type_id: {
								required: true
							},
							cat_type_id: {
								required: true
							},
							num: {
								required: true,
								digits:true
							}
						},
						messages: {
							product_type_id: {
								required: "产品线不能为空."
							},
							cat_type_id: {
								required: "款式分类不能为空"
							},
							num: {
								required: "数量不能为空.",
								digits: "数量必须为整数."
							}
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
							$("#purchase_goods_info").ajaxSubmit(options1);
						}
					});
					//回车提交
					$('#purchase_goods_info input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#purchase_goods_info').validate().form()) {
								$('#purchase_goods_info').submit();
							}
							else
							{
								return false;
							}
						}
					});
				
				}

		     ////
			return {
					init:function(){
						initElements();	
						handleForm();
						initData();
						}
			}
			////
		
	}();
	obj.init();
				 
});
//判断产品线和款式分类是不是都选了。
function check_emp()
{
	var product_type_id = $('#product_type_id').val();
	var cat_type_id = $('#cat_type_id').val();
	if(product_type_id != "" && cat_type_id != "")
	{
		var url = "index.php?mod=purchase&con=PurchaseGoods&act=getAttr_product_cat";
		$.post(url,{product_type_id:product_type_id,cat_type_id:cat_type_id},function(data){
			if(data.success==1){
				$("#style_attrs").html(data.content);//把属性值的页面加载进来
			}
			else{
				var error = data.error ? data.error : ( data ? data : '程序异常');
				bootbox.alert(error);
			}
		});
		
	}
}