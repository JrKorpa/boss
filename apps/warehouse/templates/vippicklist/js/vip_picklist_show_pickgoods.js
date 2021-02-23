util.hover();
var info_form_id = 'pickdetails_pickgoods_form';//form表单id
var info_form_base_url = 'index.php?mod=warehouse&con=VipPickList&act=';//基本提交路径
var pick_no = "<%$pick_info.pick_no%>";
function nextTab(tabIndex){
	$("#"+info_form_id+" .tab_click").removeClass("tab_click");
	$("#"+info_form_id+" .js_tr_"+tabIndex).addClass("tab_click");
	$("#"+info_form_id+" .js_goods_id[tabindex=" + tabIndex + "]").focus();
}
function loadXianhuo(tabIndex,data){
	var box = $("#"+info_form_id+" .list-"+tabIndex);	
	if(data == null){
		//重置表单数据
		box.find("input[name='goods[style_sn][]']").val('').attr("readonly",false);//款号
		box.find("input[name='goods[cart][]']").val('');//主石单颗重
		box.find("input[name='goods[zhushi_num][]']").val('');//主石粒数
		box.find("input[name='goods[jinzhong][]']").val('');//金重

		box.find("select[name='goods[color][]']").select2('val','').change();//主石颜色
		box.find("select[name='goods[clarity][]']").select2('val','').change();//主石净度
		box.find("input[name='goods[xiangkou][]']").val('');//镶口
		box.find("select[name='goods[caizhi][]']").select2('val','').change();//材质
		box.find("select[name='goods[jinse][]']").select2('val','').change();//金色
		box.find("input[name='goods[zhiquan][]']").val('');//指圈
		box.find("select[name='goods[cert][]']").select2('val','').change();//证书类型
		box.find("input[name='goods[zhengshuhao][]']").val('');//证书号
		box.find("select[name='goods[face_work][]']").select2('val','按工厂原版').change();//表面工艺
		box.find("select[name='goods[xiangqian][]']").select2('val','工厂配钻，工厂镶嵌').change();//表面工艺
	}else{
		//填充表单数据
		box.find("input[name='goods[style_sn][]']").val(data.goods_sn).attr("readonly",true);//款号
		box.find("input[name='goods[cart][]']").val(data.zuanshidaxiao);//主石单颗重
		box.find("input[name='goods[zhushi_num][]']").val(data.zhushilishu);//主石粒数
		box.find("input[name='goods[jinzhong][]']").val(data.jinzhong);//金重

		box.find("select[name='goods[color][]']").select2('val',data.zhushiyanse).change();//主石颜色
		box.find("select[name='goods[clarity][]']").select2('val',data.zhushijingdu).change();//主石净度
		box.find("input[name='goods[xiangkou][]']").val(data.jietuoxiangkou);//镶口
		box.find("select[name='goods[caizhi][]']").select2('val',data.caizhi).change();//材质
		box.find("select[name='goods[jinse][]']").select2('val',data.jinse).change();//金色
		box.find("input[name='goods[zhiquan][]']").val(data.shoucun);//指圈
		box.find("select[name='goods[cert][]']").select2('val',data.zhengshuleibie).change();//证书类型
		box.find("input[name='goods[zhengshuhao][]']").val(data.zhengshuhao);//证书号
		
		box.find("select[name='goods[face_work][]']").select2('val','按工厂原版').change();//表面工艺
		box.find("select[name='goods[xiangqian][]']").select2('val','工厂配钻，工厂镶嵌').change();//表面工艺
	}
}
function loadQihuo(tabIndex,data){
	var box = $("#"+info_form_id+" .list-"+tabIndex);	
	if(data == null){
		//重置表单数据
		loadXianhuo(tabIndex,null);
	}else{
		//填充表单数据
		box.find("input[name='goods[zhushi_num][]']").val(data.zhushi_num);//主石粒数		
	}
}	
//根据拣货单创建客订单
/*
function vip_create_order(obj){
	var url = $(obj).attr('data-url');
	var options1 = {
			url: url,
			error:function ()
			{
				util.timeout(info_form_id);
			},
			beforeSubmit:function(frm,jq,op){
				return util.lock(info_form_id);
			},
			success: function(res) {
				if(res.success==1){
					util.xalert("保存成功，制单已完成！",function(){
						util.retrieveReload();
					});					
				}else{
					util.xalert(res.error);					
				}
			}
    };
    $("#"+info_form_id).ajaxSubmit(options1);
}*/
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
		
			$("#"+info_form_id+" .js_goods_id").keypress(function(event){
				 var goods_id = $.trim($(this).val());
				 if(event.keyCode==13 && goods_id!=''){				    
					$(this).blur();						
				 };
		   });
			
			$("#"+info_form_id+" .js_goods_id").blur(function(event){		
				var goods_id = $.trim($(this).val());
				var tabIndex = parseInt($(this).attr("tabindex"));
				if(goods_id!=''){
				    $.ajax({
						type:"POST",
						url: info_form_base_url+'getGoodsInfoAjax',
						data: {
							goods_id:goods_id,
						},
						dataType: "json",
						async:false,
						success: function(res){
							if(res.success==1){
							   loadXianhuo(tabIndex,res.data);
							   nextTab(tabIndex+1);
							}else{							   
							   util.xalert(res.error,function(){
									loadXianhuo(tabIndex,null);
							   });
							   
							}
						}
					});
					
				}else{
				    loadXianhuo(tabIndex,null);	
				}
		   });
			
			//根据款号带出期货信息
			/*$("#"+info_form_id+" .js_style_sn").keypress(function(event){
				 var style_sn = $.trim($(this).val());
				 if(event.keyCode==13 && style_sn!=''){				    
					$(this).blur();						
				 };
		   });*/
			
			$("#"+info_form_id+" .js_style_sn").blur(function(event){		
				var style_sn = $.trim($(this).val());
				var tabIndex = parseInt($(this).attr("data-tabindex"));
				if(style_sn!=''){
				    $.ajax({
						type:"POST",
						url: info_form_base_url+'getStyleAttrsAjax',
						data: {
							style_sn:style_sn,
						},
						dataType: "json",
						async:false,
						success: function(res){
							if(res.success==1){
							   loadQihuo(tabIndex,res.data);
							}else{							   
							   util.xalert(res.error,function(){
									loadQihuo(tabIndex,null);
							   });
							   
							}
						}
					});
					
				}
		   });
	
			
		};
		
		//表单验证和提交
		var handleForm = function(){
			var url = "index.php?mod=warehouse&con=VipPickList&act=pickGoodsSave";
			var options1 = {
				url: url,
				error:function ()
				{
					util.timeout(info_form_id);
				},
				beforeSubmit:function(frm,jq,op){
					return util.lock(info_form_id);
				},
				success: function(res) {
					$('#'+info_form_id+' :submit').removeAttr('disabled');//解锁	
					if(res.success==1){
						util.xalert("操作成功，订单生成成功！",function(){
							util.retrieveReload();
						});					
					}else{
						util.xalert(res.error);					
					}
				}
			};			
			//提交
			$('#'+info_form_id+' #pickGoodsBtn').click(function (e) {	
				$("#"+info_form_id).ajaxSubmit(options1);
			});
		};
		var initData = function(){
		   vip_update_pick(pick_no);
		   $('#'+info_form_id+' :reset').on('click',function(){
				$('#'+info_form_id+' select').select2('val','');
		   });
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