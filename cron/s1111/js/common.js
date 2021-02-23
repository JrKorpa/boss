// JavaScript Document
function showtbl(store)
{
	//var aa = $(store).parent().parent().children('div.aa').html();
	//var tb1 = ['h','b','k'];
	
	//包含一个个元素的box
	var line = $(store).parent().parent();
	
	line.children('div.type2').show();
	
	var productline = line.children('.typebox').children('select[name=productline]').val();
	var producttype = line.children('.typebox').children('select[name=producttype]').val();
	
	
	
	var dzjz = '指圈大小：<input type="text" name="zqh" /><br/>';
		dzjz += '刻字内容：<input type="text" name="kznr" /><br/>';
	
	
	//如果是黄金，铂金和k金
	if( productline=='h' || productline=='b' || productline =='k' )
	{
		line.children('div.type2').children('div.qzjz').html('');
		line.children('div.type2').hide();
		
		line.children('div.type1').children('div.dzjz').html('');
		line.children('div.type1').show();
		
	}else if(productline=='dz'){ //如果是钻石单钻
		
		line.children('div.type1').children('div.dzjz').html('');
		if(producttype =='戒指')
		{
			line.children('div.type1').children('div.dzjz').append(dzjz);
		}
		line.children('div.type2').hide();
		line.children('div.type1').show();
	}else if(productline=='qz')
	{
		line.children('div.type2').children('div.qzjz').html('');
		if(producttype == '戒指')
		{
			//line.children('div.type2').children('div.qzjz').show();
			line.children('div.type2').children('div.qzjz').append(dzjz);	
		}
		line.children('div.type1').hide();
		line.children('div.type2').show();
		
	}
	else if(productline='p')
	{
		alert('不用抓取');
		line.children('div.type1').hide();
		line.children('div.type2').hide();
	}
	
}


function getbz(line)
{
	//把整个分块的div对象传过来
	var productline = line.children('div.typebox').children('select[name=productline]').val();
	var producttype = line.children('div.typebox').children('select[name=producttype]').val();
	
	
	var name = line.children('div.name').children('select[name=kf_name]').val();
	//获取控件的值  都会有的控件  type1的
	if(productline == 'qz')
	{
		var goods_sn = line.children('div.type2').children('input[name=goods_sn]').val();
		var remark = line.children('div.type2').children('textarea[name=remark]').val();
	}else{
		var goods_sn = line.children('div.type1').children('input[name=goods_sn]').val();
		var remark = line.children('div.type1').children('textarea[name=remark]').val();
	}
	
	//如果是空就为无
	if(javaTrim (name)=="")
	{
		name='无';
	}
	if(javaTrim (goods_sn)=="")
	{
		goods_sn='无';
	}
	if(javaTrim (remark)=="")
	{
		remark='无';
	}
	//如果是黄金，铂金和k金
	var info =  '';
	if(productline=='h' || productline=='b' || productline =='k')
	{
		//获取客户姓名,获取赠品款号,获取订单备注
		info = name+','+goods_sn+','+remark;
		
	}else if(productline=='dz')
	{
		info = name+',';
		if(producttype == '戒指')
		{
			//获取指圈大小和刻字内容
			var zqh = line.children('div.type1').children('div.dzjz').children('input[name=zqh]').val();
			var kznr = line.children('div.type1').children('div.dzjz').children('input[name=kznr]').val();
			
			if(javaTrim (zqh)=="")
			{
				zqh ='无';
			}
			if(javaTrim (kznr)=="")
			{
				kznr='无';
			}
			info += zqh+','+kznr+',';
		}
		info += goods_sn+','+remark;
	}else if(productline=='qz')
	{
		//获取群钻都拥有的信息
		var caizhi = line.children('div.type2').children('select[name=caizhi]').val();
		var caizhi_ys = line.children('div.type2').children('select[name=caizhi_ys]').val();
		var zuzuan_dx = line.children('div.type2').children('input[name=zuzuan_dx]').val();
		var fuzuan_dx = line.children('div.type2').children('input[name=fuzuan_dx]').val();
		var fuzuan_ls = line.children('div.type2').children('input[name=fuzuan_ls]').val();
		var stone_color = line.children('div.type2').children('select[name=stone_color]').val();
		var jingdu = line.children('div.type2').children('select[name=jingdu]').val();
		
		if(javaTrim (caizhi)=="")
		{
			caizhi ='无';
		}
		if(javaTrim (caizhi_ys)=="")
		{
			caizhi_ys='无';
		}
		if(javaTrim (zuzuan_dx)=="")
		{
			zuzuan_dx ='无';
		}
		if(javaTrim (fuzuan_dx)=="")
		{
			fuzuan_dx='无';
		}
		if(javaTrim(fuzuan_ls)=="")
		{
			fuzuan_ls="无";
		}
		if(javaTrim (stone_color)=="")
		{
			stone_color ='无';
		}
		if(javaTrim (jingdu)=="")
		{
			jingdu='无';
		}
		info += name+',';
		if(producttype=='戒指')
		{
			//获取指圈大小和刻字内容
			var zqh = line.children('div.type2').children('div.qzjz').children('input[name=zqh]').val();
			var kznr = line.children('div.type2').children('div.qzjz').children('input[name=kznr]').val();
			if(javaTrim (zqh)=="")
			{
				zqh='无';
			}
			if(javaTrim (kznr)=="")
			{
				kznr='无';
			}
			info += zqh+','+kznr+',';
		}
		info += caizhi+','+caizhi_ys+','+zuzuan_dx+'ct,'+fuzuan_dx+'ct/'+fuzuan_ls+'p,'+stone_color+','+jingdu+','+goods_sn+','+remark;
	}else if(productline == 'p')
	{
		info = '';
	}
	return info;
}


