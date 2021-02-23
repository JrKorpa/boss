searchXianhuo();
//searchFushi();
searchWarehouse();
function searchFushi(){
	var style_sn= '<%$view->get_style_sn()%>';
	var bc_id = '<%$view->get_id()%>';
	var url = 'index.php?mod=processor&con=ProductInfo&act=getStyleFushi'
    $.post(url,{'bc_id':bc_id,'style_sn':style_sn},function(e){
		 if(e.success==1){
			 $("#styleFushi1").html(e.data.fushi1);
			 $("#styleFushi2").html(e.data.fushi2);
			 $("#styleFushi3").html(e.data.fushi3);
		 }
	},'json');
}
/*布产现货查询*/
function  searchXianhuo(){
	var bc_id = '<%$view->get_id()%>';
	var style_sn= '<%$view->get_style_sn()%>';
	var bc_status = '<%$view->get_status()%>';
	var from_type = '<%$view->get_from_type()%>';
	var order_gd_id = '<%$view->get_p_id()%>';
	if((bc_status== 1)&&(from_type == 2)){//初始化且为订单来源
	    $('#product_info_show_page_t').before("<div id='product_info_show_page_xianhuo'><h1 style='color:red'>正在加载不需布产信息....</h1></div>");
		var url = "index.php?mod=processor&con=ProductInfo&act=hasSreach";		
		$.post(url,{'bc_id':bc_id,'order_gd_id':order_gd_id,'style_sn':style_sn},function(e){
			$('#product_info_show_page_xianhuo').html(e);
		});
	}

}
/**
 * 
 */
function searchWarehouse(){
	var buchan_sn = '<%$view->get_bc_sn()%>';
	var url = "index.php?mod=processor&con=ProductInfo&act=getInWarehouseInfo";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "JSON",
		data:{buchan_sn:buchan_sn},
		success: function(res){                        
			var data = res.data; 
			console.log(data);
			var html ="";
			$.each(data,function(key,value){
				html += "<tr><td>"+value['bill_no']+"</td><td>"+value['num']+"</td></t>";
			});
			$("#in_warehouse_info").append(html);
		}
	});
}
function to_factory(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var tab_id = $(o).attr('list-id');
	var prc_id='<%$view->get_prc_id()%>';
	var type='<%$view->get_from_type()%>';
	var status='<%$view->get_status()%>';
	if(status!=3)
	{
		util.xalert('只有已分配的才能开始生产');
		return false;
	}
	
	     bootbox.confirm("确定开始生产?", function(result) {
			if (result == true) {
				setTimeout(function(){
					$.post(url,{id:id},function(data){
						$('.modal-scrollable').trigger('click');
						if(data.success==1){
							bootbox.alert('操作成功');
							$('.modal-scrollable').trigger('click');
							//util.refresh("productinfo-"+id,data.title,'index.php?mod=processor&con=ProductInfo&act=show&id='+id);
							util.retrieveReload();
						}
						else{
							bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
						}
					});
				}, 0);
			}
		});
	/*
	if((prc_id==452||prc_id==416) && type==2)
	{
		$(o).attr('data-url','/index.php?mod=processor&con=ProductInfo&act=to_factory_edit');
		util.retrieveEdit(o);
	}
	else
	{
		bootbox.confirm("确定开始生产?", function(result) {
			if (result == true) {
				setTimeout(function(){
					$.post(url,{id:id},function(data){
						$('.modal-scrollable').trigger('click');
						if(data.success==1){
							bootbox.alert('操作成功');
							$('.modal-scrollable').trigger('click');
							//util.refresh("productinfo-"+id,data.title,'index.php?mod=processor&con=ProductInfo&act=show&id='+id);
							util.retrieveReload();
						}
						else{
							bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
						}
					});
				}, 0);
			}
		});
	}
	*/
}
	$("#picture_thumbnail li img").click(function(){
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
		$('#pic_btn-right').css({cursor: 'auto'});
		$('#pic_btn-right').removeClass("dasabled");
	}
	$('#pic_btn-right').click(function() {
		if ($slider_child_l < 5 || slider_count >= $slider_child_l - 5) {
			return false;
		}
		slider_count++;
		$slider.animate({left: '-=' + $slider_width + 'px'}, 'fast');
		slider_pic();
	});
	$('#pic_btn-left').click(function() {
		if (slider_count <= 0) {
			return false;
		}
		slider_count--;
		$slider.animate({left: '+=' + $slider_width + 'px'}, 'fast');
		slider_pic();
	});
	function slider_pic() {
		if (slider_count >= $slider_child_l - 5) {
			$('#pic_btn-right').css({cursor: 'auto'});
			$('#pic_btn-right').addClass("dasabled");
		}
		else if (slider_count > 0 && slider_count <= $slider_child_l - 5) {
			$('#pic_btn-left').css({cursor: 'pointer'});
			$('#pic_btn-left').removeClass("dasabled");
			$('#pic_btn-right').css({cursor: 'pointer'});
			$('#pic_btn-right').removeClass("dasabled");
		}
		else if (slider_count <= 0) {
			$('#pic_btn-left').css({cursor: 'auto'});
			$('#pic_btn-left').addClass("dasabled");
		}
	}


function printBills(o){
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_p_sn()%>';
	var bc_id = '<%$view->get_id()%>';
	var from_type = '<%$view->get_from_type()%>';
	var tab_id = $(o).attr('list-id');
        var id = '<%$view->get_p_sn()%>';
        var from_type = '<%$view->get_from_type()%>';
        var url = "index.php?mod=processor&con=ProductInfo&act=printBills";
        var _name = $(o).attr('data-title');
        var son = window.open(
        url+'&id='+id+'&from_type='+from_type+"&bc_id="+bc_id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes'
        );

}

function givezuan(obj){
	var bc_id = '<%$view->get_id()%>';
	var url = "index.php?mod=processor&con=ProductFactoryOpra&act=songZuan&id="+bc_id;

	bootbox.confirm("确定开始送钻操作?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.get(url , '' ,function(res){
					$('.modal-scrollable').trigger('click');
					if(res.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
					}else{
						bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
					}
				});
			}, 0);
		}
	});
}
function confirms(obj){
	var bc_id = '<%$view->get_id()%>';
	var url = "index.php?mod=processor&con=ProductInfo&act=allConfirm&id="+bc_id;

	bootbox.confirm("确定确认操作?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.get(url , '' ,function(res){
					$('.modal-scrollable').trigger('click');
					if(res.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
					}else{
						bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
					}
				});
			}, 0);
		}
	});
}

//展示分配工厂页面
function fenpei_factory(obj){
	var id = '<%$view->get_id()%>';
	var url = 'index.php?mod=processor&con=ProductInfo&act=sel_factory&c=show&id='+id;
	util._pop(url);
}
//4C销售单修改证书，货号，采购价的展示页权限验证
function edit_4c(obj){
	var id = '<%$view->get_id()%>';
	var url = 'index.php?mod=processor&con=ProductInfo&act=edit_4c&id='+id;
	util._pop(url);
}
function combinexq_print(obj){
	var id = '<%$view->get_id()%>';
	var url ='index.php?mod=processor&con=ProductInfo&act=combineXQPrint&id='+id;
	window.open(url,"标签打印",'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
}
