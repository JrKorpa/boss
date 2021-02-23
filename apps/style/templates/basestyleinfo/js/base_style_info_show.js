function checkprice(style_id,type){
	var jinzhong_name = "#style_jinzhong_"+type;
	var chengben_name = "#chengben_"+type;
	var jinzhong = $(jinzhong_name).val();
	var chengben = $(chengben_name).val();
	
	var style_id=style_id;
	if(!chengben){
		alert("请输入成本价格!");
		return false;
	}
	 $.post("index.php?mod=style&con=AppXiangkou&act=createOtherGoods",{style_id:style_id,jinzhong:jinzhong,chengben:chengben,type:type},function(res){
  		  if(res.error){
  		  	alert(res.error);
  		  }else{
  		  	alert("商品生成成功!");
  		  }
  		  
  });
	
}

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

function rel_style_stone_search_page(url){
	util.page(url,1);
}

function rel_style_factory_search_page(url){
	util.page(url,2);
}

function app_style_gallery_search_page(url){
	util.page(url,3);
}

function app_style_for_search_page(url){
	util.page(url,4);
}

function rel_style_attribute_search_page(url){
	util.page(url,5);
}

var style_sn='<%$view->get_style_sn()%>';
var style_id='<%$view->get_style_id()%>';
var cat_type_id='<%$view->get_style_type()%>';
var product_type_id='<%$view->get_product_type()%>';
function app_xiangkou_search_page(url){
	util.page(url,6);
}

function app_style_fee_search_page (url){
	util.page(url,7);
}

function base_style_log_search_page (url){
	util.page(url,8);
}

function _sub(obj,num,msg)
{
	$('body').modalmanager('loading');
	var url =$(obj).attr('data-url') ;
	var id = $(obj).attr('data-id');
	if (typeof num=='undefined')
	{
		var num=1;
	}
	if (typeof msg=='undefined')
	{
		var msg='提交成功';
	}
	//alert($('input[name="sec_stone_weight[]"]').length);
	var stone='';
	var finger='';
	var sec_stone_weight='';
	var sec_stone_num='';
	var sec_stone_weight_other='';
	var sec_stone_num_other='';
    var sec_stone_weight3='';
    var sec_stone_num3='';
	var sec_stone_price_other='';
	var g18_weight='';
	var g18_weight_more='';
	var g18_weight_more2='';
	var gpt_weight='';
	var gpt_weight_more='';
	var gpt_weight_more2='';
	var company_type='';
	var sub_company_type=[];
	
	for(var i=0;i<$('input[name="sec_stone_weight[]"]').length;i++){
		stone+=$('input[name="stone[]"]:eq('+i+')').val()+',';
		finger+=$('input[name="finger[]"]:eq('+i+')').val()+',';
		sec_stone_weight+=$('input[name="sec_stone_weight[]"]:eq('+i+')').val()+',';
		sec_stone_num+=$('input[name="sec_stone_num[]"]:eq('+i+')').val()+',';
		sec_stone_weight_other+=$('input[name="sec_stone_weight_other[]"]:eq('+i+')').val()+',';
		sec_stone_num_other+=$('input[name="sec_stone_num_other[]"]:eq('+i+')').val()+',';
        sec_stone_weight3+=$('input[name="sec_stone_weight3[]"]:eq('+i+')').val()+',';
        sec_stone_num3+=$('input[name="sec_stone_num3[]"]:eq('+i+')').val()+',';
		sec_stone_price_other+=$('input[name="sec_stone_price_other[]"]:eq('+i+')').val()+',';
		g18_weight+=$('input[name="g18_weight[]"]:eq('+i+')').val()+',';
		g18_weight_more+=$('input[name="g18_weight_more[]"]:eq('+i+')').val()+',';
		g18_weight_more2+=$('input[name="g18_weight_more2[]"]:eq('+i+')').val()+',';
		gpt_weight+=$('input[name="gpt_weight[]"]:eq('+i+')').val()+',';
		gpt_weight_more+=$('input[name="gpt_weight_more[]"]:eq('+i+')').val()+',';
		gpt_weight_more2+=$('input[name="gpt_weight_more2[]"]:eq('+i+')').val()+',';
	}

	for(var i=0;i<$('input[name="stone_type[]"]').length;i++){
		sub_company_type=[];
		$('input[name="company_type_'+i+'"]:checked').each(function(index, element) {
						//sub_company_type.push($(element).val());
						sub_company_type.push($(this).val());
						});
		company_type+=$('input[name="stone_type[]"]:eq('+i+')').val()+':'+sub_company_type.join(',')+';';

	}
	bootbox.confirm("确定信息完整并提交?", function(result) {
		if (result == true) {
            $(obj).button('loading');
			setTimeout(function(){
				$.post(url,{id:id,status:num,stone:stone,finger:finger,sec_stone_weight:sec_stone_weight,sec_stone_num:sec_stone_num,
					sec_stone_weight_other:sec_stone_weight_other,sec_stone_num_other:sec_stone_num_other,sec_stone_weight3:sec_stone_weight3,sec_stone_num3:sec_stone_num3,sec_stone_price_other:sec_stone_price_other,g18_weight:g18_weight,g18_weight_more:
					g18_weight_more,g18_weight_more2:g18_weight_more2,gpt_weight:gpt_weight,gpt_weight_more:gpt_weight_more,gpt_weight_more2:gpt_weight_more2,company_type:company_type},function(data){
					$('.modal-scrollable').trigger('click');
					if(data.success==1){
                        $(obj).button('reset');
						alert(msg);
                        $('#1111111').trigger('click');
						//util.retrieveReload(obj);
					}
					else{
						$(obj).button('reset');
						alert(data.error ? data.error : ( data ? data : '程序异常'));
                        //$('#1111111').trigger('click');
					}
				});
			}, 0);
		}
	});
}

