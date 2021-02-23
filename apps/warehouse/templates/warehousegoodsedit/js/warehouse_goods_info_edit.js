function loglist(url){
	util.page(url);
}

$import(["public/js/select2/select2.min.js"],function(){
	var info_form_base_url = 'index.php?mod=warehouse&con=WarehouseGoodsEdit&act=update';//基本提交路径
	var info_id= $('#warehouse_goods_info_edit input[name="id"]').val();
	var objlist = $('#update_log_list');

	var obj = function(){
			$('#warehouse_goods_info_edit select').select2({
			placeholder: "请选择",
			allowClear: true,
			}).change(function(e){
			$(this).valid();
			});	
		//表单验证和提交
		var handleFormxx = function(){
			/** 提交修改 **/
			$('#warehouse_goods_info_edit_btn').click(function(){
				util.lock('warehouse_goods_info_edit');
				//获取修改的数据
				var k_sn	= $('#warehouse_goods_info_edit input[name="goods_sn"]').val();
				var goods_name = $('#warehouse_goods_info_edit input[name="goods_name"]').val();
				var shoucun = $('#warehouse_goods_info_edit input[name="shoucun"]').val();
				var changdu = $('#warehouse_goods_info_edit input[name="changdu"]').val();
				var zhengshuleibie = $('#warehouse_goods_info_edit input[name="zhengshuleibie"]').val();
				var zhengshuhao = $('#warehouse_goods_info_edit input[name="zhengshuhao"]').val();
				var gemx_zhengshu = $('#warehouse_goods_info_edit input[name="gemx_zhengshu"]').val();
				var pinpai = $('#warehouse_goods_info_edit input[name="pinpai"]').val();
				//var zhushitiaoma = $('#warehouse_goods_info_edit input[name="zhushitiaoma"]').val();
				var buchan_sn = $('#warehouse_goods_info_edit input[name="buchan_sn"]').val();
				var caizhi = $('#warehouse_goods_info_edit select[name="caizhi"]').val();
				
				var jinzhong = $('#warehouse_goods_info_edit input[name="jinzhong"]').val();
                var zongzhong = $('#warehouse_goods_info_edit input[name="zongzhong"]').val();
				var zuanshidaxiao = $('#warehouse_goods_info_edit input[name="zuanshidaxiao"]').val();
				var fushizhong = $('#warehouse_goods_info_edit input[name="fushizhong"]').val();
				var jietuoxiangkou = $('#warehouse_goods_info_edit input[name="jietuoxiangkou"]').val();
				var zhushixingzhuang = $('#warehouse_goods_info_edit select[name="zhushixingzhuang"]').val();
				var zhushijingdu = $('#warehouse_goods_info_edit select[name="zhushijingdu"]').val();
				var zhushiyanse = $('#warehouse_goods_info_edit select[name="zhushiyanse"]').val();
				var qiegong = $('#warehouse_goods_info_edit select[name="qiegong"]').val();
				var paoguang = $('#warehouse_goods_info_edit select[name="paoguang"]').val();
				var duichen = $('#warehouse_goods_info_edit select[name="duichen"]').val();
				var yingguang = $('#warehouse_goods_info_edit select[name="yingguang"]').val();
				var tuo_type = $('#warehouse_goods_info_edit select[name="tuo_type"]').val();
				var jingxiaoshangchengbenjia = $('#warehouse_goods_info_edit input[name="jingxiaoshangchengbenjia"]').val();
				var management_fee = $('#warehouse_goods_info_edit input[name="management_fee"]').val();
				var data = {
						id:info_id,
						goods_sn:k_sn,
						goods_name:goods_name,
                        shoucun:shoucun,
						changdu:changdu,
						zhengshuleibie:zhengshuleibie,
						zhengshuhao:zhengshuhao,
						gemx_zhengshu:gemx_zhengshu,
						pinpai:pinpai,
						buchan_sn:buchan_sn,
						caizhi:caizhi,
						jinzhong:jinzhong,
                        zongzhong:zongzhong,
						zuanshidaxiao:zuanshidaxiao,
						fushizhong:fushizhong,
						jietuoxiangkou:jietuoxiangkou,
						zhushixingzhuang:zhushixingzhuang,
						zhushiyanse:zhushiyanse,
						qiegong:qiegong,
						duichen:duichen,
						yingguang:yingguang,
						tuo_type:tuo_type,
						paoguang:paoguang,
						zhushijingdu:zhushijingdu,
						jingxiaoshangchengbenjia,
						management_fee

				}
				$.post(info_form_base_url, data, function(res){
					if(res.success == 1){
						bootbox.alert({
							message : res.error,
							buttons : {
								ok : {
									label : '确定'
								}
							},
							animate : true,
							closeButton : false,
							title : "提示信息",
						});
						util.sync(objlist);
						loglist('index.php?mod=warehouse&con=WarehouseGoodsEdit&act=getUpdateLog&goods_id='+<%$result.goods_id%>)
						
					}else{
						bootbox.alert({
							message : res.error,
							buttons : {
								ok : {
									label : '确定'
								}
							},
							animate : true,
							closeButton : false,
							title : "提示信息",
						});
					}
				});
			});
		};

		return {
			init:function(){
				handleFormxx();//处理表单验证和提交
			}
		}
	}();
	obj.init();

	var goods_id = $('#warehouse_goods_search_form_edit input[name="goods_id"]').val();
	util.setItem('orl','index.php?mod=warehouse&con=WarehouseGoodsEdit&act=getUpdateLog&goods_id='+goods_id);
	util.setItem('listDIV',"loglist");
	var obj2 = function(){
		var initElements2 = function(){
			loglist(util.getItem("orl"));
		}

		return {
			init : function(){
				initElements2();// 处理表单元素
			}
		}
	}();
	if(goods_id != ""){
		obj2.init();
	}

});

