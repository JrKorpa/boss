//保存盘点单 （判断当前仓库是否生成未审核或取消的盘点单，如果有未审核 或 取消的单据，根据返回的bill_id跳到指定的单据上）
function createPandian(obj){
	var postDate = {
		'warehouse':$('#warehouse_bill_info_w_info select[name=warehouse]').val(),
		'bill_note':$('#warehouse_bill_info_w_info input[name=bill_note]').val(),
		'bill_no':$('#warehouse_bill_info_w_info input[name=bill_no]').val(),
		'create_time':$('#warehouse_bill_info_w_info input[name=create_time]').val(),
		'bill_status':$('#warehouse_bill_info_w_info input[name=bill_status]').val(),
		'create_user':$('#warehouse_bill_info_w_info input[name=create_user]').val(),
		'check_user':$('#warehouse_bill_info_w_info input[name=check_user]').val(),
		'goods_num':$('#warehouse_bill_info_w_info input[name=goods_num]').val(),
		'chengbenjia':$('#warehouse_bill_info_w_info input[name=chengbenjia]').val(),
	};
	var url = $(obj).attr('data-url');
	$.post(url, postDate , function(res){
		if(res.success == 1){
			util.xalert(
				res.error,
				function(){
				//新标签盘点
					var url = $(obj).attr('pandian-url');
					var params = util.parseUrl(url);
					var prefix = params['con'].toLowerCase()+"_xx";
					var title = $(obj).attr('data-title');

					//生成的盘点单ID
					var bill_id = res.bill_id;

					if (!title)
					{
						title=params['con'];
					}
					//不能同时打开两个添加页
					var flag = false;
					$('#nva-tab li').each(function(){
						var href = $(this).children('a').attr('href');
						href = href.split('-');
						href.pop();
						href = href.join('_').substr(1);
						if (href==prefix)
						{
							flag=true;
							var that = this;
							bootbox.confirm({
								buttons: {
									confirm: {
										label: '前往查看'
									},
									cancel: {
										label: '点错了'
									}
								},
								closeButton: false,
								message: "发现同类数据的页签已经打开。",
								callback: function(result) {
									if (result == true) {
										$(that).children('a').trigger("click");
									}
								},
								title: "提示信息",
							});
							return false;
						}
					});
					if (!flag)
					{
						var id = prefix+"-0";
						new_tab(id, title,url+'&bill_id='+bill_id);
						util.retrieveReload();
					}

				}
			);
		}else{

			if(res.bill_id != 0){	//同一个仓库已经有了盘点单，不生成新的盘点单，跳往已经生成的盘点单
				util.xalert(
					res.error,
					function(){
					//新标签盘点
						var url = $(obj).attr('pandian-url');
						var params = util.parseUrl(url);
						var prefix = params['con'].toLowerCase()+"_xx";
						var title = $(obj).attr('data-title');

						//生成的盘点单ID
						var bill_id = res.bill_id;

						if (!title)
						{
							title=params['con'];
						}
						//不能同时打开两个添加页
						var flag = false;
						$('#nva-tab li').each(function(){
							var href = $(this).children('a').attr('href');
							href = href.split('-');
							href.pop();
							href = href.join('_').substr(1);
							if (href==prefix)
							{
								flag=true;
								var that = this;
								bootbox.confirm({
									buttons: {
										confirm: {
											label: '前往查看'
										},
										cancel: {
											label: '点错了'
										}
									},
									closeButton: false,
									message: "发现同类数据的页签已经打开。",
									callback: function(result) {
										if (result == true) {
											$(that).children('a').trigger("click");
										}
									},
									title: "提示信息",
								});
								return false;
							}
						});
						if (!flag)
						{
							var id = prefix+"-0";
							new_tab(id, title,url+'&bill_id='+bill_id);
							util.retrieveReload();
						}

					}
				);
			}else{
				util.error(res);//错误处理
			}
		}
	})
}

