
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	


	var AppOrderWeixiuInfoObj = function(){

		var initElements=function(){

		}
		//表单验证和提交
		var handleForm = function(){

		};
		var initData=function(){

		}

		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	AppOrderWeixiuInfoObj.init();
});

function printweixiu(obj)
{
	var ids = $('#app_order_weixiu_print #weixiu_id').val();
	//将回车替换为‘,’
	ids=ids.replace(/\r\n/g,",") 
	ids=ids.replace(/\n/g,",");  
	//alert(ids);
    var url ='index.php?mod=repairorder&con=AppOrderWeixiuPrint&act=prints';
     //js请求方法
    url = url+'&ids='+ids;
    window.open(url);      
    //window.location.href=url;
}
