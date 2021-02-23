
function send_goods(order_sn){
	util.retrieveReload(this);
	var goods_id_list = $('#base_order_info_search_form2 input[name="goods_id_list"]').val();
    window.open('index.php?mod=shipping&con=VopBaseOrderInfo&act=sendGoods&order_sn='+order_sn+'&goods_id_list='+goods_id_list); 
}

function search_next2(_key){
	if (typeof _key=='undefined')
	{
		var _key='';
	}
	else
	{
		_key=parseInt(_key);
	}
	var options1 = {
		url:util.getItem("orl"+_key),
		target:'#'+util.getItem("listDIV"+_key),
		error:function ()
		{
			util.error("请求超时");
			return false;
		},
		beforeSubmit:function(frm,jq,op){
			var order_sn =  $('#base_order_info_search_form2 input[name="order_sn"]').val();
			var goods_num = $('#base_order_info_search_form2 input[name="goods_num"]').val();
			var goods_id = $('#base_order_info_search_form2 input[name="goods_id"]').val(); 
		    var goods_id_list = $('#base_order_info_search_form2 input[name="goods_id_list"]').val();
		    
		    if($('#base_order_info_search_form2 input[name="goods_id"]').val()==""){
		    	alert('请输入有效货号');
		    	foc();
		    	return false;
		    }
            
            if(goods_id_list==""){
            	goods_id_list = goods_id;
            }else{
            	goods_id_list = goods_id_list + "_" +goods_id;
            }
		  
		    var goods_array =goods_id_list.split("_");

		    if($('#base_order_info_search_form2 input[name="goods_num"]').val() == goods_array.length){
                util.retrieveReload(this);
                window.open('index.php?mod=shipping&con=VopBaseOrderInfo&act=sendGoods&order_sn='+order_sn+'&goods_id_list='+goods_id_list);       
		    	return false;
		    }		
			$('body').modalmanager('loading');
                        var jsondata = {};
			var _url = '';
                        var flag=false;
                        var tmp = [];
                        var name='';
			$(frm).each(function(i,e){                               
                                if(e.name.indexOf('[]')>0)
                                {
                                        flag = true;
                                        var tt = e.name.substr(0,e.name.length-2);
                                        if(name==''){
                                                name = tt;
                                                tmp.push(e.value);
                                        }
                                        else if(name==tt)
                                        {
                                                tmp.push(e.value);        
                                        }
                                        else
                                        {
                                                jsondata[name]=tmp;
                                                tmp=[];
                                                name=tt;
                                                tmp.push(e.value);
                                        }      
                                }
                                else
                                {
                                        if(flag){
                                                flag=false;
                                                jsondata[name]=tmp;
                                                tmp=[];
                                        }
                                        jsondata[e.name] = e.value;
                                }
				_url+="&"+e.name+"="+e.value;
			});

			util.setItem("data"+_key,JSON.stringify(jsondata));
			_url = _url.replace(/\n/g,' ');
			util.setItem("url"+_key,util.getItem("orl"+_key)+_url);
		},
		success: function(data) {
			$('body').modalmanager('removeLoading');
			//$('.modal-scrollable').trigger('click');
			//util.closeForm(util.getItem("formID"+_key));
		}
	};

	$("#"+util.getItem("formID"+_key)).ajaxForm(options1);
}




function foc(){
	//alert('test');
    $('#base_order_info_search_form2 input[name="goods_id"]').focus();
}


//匿名回调
$import(function(){
	util.setItem('orl','index.php?mod=shipping&con=VopBaseOrderInfo&act=search2');//设定刷新的初始url
	util.setItem('formID','base_order_info_search_form2');//设定搜索表单id
	util.setItem('listDIV','search_form1');//设定列表数据容器id
    
	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){};
		
		var handleForm = function(){
			//util.search();
			//util.search_open();
			search_next2();			

		};
		
		var initData = function(){
			setTimeout('foc()',500);
			//util.closeForm(util.getItem("formID"));			
			//base_order_info_search_page(util.getItem("orl"));			
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


  