//新建盘点单
function newPandian(obj){
	var newpan = $(obj).attr('data-url');
	if(newpan == ''){
		//表单置空
		$('#warehouse_bill_info_w_info input[name=bill_no]').val('');
		$('#warehouse_bill_info_w_info input[name=bill_status]').val('');
		$('#warehouse_bill_info_w_info input[name=create_time]').val('');
		$('#warehouse_bill_info_w_info input[name=create_user]').val('');
		$('#warehouse_bill_info_w_info input[name=goods_num]').val('');
		$('#warehouse_bill_info_w_info input[name=chengbenjia]').val('');
		$('#warehouse_bill_info_w_info input[name=bill_note]').val('');

		$('#warehouse_name').remove();
		$('#warehouse_list').css('display', 'block');
		$('#warehouse_bill_info_w_info select[name=warehouse]').removeAttr('disabled');

		$('#warehouse_bill_info_w_info input[name=bill_note]').removeAttr('disabled');

		$('#jxpd').remove();

		var url = 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=CreatePandian';
		$(obj).attr('data-url', url).removeClass('blue').addClass('red');
		var txt = $(obj).html('保存 <i class="fa fa-plus"></i>');
	}else{
		createPandian(obj);
	}
	//变更按钮状态
}


//继续盘点
function ShowBoxPandian(obj){
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";
	var title = $(obj).attr('data-title');

	//生成的盘点单ID
	var bill_id = $('#warehouse_bill_info_w_info input[name=id]').val();

	if (!title)
	{
		title=params['con'];
	}
	//不能同时打开两个添加页
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			var that = this;
			bootbox.confirm({
				buttons: {
					confirm: {
						label: '前往查看'
					},
					cancel: {
						label: '点错了'
					}
				},
				closeButton: false,
				message: "发现同类数据的页签已经打开。",
				callback: function(result) {
					if (result == true) {
						$(that).children('a').trigger("click");
					}
				},
				title: "提示信息",
			});
			return false;
		}
	});
	if (!flag)
	{
		var id = prefix+"-0";
		new_tab(id, title,url+'&bill_id='+bill_id);
	}
}


//切换盘点单
function qieBill(obj){
	var p_url = $(obj).attr('data-url');
	var url = p_url+$('#warehouse_bill_info_w_info input[name=id]').val();
	$('body').modalmanager('loading');//进度条和遮罩
	$.get(url , '' , function(res){
		if(res.success == 1){
			$('#warehouse_bill_info_w_info input[name=bill_no]').val(res.bill_no);
			$('#warehouse_bill_info_w_info input[name=create_time]').val(res.create_time);
			$('#warehouse_bill_info_w_info input[name=bill_status]').val(res.bill_status);
			// $('#warehouse_bill_info_w_info input[name=bill_status]').val(res.status);
			$('#warehouse_bill_info_w_info input[name=create_user]').val(res.create_user);
			$('#warehouse_bill_info_w_info input[name=goods_num]').val(res.goods_num);
			$('#warehouse_bill_info_w_info input[name=chengbenjia]').val(res.chengbenjia);
			$('#warehouse_bill_info_w_info input[name=bill_note]').val(res.bill_note);
			$('#warehouse_name  input').val(res.to_warehouse_name);
			$('#warehouse_bill_info_w_info input[name=id]').val(res.id);

			$('.modal-scrollable').trigger('click');// 关闭遮罩
		}else{
			util.error(res);//错误处理
		}
	})
}


$import(['public/js/select2/select2.min.js'] , function(){
	var info_form_id = 'warehouse_bill_info_w_info';

	var obj = function(){
		var initElements = function(){
			$('#'+info_form_id+' select').select2({
				placeholder: "请选择",
				allowClear: true,
			//	minimumInputLength: 2
			}).change(function(e){
				$(this).valid();
			});
		};

		return {
			init:function(){
				initElements();//处理表单元素
			}
		}
	}();
	obj.init();
});