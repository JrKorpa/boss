/**
 * @param {String}  errorMessage   错误信息
 * @param {String}  scriptURI      出错的文件
 * @param {Long}    lineNumber     出错代码的行号
 * @param {Long}    columnNumber   出错代码的列号
 * @param {Object}  errorObj       错误的详细信息，Anything
 */
window.onerror = function(errorMessage, scriptURI, lineNumber,columnNumber,errorObj) {
   console.log("错误信息：" , errorMessage);
   console.log("出错文件：" , scriptURI);
   console.log("出错行号：" , lineNumber);
   console.log("出错列号：" , columnNumber);
   console.log("错误详情：" , errorObj);
}


$.fn.hasAttr = function(name) { 
 return this.attr(name) !== undefined;
};



var util={};
/*带前置下划线的函数为内部处理函数，原则上不要直接调用*/
util._pop = function(url,jsondata){
	$('body').modalmanager('loading');
	setTimeout(function(){
		$.post(url,jsondata,function(data){
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
			if (data.redir_url) {
				bootbox.confirm({
		  			size : 'medium',
		  			title: "提示信息",
		  			message : '您的账号登录信息已过期或网络间断，系统将转向首页',
		  			buttons: {
		  				confirm: { label: '知道了', className: 'btn-primary'},
		  				cancel: { label: '不', className: 'btn-default'}
		  			},
		  			callback : function(res) {
		  				if(res) {
		  					location.href = "/index.php";
		  				}
		  			}
		         });
				return;
			}
			$('.modal .modal-body').html(data.content);
			//$('.modal .modal-footer').hide();
			//$('.modal').modal({backdrop: 'static', keyboard: false});
			$('.modal').modal("toggle");
		});
	}, 200);
}

util._load = function(obj,type){
	if (type)
	{
		var url = util.getItem("orl");
	}
	else
	{
		var url = util.getItem("url");
	}
	util._sync(url,$(obj).parent().parent().siblings("[id]"),type);
}

util._sync = function(url,el,type){
	App.blockUI({target: el, iconOnly: true});
	var config = {
		type: "GET",
		cache: false,
		url: url,
		dataType: "html",
		success: function(res)
		{
			App.unblockUI(el);
			el.html(res);
		},
		error: function(xhr, ajaxOptions, thrownError)
		{
			App.unblockUI(el);
			var msg = '加载错误.请检查网络连接后重试！';
			$('.modal-scrollable').trigger('click');
			util.xalert(msg);
			return;
		}
	};
	if (type)
	{
		util.deleteItem('data');
		util.setItem("url",util.getItem("orl"));
	}
        else
        {
                //同步
                data = util.getItem('data');
                if (data != '{}' && data!=null)
                {
                        data = data.replace(/\\n/g,' ');//add yangfuyou url不能还有回车符
                        data = eval('(' + data + ')');
                        config.data = data;
                }
        }

	$.ajax(config);
}



//----过程函数 start----//
//网络超时
//直接调用函数
util.timeout = function(info_form_id){
	$('#'+info_form_id+' :submit').removeAttr('disabled');
	$('.modal-scrollable').trigger('click');
	util.xalert("系统长时间无响应，请检查网络链接或本次登录已超时");
}

//异常
util.error = function(data){
	$('body').modalmanager('removeLoading');//关闭进度条
	util.xalert(data.error ? data.error : (data ? data :'程序异常'));
}

//表单锁定
util.lock = function(info_form_id){
	$('body').modalmanager('loading');//进度条和遮罩
	if ($('#'+info_form_id+' :submit').attr('disabled'))
	{
		util.xalert('已经提交过了，请耐心等待');
		return false;
	}
	else
	{
		$('#'+info_form_id+' :submit').attr('disabled','disabled');
		return true;			
	}
}

