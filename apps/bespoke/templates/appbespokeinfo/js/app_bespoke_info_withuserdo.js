$import(["public/js/select2/select2.min.js",
"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
var WithuserdoObj = function(){

    var initElements = function(){
			var bespoke_id=$("table input[name='bespoke_id']").val();
			$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=ModvisitiSelect',{bespoke_id:bespoke_id},function(data){
				if(data.success==1){
					$("span[name='Hf']").html(data.num);
					$("#lv").html(data.content);
				}
				else
				{
					bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
				}
			}); 		
    }
    var handleForm = function(){}
    var initData = function(){
		$('button[val=1]').on('click',function(){
			var remark=$("table textarea[name='remark']").val();
			var bespoke_id=$("table input[name='bespoke_id']").val();
			var bespoke_inshop_time=$("table input[name='bespoke_inshop_time']").val();
			var tab_id=0;
			if(remark==''){
				bootbox.alert('请输入备注！');return false;
			}
			$.post('index.php?mod=bespoke&con=AppBespokeInfo&act=ModvisitiInsert',{remark:remark,bespoke_id:bespoke_id,bespoke_inshop_time:bespoke_inshop_time},function(data){
				$('.modal-scrollable').trigger('click');
				if(data.success==1){
					util.xalert('提交成功',function(){
							//util.page(util.getItem('url'));
					});
					//bootbox.alert('提交成功');
					//$('.modal-scrollable').trigger('click');
					//util.retrieveReload();
					//util.syncTab(tab_id);
					
				}
				else
				{
					bootbox.alert(data.error ? data.error : ( data ? data : '程序异常'));
				}
			});
		})	
	}

    return {
        init:function(){
            initElements();
            handleForm();
            initData();
        }
    }
	}(); 
  WithuserdoObj.init();
});