function createmark()
{
	var $elements = $('.line');
	
	var outordersn = $('#out_order_sn').val();
	if(outordersn =='')
	{
		$("#remarkbox").html('<font style="color:red">请输入淘宝订单以便回写备注</font>');
		return false;
	}
	//var len = $elements.length;
	//alert(line);
	var allinfo='';
	$elements.each(function(){
		var linenow = $(this);
		//获取产品线和产品分类的值
		//var productline = linenow.children('div.typebox').children('select[name=productline]').val();
		//var producttype = linenow.children('div.typebox').children('select[name=producttype]').val();
		allinfo += getbz(linenow)+',KLBZ';
		//根据他们的值来确定需要哪些备注的字段
		//alert($this.prop('tagName'));
	});
	
	$.ajax({
		type:"POST",
		url : "updatetaobaomemo.php",
		data:{'orderid':outordersn,'info':allinfo},
		success:function(data)
		{
			if(data==1)
			{
				info = '淘宝订单ID:'+outordersn+'将备注修改为'+allinfo;
				$("#remarkbox").html(info);
			}else{
				$("#remarkbox").html('<font style="color:red;">'+data+'</font>');	
			}
		}
	})
	
	//$("#remarkbox").html(allinfo);
	
	/*
	
	var kfname = $("#kf_name").val();
	var goods_sn = $("#goods_sn").val();
	var remark = $("#remark").val();
	if(kfname == '')
	{
		alert('客服姓名不能为空');
		return ;
	}else if(goods_sn == '')
	{
		alert('商品款号不能为空');
		return;
	}else if(remark == '')
	{
		remark = '无';
	}
	
	var info = kfname;
	var productline = $("#productline").val(); //产品线
	var producttype = $("#producttype").val(); //产品类型
	if(productline =='dz' && producttype=='戒指')
	{
		info += ','+$("#zqh").val();
		info += ','+$("#kznr").val();
	}
	info += ','+goods_sn+','+remark;
	$("#remarkbox").html(info);
	*/
}

function addtable()
{
	//alert('我日追加商品啊');
	var html = $(".line").html();
	var lineinfo = '<div class="line">'+html+'</div>';
	$("#allline").append(lineinfo);
	
	var index = $(".line").length-1;
	console.log($(".line")[index]);
	var mm = $(".line")[index];
	var productline = $(mm).children('.typebox').children('select[name=productline]');
	var producttype = $(mm).children('.typebox').children('select[name=producttype]');
	showtbl($(productline));
	//alert(name);
}
function javaTrim(str) {
     for (var i=0; (str.charAt(i)==' ') && i<str.length; i++);
     if (i == str.length) return ''; //whole string is space
     var newstr = str.substr(i);
     for (var i=newstr.length-1; newstr.charAt(i)==' ' && i>=0; i--);
     newstr = newstr.substr(0,i+1);
     return newstr;
}


