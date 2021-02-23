function developer_gen(o)
{
	var mod_name = $.trim($('#developer select[name="mod_name"]').val());
	var conn_flag = parseInt($('#developer select[name="conn_flag"]').val());
	var table_name = $.trim($('#developer select[name="table_name"]').val());
	var db_name = $.trim($('#developer input[name="db_name"]').val());
	var pri_key = $('#developer input[name="pri_key"]').val();
	var con_name = $.trim($('#developer input[name="con_name"]').val());
	var con_type = parseInt($('#developer select[name="con_type"]').val());

	var detail_name = $('#developer select[name="detail_name"]').val();
	var foreign_key = $('#developer select[name="foreign_key"]').val();

	if (mod_name.length==0)
	{
		util.xalert("项目名称不能为空！");
		return ;
	}

	if (!conn_flag)
	{
		util.xalert("数据库连接不能为空！");
		return ;
	}

	if (table_name.length==0)
	{
		util.xalert("数据表名不能为空！");
		return ;
	}

	if (db_name.length==0)
	{
		util.xalert("数据库名称不能为空！");
		return ;
	}

	if (pri_key.length==0)
	{
		util.xalert("数据表主键不能为空！");
		return ;
	}

	if (con_name.length==0)
	{
		util.xalert("文件名不能为空！");
		return ;
	}

	if (!con_type)
	{
		util.xalert("请选择对象类型！");
		return ;
	}

	if (con_type==2 && !detail_name)
	{
		util.xalert("请选择明细对象表！");
		return ;
	}

	if (con_type==3 && !foreign_key)
	{
		util.xalert("明细表外键不能为空！");
		return ;
	}

	var config = {
		mod_name:mod_name,
		conn_flag:conn_flag,
		table_name:table_name,
		db_name:db_name,
		pri_key:pri_key,
		con_name:con_name,
		con_type:con_type,
		detail_name:detail_name,
		foreign_key:foreign_key
	};

	$.post($(o).attr('data-url'),config,function(data){
		if (data.success==1)
		{
			window.location.href = data.url;
		}
		else
		{
			util.error(data);
		}
	});
}

$import("public/js/select2/select2.min.js",function(){
	var obj = function(){
		var initElements = function(){
			$('#developer select[name="mod_name"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				if (_t)
				{
					$('#developer select[name="conn_flag"]').attr('readOnly',false);
				}
				else
				{
					$('#developer select[name="conn_flag"]').val('').change().attr('readOnly',true);
				}
			});

			$('#developer select[name="conn_flag"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				$('#developer select[name="table_name"]').empty();
				$('#developer select[name="table_name"]').append('<option value=""></option>');
				var _t = $(this).select2('val');
				if (_t)
				{
					var db_name = $(this).find("option:selected").attr('db_name');
					$('#developer input[name="db_name"]').val(db_name);
					$.post('index.php?mod=management&con=developer&act=getTables',{conn_flag:_t},function(data){
						$('#developer select[name="table_name"]').append(data).attr('readOnly',false);
					});
				}
				else
				{
					$('#developer select[name="table_name"]').val('').change().attr('readOnly',true);
					$('#developer input[name="con_name"]').val('');
					$('#developer input[name="db_name"]').val('');
				}

			}).attr('readOnly',true);

			$('#developer select[name="table_name"]').select2({
				placeholder: "请先选择数据库连接",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).select2('val');
				if (_t)
				{
					var db_name = $('#developer input[name="db_name"]').val();
					var conn_flag = $('#developer select[name="conn_flag"]').select2('val');
					$.post('index.php?mod=management&con=developer&act=getPk',{table_name:_t,db_name:db_name,conn_flag:conn_flag},function(data){
						$('#developer input[name="pri_key"]').val(data);
					});
                                        debugger;
					var tt = _t.split('_');
					var tmp ='';
					for (var x in tt)
					{
                                                if(typeof tt[x]!='function'){
                                                        tmp +=tt[x].substr(0,1).toUpperCase()+tt[x].substr(1)+"";              
                                                }
					}
					$('#developer input[name="con_name"]').val(tmp);
					$('#developer select[name="con_type"]').val('').attr('readonly',false).change();
				}
				else
				{
					$('#developer input[name="con_name"]').val('');
					$('#developer input[name="pri_key"]').val('');
					$('#developer select[name="con_type"]').val('').attr('readonly',true).change();
				}
			}).attr('readOnly',true);

			$('#developer select[name="con_type"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
				var _t = $(this).val();
				if (_t)
				{
					if (_t==2)
					{
						$('#developer select[name="detail_name"]').empty();
						$('#developer select[name="detail_name"]').append('<option value=""></option>');
						var conn_flag = $('#developer select[name="conn_flag"]').select2('val');
						$.post('index.php?mod=management&con=developer&act=getTables',{conn_flag:conn_flag},function(data){
							$('#developer select[name="detail_name"]').append(data).attr('readOnly',false);
						});
						//$('#developer textarea[name="detail_name"]').removeAttr('readOnly');
					}
					else
					{
						$('#developer select[name="detail_name"]').empty().change().attr('readOnly',true);
					}
					if (_t==3)
					{
						$('#developer select[name="detail_name"]').empty().change().attr('readOnly',true);
						$('#developer select[name="foreign_key"]').val('').attr('readOnly',false);
						var db_name = $(this).find("option:selected").attr('db_name');
						var conn_flag = $('#developer select[name="conn_flag"]').select2('val');
						var table_name = $('#developer select[name="table_name"]').select2('val');
						$('#developer select[name="foreign_key"]').empty();
						$('#developer select[name="foreign_key"]').append('<option value=""></option>');

						$.post('index.php?mod=management&con=developer&act=getFileds',{conn:conn_flag,db_name:db_name,table_name:table_name},function(data){
							$('#developer select[name="foreign_key"]').append(data);
						});
					}
					else
					{
						$('#developer select[name="foreign_key"]').val('').change().attr('readOnly',true);
					}
				}
				else
				{
					$('#developer select[name="detail_name"]').empty().change().attr('readOnly',true);
					$('#developer select[name="foreign_key"]').val('').change().attr('readOnly',true);
				}
			}).attr('readOnly',true);

			$('#developer select[name="detail_name"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			}).attr('readOnly',true);

			//

			$('#developer select[name="foreign_key"]').select2({
				placeholder: "请选择",
				allowClear: true
			}).change(function(e){
				$(this).valid();
			}).attr('readOnly',true);
		}
	
		return {
			init:function(){
				initElements();
			}
		}
	}();
	obj.init();
});