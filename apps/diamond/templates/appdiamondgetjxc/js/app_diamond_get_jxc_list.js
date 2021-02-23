//分页
function diamond_get_jxc_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js", 'public/js/jquery.validate.extends.js'],function(){
	var info_id='';
	//匿名函数+闭包
	var obj1 = function(){
		
		var initElements = function(){
			//下拉组件
			$('#xianhuoway').select2({
				placeholder: "全部",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
		};
		
        //表单验证和提交
        var handleForm = function() {
            var url = 'index.php?mod=diamond&con=AppDiamondGetJxc&act=upload_ins';
            var options1 = {
                url: url,
                error: function()
                {
                    $('.modal-scrollable').trigger('click');
                    bootbox.alert({
                        message: "请求超时，请检查链接",
                        buttons: {
                            ok: {
                                label: '确定'
                            }
                        },
                        animate: true,
                        closeButton: false,
                        title: "提示信息"
                    });
                    return;
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        bootbox.alert({
                            message: info_id ? "修改成功!" : "添加成功!",
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });

                        if (data._cls)
                        {
                            util.retrieveReload();
                            util.syncTab(data.tab_id);
                        }
                        else
                        {//刷新首页
							diamond_get_jxc_search_page(util.getItem("orl"));
                        }

                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        bootbox.alert({
                            message: data.error ? data.error : (data ? data : '程序异常'),
                            buttons: {
                                ok: {
                                    label: '确定'
                                }
                            },
                            animate: true,
                            closeButton: false,
                            title: "提示信息"
                        });
                        return;
                    }
                }
            };
			$("#diamond_get_jxc_info").ajaxForm(options1);
        };
		
		var initData = function(){
			//开始下载按钮
			$("button:button[val=1]").on('click', function(){
				var xianhuoway = $('#xianhuoway').val();   
				location.href = "index.php?mod=diamond&con=AppDiamondGetJxc&act=downLoad&id="+xianhuoway;
			});
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();
	obj1.init();
});

function diamond_dow_mo()
{
	document.location.href='index.php?mod=diamond&con=AppDiamondInfo&act=dow';
}
