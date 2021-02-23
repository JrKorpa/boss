var form_id = "style_salepolicy_search_form";
//分页
function goodsprice_by_style_salepolicy_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js","public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css","public/js/fancyapps-fancyBox/jquery.fancybox.js"],function(){
	util.setItem('orl','index.php?mod=style&con=GoodsPriceByStyle&act=getAttrSalepolicyList');//设定刷新的初始url
	util.setItem('formID',form_id);//设定搜索表单id
	util.setItem('listDIV','goodsprice_by_style_salepolicy');//设定列表数据容器id

	//匿名函数+闭包
	var Obj = function(){
		var initElements = function(){
            $('#'+form_id+' select').select2({
                placeholder: "全部",
                allowClear: true
            }).change(function(e) {
                $(this).valid();
            });
			
			$('#'+form_id+' :reset').on('click',function(){
				$('#'+form_id+' select').select2("val","");
			})

            
            // 点击图片弹出大图
	        $(".fancyboximg").fancybox({
                wrapCSS    : 'fancybox-custom',
                closeClick : true,
                openEffect : 'none',
                helpers : {
                    title : {
                        type : 'inside'
                    },
                    overlay : {
                        css : {
                            'background' : 'rgba(0,0,0,0.6)'
                        }
                    }
                }
            });
		};
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
            //$('#'+util.getItem('formID')).submit();
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	Obj.init();
});
