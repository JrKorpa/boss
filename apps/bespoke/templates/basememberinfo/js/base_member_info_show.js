var BaseMemberInfoObj = function(){

    var initElements = function(){
    	var user_id ='<%$view->get_member_id()%>';
    }
    var handleForm = function(){}
    var initData = function(){}

    return {
        init:function(){
            initElements();
            handleForm();
            initData();
        }
    }
}();



function app_member_address_search_page(url){
	util.page(url,1);
}
var _id="<%$view->get_member_id()%>";
$import(function(){
	util.setItem('orl1','index.php?mod=bespoke&con=AppMemberAddress&act=search&_id='+_id);//�趨ˢ�µĳ�ʼurl
	util.setItem('listDIV1','app_member_address_search_list');

	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_member_address_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();
	//util.closeDetail();//����������ϸ
	//util.closeDetail(true);//չʾ��һ����ϸ
});