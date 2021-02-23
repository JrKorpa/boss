$import(["public/js/select2/select2.min.js"],function(){
	var info_form_id = 'batch_express_upload_file_form';//form表单id

	var Ojb = function(){
		var initElements = function () {
			if (!jQuery().uniform) {
				return;
			}
            $('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
		}

		var handleForm = function(){
			var opt = {
				url: "index.php?mod=shipping&con=BatchExpress&act=insertFile",
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
						$('.modal-scrollable').trigger('click');//关闭遮罩
						util.xalert("操作成功！",function(){
							util.retrieveReload();
						});															
					}else{
						$('body').modalmanager('removeLoading');//关闭进度条
						if(data.error){
							util.xalert(data.error,function(){ });
						}else{
							util.xalert(data,function(){ });
						}
					}
				}, 
				error:function(){
					$('.modal-scrollable').trigger('click');
					alert("数据加载失败");  
				}
			};

			$("#"+info_form_id+" .btn_save").click(function(){
			   $("#"+info_form_id).ajaxSubmit(opt);						  
			});
	
		}
		var initData = function(){

		}

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();

	Ojb.init();
});