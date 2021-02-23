$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
var BaseMemberInfoObj = function(){

    var initElements = function(){
		$('select[name="make_order"]').select2({
			placeholder: "请选择销售渠道",
			allowClear: true
		}).change(function(e){
			$(this).valid();
		});  	
    }
    var handleForm = function(){}
    var initData = function(){}

    return {
        init:function(){
            initElements();
            handleForm();
            initData();
        }
    }
}();
BaseMemberInfoObj.init();
});
function finish_Bespoke0()
{
	var osalesstage = document.getElementsByName('salesstage');
	var obrandimage = document.getElementsByName('brandimage');
	var ouser_level = document.getElementsByName('user_level');
	var gift_type = document.getElementsByName('gift_type');
	var gift_id = document.getElementsByName('gift_id[]');
	
	var salesstage  = "";
	var brandimage  = "";
	var user_level = "";
	var g_type = "";
	var g_id = "";
	var tab_id = 0;
	for(i=0;i<osalesstage.length;i++){
		if(osalesstage[i].checked){
			salesstage = osalesstage[i].value;
			break;
		}
	}
	for(i=0;i<obrandimage.length;i++){
		if(obrandimage[i].checked){
			brandimage = obrandimage[i].value;
			break;
		}
	}
	for(i=0;i<ouser_level.length;i++){
		if(ouser_level[i].checked){
			user_level = ouser_level[i].value;
			break;
		}
	}
	
	if(gift_type)
	{
		for(i=0;i<gift_type.length;i++){
			if(gift_type[i].checked){
				g_type = gift_type[i].value;
				break;
			}
		}
	}
	if(gift_id)
	{
	    var g_id=new Array();
		for(i=0;i<gift_id.length;i++){
			if(gift_id[i].checked){
				g_id[i] = gift_id[i].value;
			}
		}
	}


	if(salesstage==""){
		bootbox.alert("请选择'销售阶段'");
		return false;
		return false;
	}
	if(brandimage==""){
		bootbox.alert("请选择'品牌印象'");
		return false;
	}
	/*if (user_level == "")
	{
		bootbox.alert("请选择'客户类型'");
		return false;
	}

	//没有赠品列表的时候不做赠品的判断
	if(gift_type.length > 0) 
	{
		if (g_type == "")
		{
			alert ("请选择'赠品类型'");
			return false;
		}		
		 
		if(g_type > 0)
		{
			if(g_id == ""){
				alert ("请选择'赠品'");
				return false;  
			}
		}	

		if(g_type<0)
		{
			if(g_id != ""){
				alert ("无赠品的时候不能选择赠品了");
				return false;	        
			}

		}
	}*/
//	else
//	{
//		alert("请选择赠品类型");
//		return false;
//	}

	var action_note = document.getElementById("action_note");
	var beid = document.getElementById("beid");

	// 操作处理 
	var args='beid=' + beid.value + "&action_note=" + action_note.value + '&salesstage=' + salesstage + '&brandimage=' + brandimage + "&user_level="+user_level+"&gift_type="+g_type+"&gift_id="+g_id;
	$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=release_bespoke&is_ajax=1',args,function(data){
		$('.modal-scrollable').trigger('click');
		if(data.success==1){
			bootbox.alert('提交成功');
			$('.modal-scrollable').trigger('click');
			to_look_into();
			util.retrieveReload();
			util.syncTab(tab_id);
		}
		else{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}
	});
}

function save_make_bespoke0(id){
	var make_order = document.getElementById('make_order');
	var tab_id = 0;
	if(make_order.value == '')
	{
		bootbox.alert('请选择销售顾问');
		make_order.focus();
		return false;
	}
	var args = 'make_order='+make_order.value+'&bespoke_id='+id;
	$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=handover_bespoke&is_ajax=1',args,function(data){
		$('.modal-scrollable').trigger('click');
		if(data.success==1){
			bootbox.alert('提交成功,'+data.content);
			$('.modal-scrollable').trigger('click');
			util.retrieveReload();
			util.syncTab(tab_id);
		}
		else{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}
	});	
}

function wieldBespoke(act,bespoke_id)
{
	var tab_id = 0;
	if(bespoke_id==''){
		bootbox.alert('预约单号不能为空');
		return false;
	}
	var args = 'bespoke_id='+bespoke_id;
	$.post('index.php?mod=bespoke&con=AppBespokeInfo&act='+act+'&is_ajax=1',args,function(data){
		$('.modal-scrollable').trigger('click');
		if(data.success==1){
			bootbox.alert('操作成功');
			$('.modal-scrollable').trigger('click');
			util.retrieveReload();
			util.syncTab(tab_id);
			to_look_into();
		}
		else{
			bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
		}
	});	
}