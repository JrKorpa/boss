util.hover();
function toDingzhi(obj){
    var url = $(obj).attr("data-url");//转定制url
	var ids = [];
	$('#order_details_list input[name="_ids[]"]:checked').each(function(){
		ids.push($(this).val());
	});	
	if(ids.toString()==""){
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var referer = $(obj).data("referer");
	if (referer=='EGL双旦') {
		if (!window.confirm('EGL双旦活动的现货订单与客户约定7个工作日发货，请确保已经与客户确认可以接受转为定制，如果还没有确认，请点击否，确认后再来转为定制！')) {
			return false;
		}
	}

	$.ajax({
		type:"POST",
		url: url,
		data:{'ids':ids},
		dataType: "json",
		async:false,
		success: function(res){
			if(res.success==1){
			   	util.xalert(res.content,function(){
					util.retrieveReload();						 
				});
			}else if(res.error){
			    util.xalert(res.error);	
			}else{
				util.xalert(res);	
			}
		},
		error:function(e){
		   alert("ajax异常");	
		}
	});
}

function showGift(obj){
	$("#gifts").toggle('slow');
}


function addGift(obj){
	var gift_remark = $('#gift_remark').val();
	var gift_reason = $('#gift_reason').val();
	if($('#order_status').val() == 2 && gift_reason =='' ){
		util.xalert("订单已审核，原因必填。");
		return false;
	}
	
	var gifts = [];
	$("#gifts input[type='checkbox']:checked").each(function(){
		var id= $(this).val();
		var gift_object = $('#gifts input[name="gift_num['+id+']"]');
		gifts.push({'goods_name':gift_object.attr("data-name"),'goods_sn':gift_object.attr("data-goods-number"), 'chengjiaojia':gift_object.attr("data-price"), 'goods_type':'zengpin_goods', 'info':gift_remark, 'gift_reason' :gift_reason, 'num' : gift_object.val()});
	});	
	
	
	var url = $(obj).attr("data-url");

	$.post(url,{'gifts':gifts},function(res){
		if(res.success==1){
			util.xalert(res.content,function(){
				util.retrieveReload();						 
			});
		}else if(res.error){
			util.xalert(res.error);	
		}else{
			util.xalert(res);	
		}
	});
}


function deleteGift(obj){
    var url = $(obj).attr("data-url");//转定制url

	var ids = [];

	$('#order_details_list input[name="_ids[]"]:checked').each(function(){
		ids.push($(this).val());
	});	
	if(ids.toString()==""){
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	
	var note = $('#order_status').val() == 2 ? '（订单已审核，原因必填。）<span class="required">*</span>' : '';
	bootbox.dialog({
        message: '<form class="form-horizontal" role="form"><div class="form-group">\
                        <label class="col-sm-3 control-label no-padding-right">\
                        删除原因：<br/>'+ note +'</label>\
                        <div class="col-sm-9">\
                            <textarea class="form-control" id="info" name="info" placeholder="原因"></textarea>\
                        </div>\
                 </div></form>',
        title: "删除赠品",
        buttons:             
        {
            "success" :
             {
                "label" : "<i class='icon-ok'></i> 提交",
                "className" : "btn-sm btn-success",
                "callback": function() {
					var info = $('#info').val();
					if($('#order_status').val() == 2 && info =='' ){
						util.xalert("订单已审核，原因必填。");
						return false;
					}
                    $.post(url,{'ids':ids, 'info':info},function(res){
						if(res.success==1){
							util.xalert(res.content,function(){
								util.retrieveReload();						 
							});
						}else if(res.error){
							util.xalert(res.error);	
						}else{
							util.xalert(res);	
						}
					});
                }
            }
        }
    });
}


function toXianhuo(obj){
    var url = $(obj).attr("data-url");//转定制url
    var order_sn = $(obj).attr("order_sn");//订单号

	var ids = [];

	$('#order_details_list input[name="_ids[]"]:checked').each(function(){
		ids.push($(this).val());
	});	
	if(ids.toString()==""){
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	
	$('body').modalmanager('loading');
	
	setTimeout(function(){
		$.post(url,{'ids':ids,'order_sn':order_sn},function(data){
			//alert(data);return false;
			if (typeof data !='object')
			{
				$('.modal .modal-body').html(data);
			}
			if (data.title)
			{
				$('.modal .modal-title').show();
				$('.modal .modal-title').html(data.title);
			}
			else
			{
				$('.modal .modal-title').hide();
			}
			$('.modal .modal-body').html(data.content);
			//$('.modal .modal-footer').hide();
			//$('.modal').modal({backdrop: 'static', keyboard: false});
			$('.modal').modal("toggle");
		});
	}, 200);
}


$('table.flip-content tbody tr').each(function(){
	if ($(this).attr('del')==1)
	{
		$(this).children().each(function(){
			$(this).attr('style',"position:relative;");
			$(this).append('<div style="width:100%;position:absolute;top:14px;left:-1px;border-bottom:solid 1px red;"></div><div style="width:100%;position:absolute;top:19px;left:-1px;border-bottom:solid 1px red;"></div>');
		});
	}
});

	//复选框组美化
var test = $("#app_order_details_search_list input[type='checkbox']:not(.toggle, .make-switch)");
if (test.size() > 0) {
	test.each(function () {
	if ($(this).parents(".checker").size() == 0) {
		$(this).show();
		$(this).uniform();
	}
  });
}
// table 复选框全选
$('#app_order_details_search_list .group-checkable').change(function () {
  var set = $(this).attr("data-set");
	var checked = $(this).is(":checked");
	$(set).each(function () {
		if (checked) {
			$(this).attr("checked", true);
			$(this).parents('tr').addClass("active");
		} else {
			$(this).attr("checked", false);
			$(this).parents('tr').removeClass("active");
		}                    
	});
	$.uniform.update(set);
});
$('#app_order_details_search_list').on('change', 'tbody tr .checkboxes', function(){
	$(this).parents('tr').toggleClass("active");
});

function countermandOccupy (detail_id) {
    bootbox.confirm("亲！确认取消占用？", function(result) {
        if (result == true) {
            var url = 'index.php?mod=sales&con=AppOrderDetails&act=countermandOccupy';
            var data = {'detail_id':detail_id};
            $.post(url,data,function(res)
            {
                if(res.success == 1){
                    util.xalert("操作成功！");
                    util.retrieveReload();
                    return false;
                }
                util.xalert(res.error);
                return false;
            },'json');
        }
    });
}