//弹出窗口
util.xalert = function(msg,func){
	var config = {   
		message: msg,
		buttons: {  
				   ok: {  
						label: '确定'  
					}  
				},
		animate: true, 
		closeButton: false,
		title: "提示信息" 
	}
	if (typeof func=='function')
	{
		config.callback = func;
	}
	bootbox.alert(config);
}
//收起明细
util.closeDetail = function(flag){
	var obj = $('#'+getID()+' .portlet>.portlet-title');
	for (var x in obj)
	{
		if (parseInt(x))
		{
			if (flag)
			{
				if (parseInt(x)==1)
				{
					continue;
				}
			}
			if($(obj[x]).parent().parent().hasClass('portlet-body')){//明细的搜索框默认收起
				continue;
			}
			$(obj[x]).click();
		}
	}
}

//刷新列表页签 查看页编辑、删除成功时使用
util.syncTab=function(tabid){
	var obj =$('#tab-'+tabid).find('.flip-scroll');
	if (obj.length)
	{
		var t = $('#tab-'+tabid).find('.flip-scroll').parent().hasAttr('id');
		if (t)
		{
			obj = $('#tab-'+tabid).find('.flip-scroll').parent();
		}
	}
	else
	{
		obj =$('#tab-'+tabid).find('.table-scrollable');
		var tt = $('#tab-'+tabid).find('.table-scrollable').parent().hasAttr('id');
		if (tt)
		{
			obj = $('#tab-'+tabid).find('.table-scrollable').parent();
		}
	}
	util._sync(util.getItem("url",'tab-'+tabid),obj,false);
}

//刷新当前页签 --废弃 使用 util.retrieveReload()
//util.refresh = function(id,title,url){
//	$('a[href="#'+getID()+'"]').parent().children('i').trigger('click');
//	new_tab(id,title,url);
//}

//存储
util.setItem = function(key,val){
	window.LS.set(getID()+":"+key,val);
}

//读取
util.getItem  = function(key,tab_id){
	if (tab_id)
	{
		return window.LS.get(tab_id+":"+key);
	}
	return window.LS.get(getID()+":"+key);
}

//删除
util.deleteItem = function(key,tab_id){
	if (tab_id)
	{
		window.LS.remove(tab_id+":"+key);
	}
	window.LS.remove(getID()+":"+key);
}

//----过程函数 end----//





//------通用操作 start--------//

//无条件渲染页面
util.pop = function(obj){
	util._pop($(obj).attr('data-url'));
}

//选中行后弹窗：适用于编辑、查看、相关对象处理
util.pop2 = function(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	util._pop($(obj).attr('data-url'),{id:tObj[0].getAttribute("data-id").split('_').pop()});
}

// 获取
util.row_data_id = function(obj) {
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}

	return tObj[0].getAttribute("data-id").split('_').pop();
}


//自定义按钮操作
util.cust = function(obj,callback){
	if (typeof callback=='function')
	{
		callback(obj);
	}
	else
	{
		util.xalert("自定义按钮的回调函数未定义");
		return false;
	}
}



//关闭页签
util.closeTab = function(){
	$('a[href="#'+getID()+'"]').parent().children('i').trigger('click');
}

//分页
util.page = function(url,_key,parm){
	if (typeof _key=='undefined'){var _key='';}
	if (typeof parm=='undefined'){var parm={};}
	var listID = util.getItem("listDIV"+_key);
        var el = $("#"+listID);
	var config = {
		url:url,
		type:"POST",
		success:function(data){
//			$('body').modalmanager('removeLoading');
                        App.unblockUI(el);
			//$('.modal-scrollable').trigger('click');
			if (!data)
			{
				util.xalert("程序异常");
				return false;
			}
			else
			{
				$("#"+listID).html(data);
			};
		},
		error:function(){
			util.error("请求超时");
			return false;
		}
	}
	//add by zhangruiying URL过长采用POST提交搜索条件
	if(typeof parm!='undefined')
	{
		config.data=parm;
	}
	//add end 
	util.setItem("url"+_key,url);
        App.blockUI({target:el, iconOnly: true});
	$.ajax(config);
}

