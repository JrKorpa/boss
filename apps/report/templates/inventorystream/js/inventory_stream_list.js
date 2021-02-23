//分页
function inventory_stream_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js'],function(){
	util.setItem('orl','index.php?mod=report&con=InventoryStream&act=search');//设定刷新的初始url
	util.setItem('formID','inventory_stream_search_form');//设定搜索表单id
	util.setItem('listDIV','inventory_stream_search_list');//设定列表数据容器id
    var info_form_id = 'inventory_stream_search_form';
	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉美化 需要引入"public/js/select2/select2.min.js"
          $('#'+info_form_id+' select').select2({
              placeholder: "请选择",
              allowClear: true,
//                minimumInputLength: 2
          }).change(function(e){
              $(this).valid();
          }); 

          $('#'+info_form_id+' :reset').on('click',function(){
                //下拉置空
                $('#'+info_form_id+' select[name="dia_type"]').select2('val','').change();//single
            }); 
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			inventory_stream_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData(
                        
                );//处理默认数据
			}
		}	
	}();

	obj.init();
});


function dow(){
    var down_infos = 'downs';
    var dia_type = $("#inventory_stream_search_form [name='dia_type']").val();
    var param = "&down_infos="+down_infos+"&dia_type="+dia_type;
    url = "index.php?mod=report&con=InventoryStream&act=search"+param;
    window.open(url);
}