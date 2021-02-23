//分页
function monthly_export_search_page(url){
	util.page(url);
}

//匿名回调
$import(['public/js/select2/select2.min.js',"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"],function(){
	util.setItem('orl','index.php?mod=report&con=MonthlyExport&act=search');//设定刷新的初始url
	util.setItem('formID','monthly_export_search_form');//设定搜索表单id
	util.setItem('listDIV','monthly_export_search_list');//设定列表数据容器id
    var info_form_id = 'monthly_export_search_form';//form表单id
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
            });

          $('#'+info_form_id+' select[name="dep[]"]').select2({
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
            }); 
          //时间选择器 需要引入"public/js/bootstrap-datepicker/js/bootstrap-datepicker.js","public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
          if ($.datepicker) {
              $('.date-picker').datepicker({
                  format: 'yyyy-mm-dd',
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
              $('#'+info_form_id+' select[name="dep_type"]').select2('val','').change();//single
              $('#'+info_form_id+' select[name="dep[]"]').select2('val',[]).change();//multiple
              $('#'+info_form_id+' select[name="salse[]"]').select2('val',[]).change();//multiple
              $('#'+info_form_id+' select[name="export_type"]').select2('val','').change();//single
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

function downloads(){
    var down_infos = 'downs';
    var dep_type = $("#monthly_export_search_form [name='dep_type']").val();
    var export_time_start = $("#monthly_export_search_form [name='export_time_start']").val();
    var export_time_end = $("#monthly_export_search_form [name='export_time_end']").val();
    var dep = $("#monthly_export_search_form [name='dep[]']").val();
    var salse = $("#monthly_export_search_form [name='salse[]']").val();
    var export_type = $("#monthly_export_search_form [name='export_type']").val();
    var param = "&down_infos="+down_infos+"&dep_type="+dep_type+"&export_time_start="+export_time_start+"&export_time_end="+export_time_end+"&dep="+dep+"&salse="+salse+"&export_type="+export_type;
    url = "index.php?mod=report&con=MonthlyExport&act=search"+param;
    window.open(url);
}