//搜索
util.search = function(_key){
	if (typeof _key=='undefined')
	{
		var _key='';
	}
	else
	{
		_key=parseInt(_key);
	}
	var options1 = {
		url:util.getItem("orl"+_key),
		target:'#'+util.getItem("listDIV"+_key),
		error:function ()
		{
			util.error("请求超时");
			return false;
		},
		beforeSubmit:function(frm,jq,op){
			$('body').modalmanager('loading');
			var jsondata = {};
			var _url = '';
                        var flag=false;
                        var tmp = [];
                        var name='';
			$(frm).each(function(i,e){ 
                                if(e.name.indexOf('[]')>0)
                                {
                                        flag = true;
                                        var tt = e.name.substr(0,e.name.length-2);
                                        if(name==''){
                                                name = tt;
                                                tmp.push(e.value);
                                        }
                                        else if(name==tt)
                                        {
                                                tmp.push(e.value);        
                                        }
                                        else
                                        {
                                                jsondata[name]=tmp;
                                                tmp=[];
                                                name=tt;
                                                tmp.push(e.value);
                                        }      
                                }
                                else
                                {
                                        if(flag){
                                                flag=false;
                                                jsondata[name]=tmp;
                                                tmp=[];
                                        }
                                        jsondata[e.name] = e.value;
                                }
				_url+="&"+e.name+"="+e.value;
			});

			util.setItem("data"+_key,JSON.stringify(jsondata));
			_url = _url.replace(/\n/g,' ');
			util.setItem("url"+_key,util.getItem("orl"+_key)+_url);
		},
		success: function(data) {
			$('body').modalmanager('removeLoading');
			//$('.modal-scrollable').trigger('click');
			util.closeForm(util.getItem("formID"+_key));
		}
	};

	$("#"+util.getItem("formID"+_key)).ajaxForm(options1);
}
//搜索框，不在关闭
util.search_open = function(_key){
	if (typeof _key=='undefined')
	{
		var _key='';
	}
	else
	{
		_key=parseInt(_key);
	}
	var options1 = {
		url:util.getItem("orl"+_key),
		target:'#'+util.getItem("listDIV"+_key),
		error:function ()
		{
			util.error("请求超时");
			return false;
		},
		beforeSubmit:function(frm,jq,op){
			$('body').modalmanager('loading');
                        var jsondata = {};
			var _url = '';
                        var flag=false;
                        var tmp = [];
                        var name='';
			$(frm).each(function(i,e){                               
                                if(e.name.indexOf('[]')>0)
                                {
                                        flag = true;
                                        var tt = e.name.substr(0,e.name.length-2);
                                        if(name==''){
                                                name = tt;
                                                tmp.push(e.value);
                                        }
                                        else if(name==tt)
                                        {
                                                tmp.push(e.value);        
                                        }
                                        else
                                        {
                                                jsondata[name]=tmp;
                                                tmp=[];
                                                name=tt;
                                                tmp.push(e.value);
                                        }      
                                }
                                else
                                {
                                        if(flag){
                                                flag=false;
                                                jsondata[name]=tmp;
                                                tmp=[];
                                        }
                                        jsondata[e.name] = e.value;
                                }
				_url+="&"+e.name+"="+e.value;
			});

			util.setItem("data"+_key,JSON.stringify(jsondata));
			_url = _url.replace(/\n/g,' ');
			util.setItem("url"+_key,util.getItem("orl"+_key)+_url);
		},
		success: function(data) {
			$('body').modalmanager('removeLoading');
			//$('.modal-scrollable').trigger('click');
			//util.closeForm(util.getItem("formID"+_key));
		}
	};

	$("#"+util.getItem("formID"+_key)).ajaxForm(options1);
}

//表格划过点击变色
util.hover = function(){
	$(".table tbody tr").hover(function() {
		if (!$(this).hasClass('tab_click'))
		{
			$(this).addClass("tab_hover"); // 鼠标经过添加hover样式
		}
    }, function() {
		if (!$(this).hasClass('tab_click'))
		{
			$(this).removeClass("tab_hover"); // 鼠标离开移除hover样式
		}
    }).click(function(){
        $(this).addClass("tab_click").siblings().removeClass("tab_click");
	});
}

