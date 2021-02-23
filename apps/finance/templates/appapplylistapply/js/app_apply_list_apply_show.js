function app_apply_list_apply_goods_search_page(url){
	util.page(url,1);
}

function app_apply_list_apply_search_page(url){
	util.page(url,2);
}

$import(function(){
	util.setItem('orl1','index.php?mod=finance&con=AppApplyListApply&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('formID1','app_apply_list_apply_goods_search_form');
	util.setItem('listDIV1','app_apply_list_apply_goods_search_list');

	util.setItem('orl2','index.php?mod=finance&con=AppApplyListApply&act=GoodsSearch&_id=<%$view->get_apply_id()%>');//设定刷新的初始url
	util.setItem('formID2','app_apply_list_apply_search_form');
	util.setItem('listDIV2','app_apply_list_apply_search_list');

	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);
		}

		return {

			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_apply_list_apply_goods_search_page(util.getItem('orl1'));
			}
		}

	}();

	obj1.init();

	var obj2 = function(){
		var handleForm1 = function(){
			util.search(1);
		}

		return {

			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_apply_list_apply_search_page(util.getItem('orl2'));
			}
		}

	}();

	obj2.init();


	//util.closeDetail();//收起所有明细
	util.closeDetail(true);//展示第一个明细
});


function app_apply_list_apply_goods_download_mo()
{
    location.href = "index.php?mod=finance&con=AppApplyListApply&act=themes&target=demo";
}

function editApplySub()//生成应付单
{
	var ids = Array();
	var name=Array();
	
	//if($("#data").val()=='')
	//{
		var id = '';
		var t = 0;
		$("input[name='ids[]']").each(function(i, o){
			id = 'direc_'+$(o).val();
			if($("#"+id).val() == '')
			{
				bootbox.alert('偏差说明必选');
				t = 1;
				return false;
			}
			ids.push($(o).val());
			name.push($("#"+id).val());
		});
		
		if(t){
			return false;
		}
		
	//}
	
	var idd = ids.join();
	var names = name.join();
	var prc_id=$("#prc_id").val();
	var type=$("#type").val();
	var apply_id=$("#apply_id").val();
	var fapiao=$("#fapiao").val();

	if(prc_id==''){
		bootbox.alert('请选择结算商');
	}
	if(type==''){
		bootbox.alert('请选择应付类型');
	}

	$.post('?mod=finance&con=AppApplyListApply&act=editApplySub',{id:idd,name:names,prc_id:prc_id,type:type,apply_id:apply_id,fapiao:fapiao},function(res){
		$('.modal-scrollable').trigger('click');
		if(res.success==1){
			bootbox.alert('提交成功');
			$('.modal-scrollable').trigger('click');
			util.retrieveReload();
		//	PurchaseInfoObj.init();
		}
		else{
			bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
		}
	});
}

function subCon()//提交
{
	var apply_id=$("#apply_id").val();

	$.post('?mod=finance&con=AppApplyListApply&act=subCon',{apply_id:apply_id},function(res){
		$('.modal-scrollable').trigger('click');
		if(res.success==1){
			bootbox.alert('提交成功');
			$('.modal-scrollable').trigger('click');
			util.retrieveReload();
		//	PurchaseInfoObj.init();
		}
		else{
			bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
		}
	});
}

function delCon()//取消
{
	var apply_id=$("#apply_id").val();

	$.post('?mod=finance&con=AppApplyListApply&act=delCon',{apply_id:apply_id},function(res){
		$('.modal-scrollable').trigger('click');
		if(res.success==1){
			bootbox.alert('提交成功');
			$('.modal-scrollable').trigger('click');
			util.retrieveReload();
		//	PurchaseInfoObj.init();
		}
		else{
			bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
		}
	});
}