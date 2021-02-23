// tab 关闭
var tab_delete = function() {
	$('#nva-tab').on('click', 'li > .close', function (e) {
		e.preventDefault();
		var $li = $(this).parent("li");//获取被点击父级元素
		if ($li.hasClass("active")) {//判断父级时候打开状态
			$li.prev("li").children('a').trigger("click");//如果打开关闭后显示前一个
			var ooo = $li.prev("li").children('i');
			if (ooo.length)
			{
				setTabID($li.prev("li").children('i').attr('data-id'));
			}
			else{
				setTabID(0);
			}
		}
  	$li.remove();
  	$("#tab-content").children('div[id="'+$(this).attr("data-id")+'"]').remove();
  	// console.log(a);
	});
}
//鼠标中键点击关闭
var mous_delete = function() {
	$('#nva-tab li a').live('mousedown', function (e) {
		//debugger;
		e.preventDefault();
		// 1 = 鼠标左键 left; 2 = 鼠标中键; 3 = 鼠标右键
		if(2 == e.which){
			$(this).siblings('i').trigger('click');
			return false;
		}
	});
}
//点击菜单添加tab元素
var tab_nav = function() {
	$('.sub-menu').on('click', 'ul > li > a', function (e) {
		e.preventDefault();
		//添加点击样式
		$("#sidebar-menu li ul li ul li").removeClass('active');
		$(this).parent("li").addClass('active');
		//获取唯一值
		var $id = $(this).attr('data-id');
		var url = $(this).attr('data-url');
		var $title = $(this).text();
		//判断标签页是否存在
		new_tab($id,$title,url);
	});
}
//公共方法，新建标签
var new_tab = function(id,title,url) {
	var $li = $("#nva-tab li").children('a[href="#'+id+'"]');
		if ($li.length != 1) {
			$("#nva-tab").append('<li><a href="#'+id+'" data-toggle="tab">'+title+'</a><i class="close" data-id="'+id+'" data-url="'+url+'"></i></li>');
			$("#tab-content").append('<div class="tab-pane" id="'+id+'">操作请求中，请稍候！</div>');
			//显示加载条
			$('body').modalmanager('loading');
    	setTimeout(function(){
    		//关闭加载条
    		$('.modal-scrollable').trigger('click');
    		$("#nva-tab li").children('a[href="#'+id+'"]').trigger("click");
  		}, 500);
			$("#"+id).load(url);
		}
		$li.trigger("click");
		if ($(window).width() < 992) {
			if (!$(".navbar-toggle").hasClass('collapsed')) {
				setTimeout(function(){
					$(".navbar-toggle").trigger("click");
				}, 500);
			};
		}
		setTabID(id);
}
// 切换页内容中点击，添加切换页
var tab_con_a = function() {
	$('#tab-content').on('click', 'a.tab-con-a', function (e) {
		e.preventDefault();
		var $title = $(this).attr('data-title');
		var $id = $(this).attr('data-id');
		var url = $(this).attr('data-url');
		var params = util.parseUrl(url);
		var prefix = params['con'].toLowerCase();
		var flag=false;
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
								new_tab($id,$title,url);
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
			new_tab($id,$title,url);
		}
	})
}

var Nav_tab = function() {
	tab_delete();
	tab_nav();
	mous_delete();
	tab_con_a();
}