//隐藏搜索框
util.closeForm = function(frmID){
	if ($('#'+frmID).length>0)
	{
		if ($('#'+frmID).parent().prev().find(".collapse").length>0)
		{
			$('#'+frmID).parent().prev().click();
		}
	}
}

//验证密码强度
util.checkIntensity=function (pwd)
{
	var Mcolor = "#FFF",Lcolor = "#FFF",Hcolor = "#FFF";
	var m=0;

	var Modes = 0;
	for (i=0; i<pwd.length; i++)
	{
		var charType = 0;
		var t = pwd.charCodeAt(i);
		if (t>=48 && t <=57)
		{
			charType = 1;
		}
		else if (t>=65 && t <=90)
		{
			charType = 2;
		}
		else if (t>=97 && t <=122)
			charType = 4;
		else
			charType = 4;
		Modes |= charType;
	}

	for (i=0;i<4;i++)
	{
		if (Modes & 1) m++;
		Modes>>>=1;
	}

	if (pwd.length<=4)
	{
		m = 1;
	}

	switch(m)
	{
		case 1 :
			Lcolor = "2px solid red";
			Mcolor = Hcolor = "2px solid #DADADA";
			$("#paw_type").val(1);
			break;
		case 2 :
			Mcolor = "2px solid #f90";
			Lcolor = Hcolor = "2px solid #DADADA";
			$("#paw_type").val(2);
			break;
		case 3 :
			Hcolor = "2px solid #3c0";
			Lcolor = Mcolor = "2px solid #DADADA";
			$("#paw_type").val(3);
			break;
		case 4 :
			Hcolor = "2px solid #3c0";
			Lcolor = Mcolor = "2px solid #DADADA";
			$("#paw_type").val(4);
			break;
		default :
			Hcolor = Mcolor = Lcolor = "";
			break;
	}
	if (document.getElementById("pwd_lower"))
	{
		document.getElementById("pwd_lower").style.borderBottom  = Lcolor;
		document.getElementById("pwd_middle").style.borderBottom = Mcolor;
		document.getElementById("pwd_high").style.borderBottom   = Hcolor;
	}
}

//解析url
util.parseUrl = function(url){
	var params = [];
	var pattern = /(\w+)=(\w+)/ig;
	url.replace(pattern, function(a, b, c){
		params[b] = c;
	});
	return params;
}

//创建数据后跳转到编辑页的链接生成函数
util.buildEditTab = function(id,url,tab_id,label){
	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";
	if (typeof label=='undefined')
	{
		var label = params['con'].toLowerCase()	
	}
	//不能同时打开两
	var flag = false;
	$('#nva-tab li').each(function(){
		var href = $(this).children('a').attr('href');
		href = href.split('-');
		href.pop();
		href = href.join('_').substr(1);
		if (href==prefix)
		{
			flag=true;
			$(this).children('i').trigger('click');
			new_tab(params['con'].toLowerCase()+"_xx"+'-'+tab_id,'编辑:'+label,url+'&tab_id='+tab_id+'&id='+id);
			return false;
		}
	});
	if (!flag)
	{
		new_tab(params['con'].toLowerCase()+"_xx"+'-'+tab_id,'编辑:'+label,url+'&tab_id='+tab_id+'&id='+id);
	}
}
//------通用操作 end--------//



//------列表页操作 start--------//

