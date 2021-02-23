function to_factory(o){
	$('body').modalmanager('loading');
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var tab_id = $(o).attr('list-id');

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
	var from_type = '<%$view->get_from_type()%>';
	var tab_id = $(o).attr('list-id');
	$.post("index.php?mod=processor&con=ProductInfo&act=printBills",{id:id,from_type:from_type},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = '<%$view->get_p_sn()%>';
			var from_type = '<%$view->get_from_type()%>';
			var url = "index.php?mod=processor&con=ProductInfo&act=printBills";
			var _name = $(o).attr('data-title');
			var son = window.open(
			url+'&id='+id+'&from_type='+from_type,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes'
			);
			son.onUnload = function(){
			};
		}
	 });
}

function givezuan(obj){
	var id = '<%$view->get_id()%>';
	var fac_opra = '6';
	var opra_info = '送钻';
	var url = "index.php?mod=processor&con=ProductFactoryOpra&act=insert&id="+id+"&fac_opra="+fac_opra+"&opra_info="+opra_info;

	bootbox.confirm("确定开始送钻操作?", function(result) {
		if (result == true) {
			setTimeout(function(){
				$.get(url , '' ,function(res){
					$('.modal-scrollable').trigger('click');
					if(res.success==1){
						bootbox.alert('操作成功');
						$('.modal-scrollable').trigger('click');
						util.retrieveReload();
					}
					else{
						bootbox.alert(res.error ? res.error : ( res ? res : '程序异常'));
					}
				});
			}, 0);
		}
	});


}


//打印加工流水单
function print_jiagong(o){
	var url =$(o).attr('data-url') ;
	var id = '<%$view->get_id()%>';
	var tab_id = $(o).attr('list-id');
	$.post("index.php?mod=processor&con=ProductInfo&act=print_jiagong",{id:id},function(res){
		if(res.error){
			alert(res.error);
		}else{
			var id = '<%$view->get_id()%>';
			var url = "index.php?mod=processor&con=ProductInfo&act=print_jiagong";
			var _name = $(o).attr('data-title');
			var son = window.open(
			url+'&id='+id,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false,scrollbars=yes');
			son.onUnload = function(){
			};
		}
	});
}
