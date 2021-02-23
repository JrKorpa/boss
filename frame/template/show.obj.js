util.setItem('orl{ITEM_KEY}','index.php?mod={TMPL_MOD}&con={TMPL_CTL}&act=search&_id='+getID().split('-').pop());//设定刷新的初始url
	util.setItem('formID{ITEM_KEY}','{DETAIL_ITEM}_search_form');
	util.setItem('listDIV{ITEM_KEY}','{DETAIL_ITEM}_search_list');


	var obj{ITEM_KEY} = function(){
		var handleForm1 = function(){
			util.search({ITEM_KEY});	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				{DETAIL_ITEM}_search_page(util.getItem('orl{ITEM_KEY}'));
			}
		}
	
	}();

	obj{ITEM_KEY}.init();