//列表页特殊处理
util.confirm = function(obj){
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}

	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	var _name = $(obj).attr('name');
	if (!_name)
	{
		_name='';
	}

	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定"+_name+"?", 
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{id:objid},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
								util.sync(obj);									
							});
							return ;
						}
						else{
							util.error(data);
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

//选中行后新建页签
util.newTab = function(obj){
	//$('body').modalmanager('loading');
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var prefix = params['con'].toLowerCase();
		//不能同时打开两个详情页
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
						label: '确认' 
					},  
					cancel: {  
						label: '查看'  
					}  
				},
				closeButton:false,
				message: "发现同类数据的查看页已经打开。\r\n点确定将关闭同类查看页。\r\n点查看将激活同类查看页。",  
				callback: function(result) {  
					if (result == true) {
						setTimeout(function(){
							$(that).children('i').trigger('click');
							var id = prefix+"-"+_id;
							var title=tObj[0].getAttribute("data-title");
							if (title==null || $(obj).attr("use"))
							{
								title = $(obj).attr('data-title');
							}
							if ('undefined' == typeof title)
							{
								title = id;
							}
							url+="&id="+_id;

							new_tab(id,title,url);
						}, 0);
					}
					else if (result==false)
					{
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
		var id = prefix+"-"+_id;
		var title=tObj[0].getAttribute("data-title");
		if (title==null || $(obj).attr("use"))
		{
			title = $(obj).attr('data-title');
		}
		if ('undefined' == typeof title)
		{
			title = id;
		}
		url+="&id="+_id;

		new_tab(id,title,url);
	}
}

//刷新当前列表页
util.sync = function(obj){
	util._load(obj,false);
}

//刷新，初始化到首列表
util.reload = function(obj){
	util._load(obj,true);
}

//快捷操作
util.add = util.pop;
util.edit = util.pop2;
util.retrieve = util.pop2;
util.view = util.newTab;
util.del = util.confirm;
util.sort = util.pop;

//上移
util.moveup = function(obj){
	var url = $(obj).attr('data-url');
	if (!url)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("请传递操作请求地址！");
		return false;
	}
	var id = $(obj).attr('data-id');
	if (!id)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("请传递操作记录id");
		return false;
	}
	$.post(url,{id:id},function(data){
		if (typeof data !='object')
		{
			$('body').html(data);
		}
		if (data.success==1)
		{
			//util._sync(util.getItem("url"),$(obj).parent().parent().parent().parent().parent().siblings("[id]"),false);
			util._sync(util.getItem("url"),$(obj).parent().parent().parent().parent().parent().parent(),false);

		}
		else
		{
			util.error(data);
			return false;
		}
	});
}

//下移
util.movedown = function(obj){
	var url = $(obj).attr('data-url');
	if (!url)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("请传递操作请求地址！");
		return false;
	}
	var id = $(obj).attr('data-id');
	if (!id)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("请传递操作记录id");
		return false;
	}
	$.post(url,{id:id},function(data){
		if (typeof data !='object')
		{
			$('body').html(data);
		}
		if (data.success==1)
		{
			util._sync(util.getItem("url"),$(obj).parent().parent().parent().parent().parent().parent(),false);
		}
		else
		{
			util.error(data);
			return false;
		}
	});
}

//相关列表
util.relList = function(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var id='';
	var title='';
	$("#leftmenu li a").each(
		function(){
			if($(this).attr('data-url')==url){
				id=$(this).attr('data-id');
				title=$(this).text();
				return false;//each中 return ;//实现continue功能  return false;//实现break功能
			}
		}
	);
	var li = $("#nva-tab li").children('a[href="#'+id+'"]');
	if (li.length>0)
	{
		li.parent().remove();
  		$("#tab-content").children('div[id="'+id+'"]').remove();
	}
	url+="&id="+_id;
	new_tab(id,title,url);
	util.setItem("url",url);
}


//新页签添加
util.addNew = function(obj){
	var url = $(obj).attr('data-url');
	var listid = $(obj).attr('list-id');
	var params = util.parseUrl(url);
	// prefix 对比时，加上act的比较：
	var prefix = params['con'].toLowerCase() + "_" + params['act'].toLowerCase() + "_xx";
	var title = $(obj).attr('data-title');

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
		new_tab(id,title,url+'&tab_id='+listid);
	}
}

//新页签编辑
util.editNew = function(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}

	var id = tObj[0].getAttribute("data-id").split('_').pop();
	var url = $(obj).attr('data-url');
	var listid = $(obj).attr('list-id');

	var params = util.parseUrl(url);
	var prefix = params['con'].toLowerCase()+"_xx";
	var title = $(obj).attr('data-title');
	if (!title)
	{
		title=params['con'];
	}
	//不能同时打开两个编辑页
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
						label: '去看看' 
					},  
					cancel: {  
						label: '点错了'  
					}  
				},
				closeButton:false,
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
		var t_id = prefix+"-0";
		new_tab(t_id,'编辑:'+title,url+'&tab_id='+listid+'&id='+id);
	}
}
//------列表页操作 end--------//




