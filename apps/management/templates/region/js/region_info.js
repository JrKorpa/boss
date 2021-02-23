$import("public/js/select2/select2.min.js",function(){
	var id = '<%$view->get_region_id()%>';
	var region_type= '<%$view->get_region_type()%>';
	var parent_id= '<%$view->get_parent_id()%>';
	var Regionobj = function(){
			var initElements = function(){
				if (!jQuery().uniform) {
					return;
				}
				$('#region_info select[name="region_type"]').select2({
					placeholder: "请选择",
					allowClear: true,
					value: region_type
				}).change(function (e) {

					$(this).valid();
					$('#region_info select[name="parent_id"]').empty();
					$('#region_info select[name="parent_id"]').append('<option value=""></option>');
					var _t = $(this).val();
					if (_t) {
						$.post('index.php?mod=management&con=region&act=getparent_id', {region_type: _t}, function (data) {
							$('#region_info select[name="parent_id"]').append(data);
							if (_t == region_type) {
								$('#region_info select[name="parent_id"]').select2("val", parent_id, true);
							}
							$('#region_info select[name="parent_id"]').change();
						});
					}
					else {
						$('#region_info select[name="region_info_parent_id"]').change();
					}
				});
				$('#region_info select[name="parent_id"]').select2({
					placeholder: "请选择",
					allowClear: true,
					value: parent_id
				});
			
			}
			var initData = function(){
				$('#region_info :reset').on('click', function () {

					$('#region_info select[name="region_type"]').select2("val", region_type).change();
                    $('#region_info select[name="parent_id"]').select2("val",parent_id).change();

				})
				if (region_type) {//修改
					$('#region_info :reset').click();
				}
				
			}
			var handleForm = function(){
					var url = id ? 'index.php?mod=management&con=Region&act=update' : 'index.php?mod=management&con=Region&act=insert';
					var options1 = {
						url: url,
						error:function ()
						{
							util.timeout('region_info');
						},
						beforeSubmit:function(frm,jq,op){
							return util.lock('region_info');
						},
						success: function(data) {
							$('#region_info :submit').removeAttr('disabled');//解锁
							if(data.success == 1 ){
								$('.modal-scrollable').trigger('click');//关闭遮罩(当前弹出框和背景锁定)
								util.xalert(
									id ? "修改成功!": "添加成功!",
									function(){
										if (id)
										{//刷新当前页
											util.page(util.getItem('url'));
										}
										else
										{//刷新首页
											region_search_page(util.getItem("orl"));
										}
									}
								);
							}
							else
							{
								util.error(data);//错误处理
							}
						}
					};

					$('#region_info').validate({
						errorElement: 'span', //default input error message container
						errorClass: 'help-block', // default input error message class
						focusInvalid: false, // do not focus the last invalid input
						rules: {
							region_name: {
								required: true
							},
							region_type:{
								required:true
							}
						},
						messages: {
							region_name: {
								required: "地区名称不能为空."
							},
							region_type:{
								required:"地区类型不能为空."
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
							if (parseInt($('#region_info select[name="region_type"]').val()))
							{
								if (!parseInt($('#region_info select[name="parent_id"]').val()))
								{
									util.xalert('请选择上级地区');
									return false;
								}	
							}
							$("#region_info").ajaxSubmit(options1);
						}
					});
					//回车提交
					$('#region_info input').keypress(function (e) {
						if (e.which == 13) {
							$('#region_info').validate().form();
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
		
	}();
	Regionobj.init();
				 
});