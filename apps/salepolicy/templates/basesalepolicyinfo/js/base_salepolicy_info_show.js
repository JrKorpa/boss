debugger;
function app_salepolicy_channel_search_page(url){
	util.page(url,1);
}

function app_salepolicy_channel_log_search_page(url){
	util.page(url,2);
}

function app_salepolicy_goods_search_page(url){
	util.page(url,3);
}
function app_yikoujia_goods_search_page(url){
	util.page(url,3);
}
function downcsv(o){
    var url = $(o).attr('data-url');
    debugger;
    location.href = url;
}

function print_info(obj)
{
	var url =$(obj).attr('data-url') ;
	var id = '<%$view->get_policy_id()%>';
	//js请求方法
	window.location.href=url+"&id="+id;
	//$.post(url,{id:id},function(data){
		//alert(data);
	//})
}
$import(["public/js/select2/select2.min.js"],function(){
	var id = '<%$view->get_policy_id()%>';
	util.setItem('orl1','index.php?mod=salepolicy&con=AppSalepolicyChannel&act=showlist&_id='+id);//设定刷新的初始url
	util.setItem('formID1','reply_search_form');
	util.setItem('listDIV1','app_salepolicy_channel_show_list'+id);

	util.setItem('orl2','index.php?mod=salepolicy&con=AppSalepolicyChannelLog&act=search&_id='+id);//设定刷新的初始url
	util.setItem('formID2','message_reply_search_form');
	util.setItem('listDIV2','app_salepolicy_channel_log_show_list'+id);
    /*
	util.setItem('orl3','index.php?mod=salepolicy&con=AppSalepolicyGoods&act=search&_id='+id);//设定刷新的初始url
	util.setItem('formID3','app_salepolicy_goods_form');
	util.setItem('listDIV3','app_salepolicy_goods_show_list'+id);
    */
	util.setItem('orl3','index.php?mod=salepolicy&con=AppYikoujiaGoods&act=search&_id='+id);//设定刷新的初始url
	util.setItem('formID3','app_yikoujia_goods_form');
	util.setItem('listDIV3','app_yikoujia_goods_show_list'+id);
    util.setItem('url','index.php?mod=salepolicy&con=AppYikoujiaGoods&act=search&_id='+id);
	var obj1 = function(){
		var handleForm1 = function(){
			util.search(1);	
		}
	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_salepolicy_channel_search_page(util.getItem('orl1'));
			}
		}
	
	}();

	obj1.init();


	var obj2 = function(){
		var handleForm1 = function(){
			util.search(2);	
		}
		$('#base_yikoujia_goods_search_form_app select').select2({
            placeholder: "请选择",
            allowClear: true
        }).change(function(e){
            $(this).valid();
        });
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_salepolicy_channel_log_search_page(util.getItem('orl2'));
			}
		}
	
	}();

	obj2.init();

	var obj3 = function(){
		var handleForm1 = function(){
			util.search(3);	
		}
        $('#base_salepolicy_goods_search_form_app select').select2({
            placeholder: "请选择",
            allowClear: true
        }).change(function(e){
            $(this).valid();
        });

        $('#s_goods_list').click(function(){
            //获取销售政策和表单信息
            var policy_id = '<%$view->get_policy_id()%>';
            var url = 'index.php?mod=salepolicy&con=AppYikoujiaGoods&act=search&_id='+policy_id;
            var goods_id = $('#base_yikoujia_goods_search_form_app input[name="goods_id"]').val();
			var goods_sn = $('#base_yikoujia_goods_search_form_app input[name="goods_sn"]').val();
            var min_p = $('#base_yikoujia_goods_search_form_app input[name="small"]').val();
            var max_p = $('#base_yikoujia_goods_search_form_app input[name="sbig"]').val();
			var is_delete = $('#base_yikoujia_goods_search_form_app select[name="is_delete"]').val();
			var cert = $('#base_yikoujia_goods_search_form_app select[name="cert"]').val();
            //var xianhuo = $('#base_salepolicy_goods_search_form_app select[name="xianhuo"]').val();
            //var is_valid = $('#base_salepolicy_goods_search_form_app select[name="is_valid"]').val();
            //等待各种新的搜索条件
            $.post(url,{goods_id:goods_id,goods_sn:goods_sn,small:min_p,sbig:max_p,is_delete:is_delete,cert:cert},function(data){
               $("#app_yikoujia_goods_show_list<%$view->get_policy_id()%>").empty().html(data);
                $('#goods_s_trigger').trigger('click');
            });
			var formdata = $("#base_yikoujia_goods_search_form_app").serialize();
            url += "&"+formdata;
            util.setItem('url',url);


        });


	
		return {
		
			init:function(){
				handleForm1();
				//util.closeForm(util.getItem("form1"));
				app_yikoujia_goods_search_page(util.getItem('orl3'));
			}
		}
	
	}();

	obj3.init();

	//util.closeDetail();//收起所有明细
	util.closeDetail(true);//展示第一个明细
});


function printcode(){
    var id ='<%$view->get_policy_id()%>';
    location.href = "index.php?mod=salepolicy&con=AppSalepolicyGoods&act=printcode&id="+id;

}
function download() {
	var formdata = $("#base_yikoujia_goods_search_form_app").serialize();
    location.href = "index.php?mod=salepolicy&con=AppYikoujiaGoods&act=downloads&"+formdata;
}

  




