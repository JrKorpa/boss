function control_search_page(url){
	util.page(url);
};
function view_link_obj(obj){
	var tObj = $('#'+getID()+' .tab_click');
	if (!tObj.length)
	{
		$('.modal-scrollable').trigger('click');		
		util.xalert('很抱歉，您当前未选中任何一行！');
		return false;
	}
	var url = $(obj).attr('data-url');
	var _id = tObj[0].getAttribute("data-id").split('_').pop();

	if(tObj[0].getAttribute("data-type").split('_').pop()!=2){
		$('.modal-scrollable').trigger('click');		
		util.xalert('只有主对象有关联对象！');
		return false;
	}
	
	util._pop(url+'&id='+_id);
};

$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=management&con=Control&act=search');//设定刷新的初始url
	util.setItem('formID','control_search_form');//设定搜索表单id
	util.setItem('listDIV','control_search_list');//设定列表数据容器id
	var ControlObj = function(){
		var initElements = function(){
			$('#control_search_form select[name="application_id"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#control_search_form select[name="type"]').select2({
				placeholder: "请选择",
				allowClear: true
			});
			$('#control_search_form :reset').on('click',function(){
				$('#control_search_form select[name="application_id"]').select2("val","");
				$('#control_search_form select[name="type"]').select2("val","");
			})
		}

		var handleForm = function(){
			util.search();
		}

		var initData = function(){
			util.closeForm(util.getItem("formID"));
                        $('#'+util.getItem("formID")).submit();
			//control_search_page(util.getItem("orl"));
		}

		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}
	}();

	ControlObj.init();
});