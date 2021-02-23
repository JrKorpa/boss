//根据货号获取该货品的仓库和库位
function GetWarehouseAndBox() {
    
    var goods_id = $('#goods_on input[name="goods_id"]').val();
   
    var url = 'index.php?mod=warehouse&con=GoodsWarehouse&act=GetData&goods_id='+goods_id;
   
    if (goods_id) {
        $.post(url, {'goods_id': goods_id}, function (data) {
            //alert(data);return false;
           var strs= new Array(); //定义一数组
           strs=data.split(","); //字符分割
          
           document.getElementById('warehouse_id').value = strs[0];
           document.getElementById('box_id').value = strs[1];
           $("#warehouse_id").attr('disabled',true);
           $("#box_id").attr('disabled',true);
           //$('goods_on input[name="warehouse_id"]').attr('disabled',true);
           //$('goods_on input[name="box_id"]').attr('disabled',true);
        });
           
    }
}
//获取相关信息根据货号  款号，柜位，仓库
function GetData() {
    var goods_id = $('#goods_on input[name="goods_id"]').val();alert(goods_id);
    var url = 'index.php?mod=warehouse&con=GoodsWarehouse&act=GetData&type=on&goods_id='+goods_id;
    //回车提交
    $('#goods_on input').keypress(function (e) {
        if (e.which == 13) {
            alert('ffffff');return false;
            $.post(url, {'goods_id': goods_id}, function (data) {
            var strs= new Array(); //定义一数组
            strs=data.split(","); //字符分割

            document.getElementById('warehouse_id').value = strs[0];
            document.getElementById('box_id').value = strs[1];
            $("#warehouse_id").attr('disabled',true);
            $("#box_id").attr('disabled',true);
           
            });
        }
           
    });
    
}

function setBoxSn(obj){
    var select = document.getElementById("box_id");
    var id = obj.id;
    var strs= new Array(); //定义一数组
    strs=id.split("|"); //字符分割
    var box_id = strs[1];
    var box_sn = strs[0];
    var warehouse = document.getElementById('warehouse_id').value;
    var warehouse2 = obj.name;
    if (warehouse2 != warehouse){
        alert('该柜位不属于该货号的仓库！');
        
        return false;
    }
    for(var i=0; i<select.options.length; i++){ 
        if(select.options[i].innerHTML == box_sn){  
            //select.options[i].selected = true;  //美化过的select对此无效
            var a = $('#goods_on select[name="box_id"]').prev();
            a.addClass('select2-allowclear');
            $('#goods_on select[name="box_id"]').select2('val',box_id); 
            $('#goods_on select[name="box_id"]').change(); 
            break;  
        } 
    }
}

$import(['public/js/select2/select2.min.js'],function(){
	var info_id= '<%$view->get_id()%>';
	var warehouse_warehouse_id = '<%$view->get_warehouse_id()%>';
	var obj = function(){
		var initElements = function(){
			$('input:text:first').focus();
                        var $inp = $('#goods_on input:text');
                        $inp.bind('keydown', function (e) {
                            var key = e.which;
                            if (key == 13) {
                                var goods_id = $('#goods_on input[name="goods_id"]').val();
                                var url = 'index.php?mod=warehouse&con=GoodsWarehouse&act=GetData&type=on&goods_id='+goods_id;
                                //alert(url);return false;
                                $.post(url, {'goods_id': goods_id}, function (data) {
                                    if(data.error == 1){
                                        alert(data.info);
                                        $('.modal-scrollable').trigger('click');//关闭遮罩
                                        return false;
                                    }else{
                                        var strs= new Array();
                                        //var divshow = $("#showinfo");
                                        var divshow = document.getElementById('showinfo');
                                        strs=data.split(","); //字符分割
                                        document.getElementById('warehouse_id').value = strs[0];
                                        //shosetype.options.add(new Option("1","添加成功"));
                                        //document.getElementById('box_id').value = strs[1];
                                        document.getElementById('box_id').innerHTML = strs[1];
                                        //document.getElementById('box_id').options.add(new Option("1","添加成功"));
                                        divshow.innerHTML=strs[2];
                                        
                                        $("#warehouse_id").attr('disabled',true);
                                    }
                                    

                                });
                                //e.preventDefault();
                                //var nxtIdx = $inp.index(this) + 1;
                                //$(":input:text:eq(" + nxtIdx + ")").focus();
                            }
                        });
                        $('#goods_on select[name="box_id"]').select2({
			    placeholder: "请选择",
			    allowClear: true,
			});
		};

		//表单验证和提交
		var handleForm = function(){
                        //回车不要提交
			$('#goods_on input').keypress(function (e) {
				if (e.which == 13) {
					
                                        return false;
				}
				// $('.modal-scrollable').trigger('click');//关闭遮罩
			});
                        var num = 1;
                        var goods_id = $('#goods_on input[name="goods_id"]').val();
                        var box_id = $('#goods_on select[name="box_id"]').val();
                        var url = 'index.php?mod=warehouse&con=GoodsWarehouse&act=SaveData&box_id='+box_id;
                        //alert(url);
			var options1 = {
				url: url,
				error:function ()
				{
					$('.modal-scrollable').trigger('click');
					bootbox.alert('请求超时，请检查链接');
				},
				beforeSubmit:function(frm,jq,op){
					$('body').modalmanager('loading');//进度条和遮罩
				},
				success: function(data) {
					if(data.success == 1 ){
                                            alert(data.info);
                                            
                                            var showrecord = document.getElementById('showrecord');
                                            var divshow = document.getElementById('showinfo');
                                            //showrecord.innerHTML =data.html;
                                           
                                            document.getElementById('record').style.display = 'block';
                                            $("#showrecord").append(data.html);
                                            document.getElementById("pici").innerHTML = "该批次共上架："+num;
                                            num++;
                                            //showrecord.append(data.html);
                                            $('#warehouse_id').val('');
                                            $('#goods_id').val('');
                                            $('#box_id').val('');
                                            $('#goods_on select[name="box_id"]').select2('val',''); 
                                            divshow.innerHTML = '';
                                            $('.modal-scrollable').trigger('click');//关闭遮罩
                                            $('#goods_on input[name="goods_id"]').focus();
					}else{
                                            alert(data.info);
					    $('body').modalmanager('removeLoading');//关闭进度条
						
					}
				}
			};

			$('#goods_on').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {

				},
				messages: {

				},

				highlight: function (element) { // hightlight error inputs
					$(element)
						.closest('.form-group').addClass('has-error'); // set error class to the control group
					//$(element).focus();
				},

				success: function (label) {
					label.closest('.form-group').removeClass('has-error');
					label.remove();
				},

				errorPlacement: function (error, element) {
					error.insertAfter(element.closest('.form-control'));
				},

				submitHandler: function (form) {
					$("#goods_on").ajaxSubmit(options1);
				}
			});
                       
			

		};
		var initData = function(){
			//下拉重置
			$('#goods_on :reset').on('click',function(){
				$('#goods_on input[name="goods_id"]').val('');
			});
                        
		};
		return {
			init:function(){
				initElements();//处理表单元素
				handleForm();//处理表单验证和提交
				initData();//处理表单重置和其他特殊情况
			}
		}
	}();
	obj.init();
});