$import(["public/js/select2/select2.min.js"],function(){
	var info_form_id = 'app_bespoke_info_edit_check';//form表单id
	var info_form_base_url = 'index.php?mod=bespoke&con=AppBespokeInfo&act=bespokeCheckDo';//基本提交路径

	var obj = function(){
		var initElements = function(){
			$('#app_bespoke_info_edit_check select[name="accecipt_man"]').select2({
				placeholder: "请选择销售渠道",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});  	
		};

		//表单验证和提交
		var handleForm = function(){
		}
		var initData = function(){}

		return {
			init:function(){
				initElements();
				handleForm();
				initData();
			}
		}
	}();
	obj.init();
});


$("#app_bespoke_info_edit_check button[type='button']").click(function(){
	var bespoke_id ='<%$view->get_bespoke_id()%>';
	var remark=$("#app_bespoke_info_edit_check textarea[name='remark']").val();
	var accecipt_man=$("#app_bespoke_info_edit_check select[name='accecipt_man']").val();
	var tab_id=0;
	if(accecipt_man==''){
		bootbox.alert('请选择销售顾问.');
		return false;
	}
	
	$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=bespokeCheckDo',{id:bespoke_id,accecipt_man:accecipt_man,remark:remark},function(data){
		$('.modal-scrollable').trigger('click');
		if(data.success==1){
			bootbox.alert('提交成功');
            // $('.table-toolbar button[name=同步]').trigger('click');
            util.page(util.getItem('url'));
			$('.modal-scrollable').trigger('click');
			to_look_into();
		}
		else{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}
	});
});