//------查看页操作 start--------//

//查看页编辑
util.retrieveEdit = function(obj){
	var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
	util._pop($(obj).attr('data-url'),{_cls:1,id:_id,'tab_id':$(obj).attr("list-id")});
}

//查看页删除
util.retrieveDelete = function(obj){
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var objid = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
	var listid = $(obj).attr("list-id");

	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定删除?", 
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				$('body').modalmanager('loading');

				setTimeout(function(){
					$.post(url,{id:objid},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert('操作成功',function(){
								util.closeTab();
								util.syncTab(listid);							
							});						
						}
						else
						{
							util.error(data);
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

//查看页刷新当前页签
util.retrieveReload=function(obj){
	var url = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-url');//当前页签的url
	var el = $("#tab-content").children('div[id="'+getID()+'"]');
	var listid = $(obj).attr('list-id');
	if (url)
	{
        App.blockUI({target: el, iconOnly: true});
		$.ajax({
			type: "GET",
			cache: false,
			url: url,
			dataType: "html",
			success: function(res)
			{
				App.unblockUI(el);
				el.html(res);
				util.syncTab(listid);
			},
			error: function(xhr, ajaxOptions, thrownError)
			{
				App.unblockUI(el);
				var msg = '加载错误.请检查网络连接后重试！';
				util.xalert(msg);
				return false;
			}
		});
	}
}

//查看页特殊处理
util.retrieveConfirm = function(obj){
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var objid = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
	var _name = $(obj).attr('data-title');
	if (!_name)
	{
		_name='';
	}
	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定"+_name+"?", 
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{id:objid},function(data){
						
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){
								util.retrieveReload(obj);
							});							
						}
						else
						{
							util.error(data);
							if(data.hasOwnProperty("is_refresh"))
							{
								if(data.is_refresh==1)
								{
									util.retrieveReload(obj);
								}
							}
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

//------查看页操作 end--------//


//------明细操作 start--------//

//添加明细
util.addRel = function(o){
	var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
	util._pop($(o).attr('data-url'),{_id:_id});
}

//编辑明细
util.editRel=function(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}

	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	util._pop(url,{id:_id,'tab_id':$(obj).attr("list-id")});//tab-id是主记录的列表
}

//删除明细
util.deleteRel = function(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var title = $(obj).attr('data-title');
	if (!title)
	{
		title = '该明细';
	}

	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定删除"+title+"?", 
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{id:_id},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){					
								util.retrieveReload(obj);	
							});
						}
						else
						{
							util.error(data);
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

//刷新明细
util.relReload = function(o){
	var el = $(o).parent().parent().siblings('div[id]');
	if($(o).parent().parent().parent().find('.portlet .portlet-title').find("a.collapse").length>0){
		$(o).parent().parent().parent().find('.portlet .portlet-title').click();
	}
	var url = $(o).attr("data-url");
	var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
	url+='&_id='+_id;
	if (url) {
		App.blockUI({target: el, iconOnly: true});
		$.ajax({
			type: "GET",
			cache: false,
			url: url,
			dataType: "html",
			success: function(res)
			{
				App.unblockUI(el);
				el.html(res);
			},
			error: function(xhr, ajaxOptions, thrownError)
			{
				App.unblockUI(el);
				var msg = '加载错误.请检查网络连接后重试！';
				util.xalert(msg);
				return false;
			}
		});
	} else {
		// for demo purpose
		App.blockUI({target: el, iconOnly: true});
		window.setTimeout(function () {
			App.unblockUI(el);
		}, 1000);
	}
}

//明细特殊处理
util.relConfirm = function(obj){
	$('body').modalmanager('loading');
    var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	$('body').modalmanager('loading');
	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();
	var _name = $(obj).attr('data-title');
	if (!_name)
	{
		_name='';
	}
	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定"+_name+"?",
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{id:_id},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){								
								util.retrieveReload(obj);	
							});
						}
						else
						{
							util.error(data);
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

//明细排序
util.relSort = function(o){
	var _id = $("#nva-tab li").children('a[href="#'+getID()+'"]').siblings('i').attr('data-id').split('-').pop();
	util._pop($(o).attr('data-url'),{_id:_id});
}
//------明细操作 end--------//

//列表页多选进行批量操作
util.check = function(listDIV){
	$('#'+listDIV+'>table>thead>tr>th:first input:checkbox').click(function(){
		$(this).parent().parent().parent().siblings().find('input:checkbox[name="_ids[]"]').attr('checked',this.checked);
	});
}

//批量选取并弹框
util.pop3 = function(obj){
	var _ids = [];
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
		_ids.push($(this).val());
	});
	if (!_ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}

	util._pop($(obj).attr('data-url'),{_ids:_ids});
}

