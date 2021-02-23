//分页
function stock_purchase_search_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js'],function(){
	util.setItem('orl','index.php?mod=purchase&con=StockPurchaseSearch&act=search');//设定刷新的初始url
	util.setItem('formID','stock_purchase_search_search_form');//设定搜索表单id
	util.setItem('listDIV','stock_purchase_search_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
                //下拉美化 需要引入"public/js/select2/select2.min.js"
                  $('#'+util.getItem("formID")+' select').select2({
                      placeholder: "请选择",
                      allowClear: true,
        //                minimumInputLength: 2
                  }).change(function(e){
                      $(this).valid();
                  }); 

                $('#'+util.getItem("formID")+' :reset').on('click',function(){
                    $('#'+util.getItem("formID")+' select[name="channel_id"]').select2('val','').change();//single
                    $('#'+util.getItem("formID")+' select[name="yanse"]').select2('val','').change();//single
                    $('#'+util.getItem("formID")+' select[name="jingdu"]').select2('val','').change();//single
                    $('#'+util.getItem("formID")+' select[name="caizhi"]').select2('val','').change();//single
                    $('#'+util.getItem("formID")+' select[name="18k_color"]').select2('val','').change();//single
                });  
            };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			stock_purchase_search_search_page(util.getItem("orl"));


		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});