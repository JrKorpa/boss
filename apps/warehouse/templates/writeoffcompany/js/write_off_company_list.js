//分页
function write_off_company_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"],function(){
	util.setItem('orl','index.php?mod=warehouse&con=WriteOffCompany&act=search');//设定刷新的初始url
	util.setItem('formID','write_off_company_search_form');//设定搜索表单id
	util.setItem('listDIV','write_off_company_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
                     $('#write_off_company_search_form select').select2({
                        placeholder: "请选择",
                        allowClear: true
                    }).change(function(e) {
                        $(this).valid();
                    });
                  
                };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
                    $('#write_off_company_search_form :reset').on('click',function(){
                        $('#write_off_company_search_form select').select2("val","");
		    })
							write_off_company_search_page(util.getItem('orl'));

			
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
        $('#add_pay_type').click(function(){
        var type_name =  $("#pay_type").find("option:selected").text();
        var type =  $("#pay_type").val();
        var company_id = $("#company_id").val();
        var company = $("#company_id").find("option:selected").text();
        if(type_name=='' || company_id ==''){
            util.xalert('订购类型和销帐公司不能为空');return;
        }else{
           
            var url = "index.php?mod=warehouse&con=WriteOffCompany&act=insert&type="+type+"&type_name="+type_name+"&company_id="+company_id+"&company="+company;
            var data = {'type':type,'type_name':type_name,'company_id':company_id,'company':company};
            $.post(url,data,function(e){
                if (e.success == 1){
                    util.xalert(e.info,function(){
			util.retrieveReload();

                    });
                }else{
                    util.xalert(e.error);return;
                }
            })
        }
});
});