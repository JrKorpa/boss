//领单操作
function wieldBespoke(act,bespoke_id)
{
	var tab_id=0;
	if(bespoke_id==''){
		alert('预约单号不能为空');
		return false;
	}
	$('body').modalmanager('loading');
	setTimeout(function(){
		$.post('index.php?mod=bespoke&con=AppBespokeInfo&is_ajax=1&act='+act,'bespoke_id='+bespoke_id,function(data){
			$('.modal-scrollable').trigger('click');
			if(data.success==1){
				to_look_into();
				if (act != 'get_bespoke') {
					bootbox.alert({   
						message: '操作成功',
						closeButton: false,
						title: "提示信息" 
					});
					return false;
				} else {					
		          bootbox.confirm({
		  			size : 'medium',
		  			title: "提示信息",
		  			message : '<font size="2">操作已成功，去下单？</font>',
		  			buttons: {
		  				confirm: { label: '下单', className: 'btn-primary'},
		  				cancel: { label: '不', className: 'btn-default'}
		  			},
		  			callback : function(res) {
		  				if(res) {
		  					new_tab('baseorderinfo_add_xx-0','添加','/index.php?mod=sales&con=BaseOrderInfo&act=add&tab_id=72');
		  				}
		  			}
		          });
				}
			} else{
				bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
			}
		});
	}, 200);
}

//领单页面
function to_look_into()
{
	setTimeout(function(){
		$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=to_look_into',function(data){
			$("#to_look_into").html(data.content);
		});
	}, 3000);
}

//登录后自动打开指定tab页
function auto_launch_page() {
	xlu = $.cookie('xlu');
	if (!xlu) return;
	
	xluObj = JSON.parse(xlu);
	if (!xluObj) return;
	
	new_tab(xluObj.tb,xluObj.ti,xluObj.ln);

	$.removeCookie('xlu', { domain: document.domain});
	$.removeCookie('xlu', { domain: '.'+document.domain});	
}

$import(["public/js/nav-tab-style.js?v=2.2"],function(){
	$('#leftmenu').load('index.php?mod=management&con=main&act=getMenu',function(){
		$('.page-content').load('index.php?mod=management&con=main&act=dashboard',function(){
			App.init();
			UIExtendedModals.init();
			Nav_tab();
			auto_launch_page();
			to_look_into();
			//修改密码
			$('#modify_mypass').on('click', function(){
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post('index.php?mod=management&con=main&act=changePass',function(data){
						$('.modal .modal-title').html(data.title);
						$('.modal .modal-body').html(data.content);
						//$('.modal .modal-footer').hide();
						$('.modal').modal("toggle");
					});
				}, 200);
			});
			//切换所在公司
			$('#change_company').on('click', function(){
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post('index.php?mod=management&con=main&act=changeCompany',function(data){
						$('.modal .modal-title').html(data.title);
						$('.modal .modal-body').html(data.content);
						//$('.modal .modal-footer').hide();
						$('.modal').modal("toggle");
					});
				}, 200);
			});
			//个人信息
			$('#my_info').on('click', function(){
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post('index.php?mod=management&con=main&act=showInfo',function(data){
						$('.modal .modal-title').html(data.title);
						$('.modal .modal-body').html(data.content);
						$('.modal').modal("toggle");
					});
				}, 200);
			});
			
			//帮助文档
			$('#helpme_info').on('click', function(){
				$('body').modalmanager('loading');
				setTimeout(function(){
					$.post('index.php?mod=management&con=main&act=help',function(data){
						$('.modal .modal-title').html('系统帮助文档');
						$('.modal .modal-body').html(data);
						$('.modal').modal("toggle");
					});
				}, 200);
			});
			
			//监听浏览器全屏状态
			$(document).unbind( 'fullscreenchange webkitfullscreenchange mozfullscreenchange' )
 						.bind('fullscreenchange webkitfullscreenchange mozfullscreenchange',function( evt ) {
						 	if ( document.fullscreen || document.webkitIsFullScreen || document.mozFullScreen || false) {
								$("#trigger_fullscreen").html('<i class="fa fa-arrows"></i> 退出全屏');
							}else{
								$("#trigger_fullscreen").html('<i class="fa fa-arrows"></i> 全屏模式');
							}
						});
		});
	});
});

function banBackSpace(e){   
	var ev = e || window.event;
	var obj = ev.target || ev.srcElement;
	var t = obj.type || obj.getAttribute('type');
	var vReadOnly = obj.getAttribute('readonly');
	vReadOnly = (vReadOnly == "") ? false : vReadOnly;
	var flag1=(ev.keyCode == 8 && (t=="password" || t=="text" || t=="textarea" || t=='search') 
				&& vReadOnly=="readonly")?true:false;
	var flag2=(ev.keyCode == 8 && t != "password" && t != "text" && t != "textarea" && t !='search')
				?true:false;        
	if(flag2){
		return false;
	}
	if(flag1){   
		return false;   
	}   
}
window.onload=function(){
    document.onkeypress=banBackSpace;
    document.onkeydown=banBackSpace;
}