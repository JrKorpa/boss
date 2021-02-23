

//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/select2/select2.min.js"],function(){

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){

		};
		
		//表单验证和提交
		var handleForm = function(){
			$(':button').on('click',function(){
				//document.location.href='index.php?mod=diamond&con=AppDownloadZhekou&act=xiazai';				
			})			
		};
		
		var initData = function(){

		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

function diamond_dow_mo()
{
	document.location.href='index.php?mod=diamond&con=AppDownloadZhekou&act=downLoad';
}