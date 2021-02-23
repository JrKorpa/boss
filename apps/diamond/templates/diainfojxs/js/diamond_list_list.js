//分页
function zt_diamond_list_search_page(url){
	util.page(url);
}

function showall (obj) {
    //$('body').modalmanager('loading');
    //var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
    //if (!tObj.length)
    //{
        //$('.modal-scrollable').trigger('click');
        //util.xalert("很抱歉，您当前未选中任何一行！");
        //return false;
    //}
    var url = $(obj).attr('data-url');
    var params = util.parseUrl(url);
    //var _id = tObj[0].getAttribute("data-id").split('_').pop();
    var _id = 88888888888;
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
        //var title=tObj[0].getAttribute("data-title");
        var title= '展厅-更多裸钻查询';
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

//匿名回调
$import("public/js/select2/select2.min.js",function(){
    util.setItem('orl','index.php?mod=diamond&con=DiaInfoJxs&act=search');//设定刷新的初始url
	util.setItem('formID','zt_diamond_list_search_form');//设定搜索表单id
	util.setItem('listDIV','zt_diamond_list_search_list');//设定列表数据容器id

	//匿名函数+闭包


	var ListObj = function(){
		
		var initElements = function(){
			var test = $("#zt_diamond_list_search_form input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
			 	test.each(function () {
			   	if ($(this).parents(".checker").size() == 0) {
			     	$(this).show();
			     	$(this).uniform();
			    }
			  });
			}
			//初始化下拉组件
			$('#zt_diamond_list_search_form select').select2({
				placeholder: "请选择",
				allowClear: true
			});//validator与select2冲突的解决方案是加change事件	
			
			$('#zt_diamond_list_search_form :reset').on('click',function(){
				$('#zt_diamond_list_search_form :checkbox').each(function(){
					$(this).parent().removeClass("active");
				})
			})
		};
		
		var handleForm = function(){
			util.search_open();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
            $('#'+util.getItem('formID')).submit();
			//diamond_list_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	ListObj.init();
});


//选中行后新建页签
/*util.newTab = function(obj){
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
}*/