/*处理图片的滚动问题*/
$("#picture_thumbnailb li img").click(function(){
		$(".picture_zoompic img").attr("src",$(this).attr("src"));
		$(this).parents("li").addClass("current").siblings('li').removeClass("current");
		return false;
	});
	$(".picture_zoompic>img").load(function(){
		$(".picture_zoompic>img:hidden").show();
	});
	//小图片左右滚动
	var $slider = $('.pic_slider ul');
	var $slider_child_l = $('.pic_slider ul li').length;
	var $slider_width = $('.pic_slider ul li').width();
	$slider.width($slider_child_l * $slider_width);
	var slider_count = 0;
	if ($slider_child_l < 5) {
		$('#pic_btn-rightb').css({cursor: 'auto'});
		$('#pic_btn-rightb').removeClass("dasabled");
	}
	$('#pic_btn-rightb').click(function() {
		if ($slider_child_l < 5 || slider_count >= $slider_child_l - 5) {
			return false;
		}
		slider_count++;
		$slider.animate({left: '-=' + $slider_width + 'px'}, 'fast');
		slider_pic();
	});
	$('#pic_btn-leftb').click(function() {
		if (slider_count <= 0) {
			return false;
		}
		slider_count--;
		$slider.animate({left: '+=' + $slider_width + 'px'}, 'fast');
		slider_pic();
	});
	function slider_pic() {
		if (slider_count >= $slider_child_l - 5) {
			$('#pic_btn-rightb').css({cursor: 'auto'});
			$('#pic_btn-rightb').addClass("dasabled");
		}
		else if (slider_count > 0 && slider_count <= $slider_child_l - 5) {
			$('#pic_btn-leftb').css({cursor: 'pointer'});
			$('#pic_btn-leftb').removeClass("dasabled");
			$('#pic_btn-rightb').css({cursor: 'pointer'});
			$('#pic_btn-rightb').removeClass("dasabled");
		}
		else if (slider_count <= 0) {
			$('#pic_btn-leftb').css({cursor: 'auto'});
			$('#pic_btn-leftb').addClass("dasabled");
		}
	}