$import(function(){
	util.setItem('orl1','index.php?mod=style&con=RelStyleStone&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV1','style_stone_search_list');

	util.setItem('orl2','index.php?mod=style&con=RelStyleFactory&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV2','style_factory_search_list');

	util.setItem('orl3','index.php?mod=style&con=AppStyleGallery&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV3','style_gallery_search_list');

	util.setItem('orl4','index.php?mod=style&con=AppStyleFor&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV4','app_style_for_search_list');

	util.setItem('orl5','index.php?mod=style&con=RelStyleAttribute&act=search&_id='+getID().split('-').pop()+'&style_sn='+style_sn+'&cat_type_id='+cat_type_id+'&product_type_id='+product_type_id+'&style_id='+style_id);//设定刷新的初始url
	util.setItem('listDIV5','style_attribute_search_list');

	util.setItem('orl6','index.php?mod=style&con=AppXiangkou&act=search&_id='+getID().split('-').pop()+'&style_sn='+style_sn+'&style_id='+style_id);//设定刷新的初始url
	util.setItem('listDIV6','app_xiangkou_search_list');
	
	util.setItem('orl7','index.php?mod=style&con=AppStyleFee&act=search&_id='+getID().split('-').pop());
	util.setItem('listDIV7','app_style_fee_search_list');

	util.setItem('orl8','index.php?mod=style&con=BaseStyleLog&act=search&_id='+getID().split('-').pop());
	util.setItem('listDIV8','base_style_log_search_page');
	//工费
	//util.setItem('orl7','index.php?mod=style&con=AppStyleFee&act=search&_id='+getID().split('-').pop());
	//util.setItem('listDIV7','app_style_fee_search_list');


	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				rel_style_stone_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();


	var obj2 = function(){
		var handleForm1 = function(){
			util.search(2);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				rel_style_factory_search_page(util.getItem('orl2'));
			}
		}
	
	}();

	obj2.init();


	var obj3 = function(){
		var handleForm1 = function(){
			util.search(3);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_style_gallery_search_page(util.getItem('orl3'));
			}
		}
	
	}();

	obj3.init();

	var obj4 = function(){
		var handleForm1 = function(){
			util.search(4);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_style_for_search_page(util.getItem('orl4'));
			}
		}
	
	}();

	obj4.init();
	var obj5 = function(){
		var handleForm1 = function(){
			util.search(5);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				rel_style_attribute_search_page(util.getItem('orl5'));
			}
		}
	
	}();

	obj5.init();

	var obj6 = function(){
		var handleForm1 = function(){
			util.search(6);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_xiangkou_search_page(util.getItem('orl6'));
			}
		}
	
	}();

	obj6.init();
	//util.closeDetail();//收起所有明细
	//util.closeDetail(true);//展示第一个明细

	var obj7 = function(){
		var handleForm1 = function(){
			util.search(7);	
		}

		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_style_fee_search_page(util.getItem('orl7'));
			}
		}

	}();
	obj7.init();

	var obj8 = function(){
		var handleForm1 = function(){
			util.search(8);	
		}

		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				base_style_log_search_page(util.getItem('orl8'));
			}
		}

	}();
	obj8.init();
});
	
	

