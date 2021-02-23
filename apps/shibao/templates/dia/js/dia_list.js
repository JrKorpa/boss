//分页
function dia_search_page(url){
	util.page(url);
}

//匿名回调
$import("public/js/select2/select2.min.js",function(){
	util.setItem('orl','index.php?mod=shibao&con=Dia&act=search');//设定刷新的初始url
	util.setItem('formID','dia_search_form');//设定搜索表单id
	util.setItem('listDIV','dia_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){

		
		var initElements = function(){
			$('#dia_search_form select').select2({
					placeholder: "请选择",
					allowClear: true
			});
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
				$('#dia_search_form :reset').on('click',function(){
				$('#dia_search_form select[name="kucun_cnt"]').select2("val",'')
			})
			util.closeForm(util.getItem("formID"));
			dia_search_page(util.getItem("orl"));
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


function download() {

    var shibao 	 = $("#dia_search_form input[name='shibao']").val();
    var kucun_cnt = $("#kucun_cnt").val();

    var args = "&shibao="+shibao+"&kucun_cnt="+kucun_cnt;
    location.href = "index.php?mod=shibao&con=Dia&act=download"+args;
}