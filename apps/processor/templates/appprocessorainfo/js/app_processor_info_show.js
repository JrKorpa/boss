function app_processor_fee_search_page(url){
	util.page(url,1);
}

function rel_style_factory_search_page(url){
	util.page(url,2);
}

function app_processor_worktime_search_page(url){
	util.page(url,3);
}
$import(function(){
	util.setItem('orl1','index.php?mod=processor&con=AppProcessorFee&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV1','app_processor_info_fee_show');

	util.setItem('orl2','index.php?mod=style&con=RelStyleFactory&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV2','style_factory_search_list');
    
    util.setItem('orl3','index.php?mod=processor&con=AppProcessorWorktime&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('listDIV3','app_processor_worktime_list');


	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_processor_fee_search_page(util.getItem('orl1'));
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

	//obj2.init();

    var obj3 = function(){
		var handleForm3 = function(){
			util.search(3);	
		}
	
		return {
		
			init:function(){
				handleForm3();
				//util.closeForm(util.getItem("form1"));
				app_processor_worktime_search_page(util.getItem('orl3'));
			}
		}
	
	}();

	obj3.init();

	//util.closeDetail();//收起所有明细
	//util.closeDetail(true);//展示第一个明细
});