//根据货号获取该货品的仓库和库位
function GetWarehouseAndBox() {
    
    var goods_id = $('#goods_off input[name="goods_id"]').val();
   
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
           //$('goods_off input[name="warehouse_id"]').attr('disabled',true);
           //$('goods_off input[name="box_id"]').attr('disabled',true);
        });
           
    }
}
$import(['public/js/select2/select2.min.js'],function(){
	var info_id= '<%$view->get_id()%>';
	var warehouse_warehouse_id = '<%$view->get_warehouse_id()%>';
	var obj = function(){
		var initElements = function(){
			$('#goods_off select[name="warehouse_id"]').select2({
				placeholder: "请选择",
				allowClear: true,
			}).change(function (e) {
  				$(this).valid();
				$('#goods_off select[name="box_id"]').empty();
				$('#goods_off select[name="box_id"]').append('<option value=""></option>');
				var _t = $(this).val();
				if (_t) {
					$.post('index.php?mod=warehouse&con=GoodsWarehouse&act=getBox', {'id': _t}, function (data) {
						$('#goods_off select[name="box_id"]').append(data);
					});
					$('#goods_off select[name="box_id"]').change();
				}
			});


			$('#goods_off select[name="box_id"]').select2({
			    placeholder: "请选择",
			    allowClear: true,
			});
		};

		//表单验证和提交
		var handleForm = function(){
                    $("#goods_off button[name=shangjia]").click(
			function(){
				$('body').modalmanager('loading');//进度条和遮罩
				var ids=$('#goods_off textarea[name=goods_id]').val();
                                var box_sn = $("#box_id option:selected").text();
                                var warehouse = $("#goods_off select[name=warehouse_id]").val();
				if(ids.length==0)
				{
					util.xalert('请输入要上架的货！');
					return false;
				}
				ids=ids.replace(/\s+/g,',');
				var data = {goods_id:ids};
				var url = 'index.php?mod=warehouse&con=GoodsWarehouse&warehouse='+warehouse+'&box_sn='+box_sn+'&act=Updata';
                                
				$.post(url , data, function(res){
                                    if (res.success == 1){
                                        //add goods record
                                        //alert(res.info);
                                        if (res.html != ''){
                                            var num = res.success_num;
                                            var showrecord = document.getElementById('showrecord');
                                            document.getElementById('record').style.display = 'block';
                                            $("#showrecord").append(res.html);
                                            document.getElementById("pici").innerHTML = "该批次共上架："+num;
                                            num++;
                                        }
                                        
                                        //$('#error_list').html(res.error);
					//$('#num span').html(res.success_num);
					
					$('.modal-scrollable').trigger('click');// 关闭遮罩
                                        document.getElementById('goods_id').value = '';
                                        $('#goods_off select').select2("val","");
                                        $('#goods_id').value = '';
                                        $('#goods_id').focus();
                                    }else{
                                        util.xalert('上架失败');
                                        if (res.html != ''){
                                            var num = res.success_num;
                                            var showrecord = document.getElementById('showrecord');
                                            document.getElementById('record').style.display = 'block';
                                            $("#showrecord").append(res.html);
                                            document.getElementById("pici").innerHTML = "该批次共上架："+num;
                                            num++;
                                        }
                                        $('#error_list').html(res.error);
                                    }
                                    
					
				})
				return false;
			}
		);
                }
                
                $('#goods_off').validate({
				errorElement: 'span', //default input error message container
				errorClass: 'help-block', // default input error message class
				focusInvalid: false, // do not focus the last invalid input
				rules: {
					
                                        goods_id: {
                                                required: true
                                                
                                        },
					box_id: {
						required: true
					},
					warehouse_id: {
						required: true
					}
				},

				messages: {
					
                                        goods_id: {
                                                required: "货号不能为空"
                                                
                                        },
					box_id: {
						required: "柜位不能为空."
					},
					warehouse_id: {
						required: "仓库不能为空."
					}
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
					$("#"+info_form_id).ajaxSubmit(options1);
				}
			});
            
            
            
            
		var initData = function(){
			//下拉重置
			$('#goods_off :reset').on('click',function(){
				$('#goods_off input[name="goods_id"]').val('');
			});
                        $('#goods_off :reset').on('click',function(){
				$('#goods_off select').select2("val","");
			})
                       
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