//批量操作处理
util.batchConfirm = function(obj){
	$('body').modalmanager('loading');
	var _ids = [];
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
		_ids.push($(this).val());
	});
	if (!_ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}
	var url = $(obj).attr('data-url');

	var _name = $(obj).attr('data-title');
	if (!_name)
	{
		_name='';
	}
	bootbox.confirm({  
		buttons: {  
			confirm: {  
				label: '确认' 
			},  
			cancel: {  
				label: '放弃'  
			}  
		},  
		message: "确定"+_name+"?",
		closeButton: false,
		callback: function(result) {  
			if (result == true) {
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post(url,{_ids:_ids},function(data){
						if(data.success==1)
						{
							$('.modal-scrollable').trigger('click');
							util.xalert("操作成功",function(){								
								util.sync(obj);									
							});
						}
						else
						{
							util.error(data);
							//add by zhangruiying
							if(data.hasOwnProperty("is_refresh"))
							{
								if(data.is_refresh==1)
								{
									util.sync(obj);	
								}
							}
							//add end
						}
					});
				}, 0);
			}
		},  
		title: "提示信息", 
	});
}

//批量操作打开新窗口
util.pop4 = function(obj){
	var _ids = [];
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>tr>td input:checkbox[name="_ids[]"]:checked').each(function(){
		_ids.push($(this).val());
	});
	if (!_ids.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一条记录！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var _name = $(obj).attr('data-title');

	var son = window.open($(obj).attr('data-url')+'&_ids='+_ids.join(),_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);									
	};
}

//新建页签
util.newEmptyTab = function(obj){
	var url = $(obj).attr('data-url');
	var params = util.parseUrl(url);
	var _id = $(obj).attr("data-id").split('_').pop();

	var prefix = params['con'].toLowerCase()+"_xxx";

	var title = $(obj).attr('data-title');
	if (!title)
	{
		title=id;
	}
	//不能同时打开两个编辑页
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
						label: '悲催的前行' 
					},  
					cancel: {  
						label: '去瞄一眼'  
					}  
				},
				closeButton:false,
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
		var id = prefix+"-"+_id;
		new_tab(id,'编辑:'+title,url+'&id='+_id);
	}
}

//批量复制单单据货号
util.batchCopyGoodsid = function(bill_id,btn_id){
	var client = new ZeroClipboard( document.getElementById(btn_id));
	var itemsId="items_copy_"+btn_id;
	var div ='<div style="opacity:0.0;cursor: default;width:1px;height:1px;" ><textarea id="'+itemsId+'" redayonly="true"></textarea></div>';
	var bool=false;
	$.post('index.php?mod=warehouse&con=WarehouseBill&act=batchCopyGoods_id',{'bill_id':bill_id},function(data) {
		client.on( "ready", function( readyEvent ) {
			client.on("beforecopy", function( event ) {
				$('body').modalmanager('loading');
			}),
				client.on("copy", function( event ) {
					var copy_text = data;
					event.clipboardData.setData("text/plain", copy_text);
				}),
				client.on( "aftercopy", function( event ) {
					$('.modal-scrollable').trigger('click');
					//alert("复制成功: \r\n" + event.data["text/plain"] );
					util.xalert("复制成功");
				});
		}),
			client.on("error",function() {
				ZeroClipboard.destroy();
				$("#"+btn_id).on('click',function(){
					$(this).append(div);
					$("#"+itemsId).text(data);
					$("#"+itemsId).select();
					document.execCommand("Copy");
					util.xalert("复制成功");
				});
			});
	});
}
Array.prototype.contains = function(obj) {                           
		for(var x in this){
			if(x===obj)
			{
				return true;
			}
		}
		return false;
    }

 util.diff = function(a,b){
        var newArr=[];
        for(var x in a){
			if(!b.contains(x))
			{
				newArr[x]=a[x]; 
			}   
        }
        return newArr;
    }
