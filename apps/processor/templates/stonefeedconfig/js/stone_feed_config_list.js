//分页
function stone_feed_config_search_page(url){
	util.page(url);
}
var formID= 'stone_feed_config_search_form';
//匿名回调
$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=processor&con=stoneFeedConfig&act=search');//设定刷新的初始url
	util.setItem('formID',formID);//设定搜索表单id
	util.setItem('listDIV','stone_feed_config_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			
			if ($.datepicker) {
				$('.date-picker').datepicker({
					format: 'yyyy-mm-dd',
					rtl: App.isRTL(),
					autoclose: true,
					clearBtn: true
				});
				$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
			}
			 $('#'+formID+' select').select2({
				placeholder: "请选择",
				allowClear: true

		    }).change(function(e){
				$(this).valid();
		    });
		};
		   
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			stone_feed_config_search_page(util.getItem("orl"));
			$('#'+formID+' :reset').on('click',function(){
				$('#'+formID+' select').select2('val','');
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

	obj.init();
});
