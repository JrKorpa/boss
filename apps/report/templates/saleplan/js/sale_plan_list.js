//分页
function monthly_export_search_page(url){
	util.page(url);
}
 var demo = function(obj){
  //$('body').modalmanager('loading');
  var tObj = $(obj).parent().parent().siblings().find('table>tbody>.tab_click');
  /*if (!tObj.length)
  {
    $('.modal-scrollable').trigger('click');
    util.xalert("很抱歉，您当前未选中任何一行！");
    return false;
  }*/
  var url = $(obj).attr('data-url')+"&export_time_start="+$('input[name=export_time_start]').val();

  var params = util.parseUrl(url);
 //console.log(params);
  //var _id = tObj.getAttribute("data-id").split('_').pop();
var _id = 1;
  var prefix = params['con'].toLowerCase();
    //不能同时打开两个详情页
  var flag = false;
  $('#nva-tab li').each(function(){
    var href = $(this).children('a').attr('href');
    href = href.split('-');
    href.pop();
    href = href.join('_').substr(1);
    if (href==prefix)
    {
      flag=true;
      var that = this;
      bootbox.confirm({  
        buttons: {  
          confirm: {  
            label: '确认' 
          },  
          cancel: {  
            label: '查看'  
          }  
        },
        closeButton:false,
        message: "发现同类数据的查看页已经打开。\r\n点确定将关闭同类查看页。\r\n点查看将激活同类查看页。",  
        callback: function(result) {  
          if (result == true) {
            setTimeout(function(){
              $(that).children('i').trigger('click');
              var id = prefix+"-"+_id;
              //var title=tObj[0].getAttribute("data-title");
              var title = '销售计划报表';
              if (title==null || $(obj).attr("use"))
              {
                title = $(obj).attr('data-title');
              }
              if ('undefined' == typeof title)
              {
                title = id;
              }
              url+="&id="+_id;

              new_tab(id,title,url);
            }, 0);
          }
          else if (result==false)
          {
            $(that).children('a').trigger("click");
          } 
        },  
        title: "提示信息", 
      });
      return false;
    }
  });
  if (!flag)
  {
    var id = prefix+"-"+_id;
    //var title=tObj[0].getAttribute("data-title");
    var title='销售计划报表';
    if (title==null || $(obj).attr("use"))
    {
      title = $(obj).attr('data-title');
    }
    if ('undefined' == typeof title)
    {
      title = id;
    }
    url+="&id="+_id;

    new_tab(id,title,url);
  }
}



//匿名回调
$import(['public/js/select2/select2.min.js',"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=SalePlan&act=search');//设定刷新的初始url
	util.setItem('formID','sale_plan_search_form');//设定搜索表单id
	util.setItem('listDIV','sale_plan_search_list');//设定列表数据容器id
    var info_form_id = 'sale_plan_search_form';//form表单id
	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
            //下拉美化 需要引入"public/js/select2/select2.min.js"
          $('#'+info_form_id+' select').select2({
              placeholder: "请选择",
              allowClear: true,
//                minimumInputLength: 2
          }).change(function(e){
              $(this).valid();
          });
          $("#add").click(function(){
              //var export_time_start=$("input[name=export_time_start]").val();
              //var bed=$("input[name='dep[]']").val();
              //window.location.href="index.php?mod=report&con=SalePlan&act=add&export_time_start="+export_time_start;
            //location.href="/index.php?mod=report&con=SalePlan&act=add";
                   /* $.ajax({
                      type: 'POST',
                      url: 'index.php?mod=report&con=SalePlan&act=add',
                      data: {'export_time_start':export_time_start},
                      dataType: 'json',
                      success: function (res) {
                        alert('成功了');
                      },
                      error:function(res){
                        alert('Ajax出错!');
                      }
                  });*/
          });
         /* 体验店类型：
         $('#'+info_form_id+' select[name="dep_type"]').change(function(e){
                $(this).valid();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=MonthlyExport&act=getShops',{dep_type:t_v},function(data){
                        $('#'+info_form_id+' select[name="dep[]"]').empty();
                        $('#'+info_form_id+' select[name="dep[]"]').append(data);
                    });
                } else {
                    $('#'+info_form_id+' select[name="dep[]"]').select2('val','').attr('readOnly',false).change();
                }
            });*/

         /* $('#'+info_form_id+' select[name="dep[]"]').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
                $('#'+info_form_id+' select[name="salse[]"]').empty();
                $('#'+info_form_id+' select[name="salse[]"]').append('<option value=""></option>');
                var _t = $(this).val();
                if (_t) {
                    $.post('index.php?mod=report&con=MonthlyExport&act=getCreateuser', {department: _t}, function(data) {
                        $('#'+info_form_id+' select[name="salse[]"]').append(data.content);
                        $('#'+info_form_id+' select[name="salse[]"]').change();
                    });
                }else{
                    $('#'+info_form_id+' select[name="salse[]"]').change();
                }
          }); */
          //时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
          if ($.datepicker) {
              $('.date-picker').datepicker({
                  format: 'yyyy-mm',
                  rtl: App.isRTL(),
                  autoclose: true
              });
              $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
          }

          $('#'+info_form_id+' :reset').on('click',function(){

                //单选按钮组重置
//              $("#"+info_form_id+" input[name='xx'][value='"+xx+"']").attr('checked','checked');
//              var test = $("#"+info_form_id+" input[name='xx']:not(.toggle, .star, .make-switch)");
//              if (test.size() > 0) {
//                  test.each(function () {
//                      if ($(this).parents(".checker").size() == 0) {
//                          $(this).show();
//                          $(this).uniform();
//                      }
//                  });
//              }

                //复选按钮重置
//              if (xxx)
//              {
//                  $("#"+info_form_id+" input[name='xxx']").attr('checked',true);
//              }
//              else
//              {
//                  $("#"+info_form_id+" input[name='xxx']").attr('checked',false);
//              }
//
//              var test = $("#"+info_form_id+" input[name='xxx']:not(.toggle, .make-switch)");
//              if (test.size() > 0) {
//                  test.each(function () {
//                      if($(this).attr('checked')=='checked')
//                      {
//                          $(this).parent().addClass('checked');
//                      }
//                      else
//                      {
//                          $(this).parent().removeClass('checked');
//                      }
//                  });
//              }
                //下拉置空
              //$('#'+info_form_id+' select[name="dep_type"]').select2('val','').change();//single
              $('#'+info_form_id+' select[name="dep[]"]').select2('val',[]).change();//multiple
              //$('#'+info_form_id+' select[name="salse[]"]').select2('val',[]).change();//multiple
              //$('#'+info_form_id+' select[name="export_type"]').select2('val','').change();//single
            });     
        };
		
		var handleForm = function(){
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			monthly_export_search_page(util.getItem("orl"));
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				//initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