//add by zhangruiying下载
util.downloadCSV = function (obj)
{
        var act = util.getItem("data");
        var url = $(obj).attr('data-url');
		var orl=util.getItem('orl');
		var orl_arr=util.parseUrl(orl);
		var url_arr=util.parseUrl(url);
		var arr=util.diff(orl_arr,url_arr);
		for(var j in arr)
		{
			if(typeof arr[j]!='function'){
				url+="&"+j+"="+arr[j];
			}
			
		}
        if (act != '{}' && act!=null)
        {
                act = act.replace(/\\n/g,' ');//add yangfuyou url不能还有回车符
                act = eval('(' + act + ')');
                //防止缓存
                url += '&rnd=' + Math.random() * 100;
                for (var x in act) {
						if(typeof(act[x])=='array')
						{
							for(var i=0;i<act[x].length;i++)
							{
								url += '&' + x + '=' + act[x][i];
							}
						}
						else
						{
							url += '&' + x + '=' + act[x];
						}
                       // url += '&' + x + '=' + act[x];
                }
        }
        location.href = url;return false;
}
//add end 
//add by zhangruiying 2015/5/5
//按列表字段排序
util.fieldSort = function (obj, field, form_id)
{
        var temp = $(obj).children('img');
        var src = temp.attr('src');
        if (src == '/public/img/order.png' || src == '/public/img/down.png')
        {
                var s = 'DESC';
        }
        else
        {
                var s = 'ASC';
        }
        var url = util.getItem('orl' + form_id);
        var data = util.getItem('data', form_id);
        if (data != '{}' && data!=null)
        {
                data = data.replace(/\\n/g,' ');//add yangfuyou url不能还有回车符
                data = eval('(' + data + ')');
                //防止缓存
                url += '&rnd=' + Math.random() * 100;
                for (var x in data) {
						if(typeof(data[x])=='array')
						{
							for(var i=0;i<data[x].length;i++)
							{
								url += '&' + x + '=' + data[x][i];
							}
						}
						else
						{
							url += '&' + x + '=' + data[x];
						}
                }
        }

        url += '&__order=' + field + '&__desc_or_asc=' + s;
        util.page(url);
}
//add by zhangruiying 2015/5/11
util.viewOpenNewWindowPrt=function(obj)
{
	var url=$(obj).attr('data-url');
	var objid = $("#nva-tab .active i").attr('data-id').split('-').pop();
	url+="&_ids="+objid;
	var son=window.open(url, 'newwindow','fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);									
	};
}
//add by zhangruiying列表页选中一行导出
util.download = function(obj){
	var url =$(obj).attr('data-url') ;
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}

	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	location.href=url+"&id="+objid;
}
//列表页选中一行打开新窗口ADD BY ZHANGRUIYING
util.pop5 = function(obj){
	var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');
		util.xalert("很抱歉，您当前未选中任何一行！");
		return false;
	}
	var url = $(obj).attr('data-url');
	var _name = $(obj).attr('data-title');
	var objid = tObj[0].getAttribute("data-id").split('_').pop();
	var son = window.open(url+'&id='+objid,_name,'fullscreen:true,menubar:false,resizable:false,titlebar:false,toolbar:false');
	son.onUnload = function(){
		util.sync(obj);									
	};
}
