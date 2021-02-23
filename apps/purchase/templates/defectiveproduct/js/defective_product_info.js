function checkDfpro(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定审核此不良品返厂单?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}

function cancelPro(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var tab_id = $(o).attr('list-id');
	
	bootbox.confirm("确定取消此不良品返厂单?（取消后不可恢复，只能重新制单，请慎重）", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.post(url,{id:id},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
						util.syncTab(tab_id);
					}
					else{
						bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
					}
				});
			}, 0);
		}
	});
}

function printDetail(o)
{
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	window.open(url+'&id='+id);
}

$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
	"public/css/jquery.handsontable.full.css",
	"public/js/jquery.handsontable.full.js",
	"public/js/table_data_ed.js",],function(){
	var id = '<%$view->get_id()%>';
	var obj = function(){
			var initElements = function(){}
			var initData = function(){}
			var handleForm = function(){
					var url = id ? 'index.php?mod=purchase&con=PurchaseType&act=update' : 'index.php?mod=purchase&con=PurchaseType&act=insert';
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
								bootbox.alert(id ? "修改成功!": "添加成功!");
								if (id)
								{//刷新当前页
									util.page(util.getItem('url'));
								}
								else
								{//刷新首页
									purchase_type_search_page(util.getItem("orl"));
									util.page('index.php?mod=purchase&con=PurchaseType&act=search');
								}
							}else{
								$('body').modalmanager('removeLoading');//关闭进度条
								bootbox.alert(data.error ? data.error : (data ? data :'程序异常'));
							}
						}, 
						error:function(){
							$('.modal-scrollable').trigger('click');
							bootbox.alert("数据加载失败");  
						}
					};
		
					$('#purchase_type_info').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							t_name: {
								required: true
							}
						},
						messages: {
							t_name: {
								required: "采购分类名称不能为空."
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
							$("#purchase_type_info").ajaxSubmit(options1);
						}
					});
					//回车提交
					$('#purchase_type_info input').keypress(function (e) {
						if (e.which == 13) {
							if ($('#purchase_type_info').validate().form()) {
								$('#purchase_type_info').submit();
							}
							else
							{
								return false;
							}
						}
					});
				
				}
	/*	var from_table = function(){
			$.ajax({
				//url:"public/json/load.json",
				url:"index.php?mod=purchase&con=DefectiveProduct&act=mkJson",
				dataType:"json",
				type:"POST",
				data:{'id':id},
				success:function(res) {
					from_table_data(res.id,res.data,res.title,res.columns);
				}
			});
			//保存值
			$("body").find("#from_table_data_btn").click(function(){
				if ($("#defec_pro_from_table_data").find("td").hasClass("htInvalid") == true) {
					$("#defec_pro_from_table_data").prev("p").addClass('text-danger').text("表单有错误信息，请更正再保存！");
					return false;
				}else{
					var save = {'data':$("#defec_pro_from_table_data").handsontable('getData')};
					$.ajax({
					    url:"index.php?mod=purchase&con=DefectiveProduct&act=getJson&id="+id,
					    data:save,
					    dataType:"json",
					    type:"POST",
					    success:function(res) {
							if (res.success == 1) {
								$('#defective_product_info').submit();
							} else {
								bootbox.alert(res.error);
							}
					    }
					});
				}
			});
		};*/
		     ////
		return {
			init:function(){
				initElements();	
				handleForm();
				initData();
			//	from_table();
			}
		}
			////
		
	}();
	obj.init();
				 
});