$import('public/js/select2/select2.min.js',function(){
    var serach_from_id = 'product_has_goods_search';
    var obj = function(){
        var initElements = function(){
            $('#'+serach_from_id+' select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
        }

        var handleForm = function () {
            var options1 = {
                url: 'index.php?mod=processor&con=ProductInfo&act=goodsSreach',
                error:function ()
                {
                    util.timeout('#product_has_goods_search');
                },
                beforeSubmit:function(frm,jq,op){
                    return util.lock(serach_from_id);
                    //$('body').modalmanager('loading');
                },
                success: function(data) {
                    $('#'+serach_from_id+' :submit').removeAttr('disabled');
                    $('body').modalmanager('removeLoading');
                    $('#product_has_goods_result_list').empty().append(data)
                }
            };

            $('#product_has_goods_search').ajaxForm(options1);
        }

        var initData = function(){
            $('#'+serach_from_id+' :reset').on('click',function(){
                $('#'+serach_from_id+' select').select2("val","");
            });
        }

        return {
            init:function(){
                initElements();
                handleForm();
                initData();
            }
        }
    }();
    obj.init();


	$('#product_has_goods_cannel_bc').click(function(){
		var g_id = $('#product_has_goods_result_list input[name="select_goods_id"]').val();
		if(!g_id){
			util.xalert('请选择商品');
			return false;
		}
		bootbox.dialog({  
			message: "确定不需布产？",  
			title: "提示",  
			buttons: {  
				Cancel: {  
					label: "取消",  
					className: "btn-default"
				},
				Confirm: {  
					label: "确定",  
					className: "btn-primary",  
					callback: function () {
						   var bc_id = $('#product_has_goods_search input[name="bc_id"]').val();
							var order_gs_id = $('#product_has_goods_search input[name="order_gs_id"]').val();
							var order_sn = $('#product_has_goods_search input[name="order_sn"]').val();
							var url = 'index.php?mod=processor&con=ProductInfo&act=cannelBC';
							var data = {'bc_id':bc_id,'goods_id':g_id,'order_gs_id':order_gs_id , 'order_sn':order_sn};
							$.post(url,data,function(e){
								if (e.success == 1){
									util.xalert(e.info,function(){
										util.retrieveReload();
										//util.closeTab();
									});
								}else{
									util.xalert(e.error);return;
								}
							});
					}//end callback
				} 
			}  
	  });		
			
	
	});

	//现货组合镶嵌点击事件
	$('#product_has_goods_combinexq').click(function(){
		var g_id = $('#product_has_goods_result_list input[name="select_goods_id"]').val();
		if(!g_id){
			util.xalert('请选择商品');
			return;
		}
		var dialog_url = 'index.php?mod=processor&con=ProductInfo&act=combineXQ&goods_id='+g_id;//util.retrieveEdit(this);
		$(this).attr('data-url',dialog_url);
		util.retrieveEdit(this);
			
		/*var bc_id = $('#product_has_goods_search input[name="bc_id"]').val();
			var order_gs_id = $('#product_has_goods_search input[name="order_gs_id"]').val();
			var order_sn = $('#product_has_goods_search input[name="order_sn"]').val();
			
			var data = {'bc_id':bc_id,'goods_id':g_id,'order_gs_id':order_gs_id,'order_sn':order_sn};
			$.post(url,data,function(e){
				if (e.success == 1){
					util.xalert(e.info,function(){
						util.retrieveReload();
						//util.closeTab();
					});
				}else{
					util.xalert(e.error);return;
				}
			});*/
	
	});



});