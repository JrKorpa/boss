var info_form_id = 'vip_picklist_show_form';//form表单id
var info_form_base_url = 'index.php?mod=warehouse&con=VipPickList&act=';//基本提交路径
util.hover();
var pick_no = "<%$data.pick_no%>";
function vip_update_pick(pick_no){	
	if(pick_no==""){
	    return false;	
	}
	var url = "cron/vip/index.php?act=updatepicklist&pick_no="+pick_no;
	$.ajax({
		type:"POST",
		url: url,
		data: {	},
		dataType: "json",
		async:true,
		success: function(res){
			
		}
	});
}

// JavaScript Document
$import(["public/js/select2/select2.min.js"],function(){	
	
	
	var obj = function(){
		var initElements = function(){
			//初始化单选按钮组
			if (!jQuery().uniform) {
				return;
			}
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			});
			
		};
		
		//表单验证和提交
		var handleForm = function(){
			
		};
		var initData = function(){
		   vip_update_pick(pick_no);
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});