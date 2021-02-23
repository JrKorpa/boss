function app_processor_info_search_page(url) {
    util.page(url);
}

$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/select2/select2.min.js"], function() {
    util.setItem('orl', 'index.php?mod=processor&con=AppProcessorInfo&act=search');
    util.setItem('formID', 'app_processor_info_search_form');
    util.setItem('listDIV', 'app_processor_info_search_list');
    var ControlObj = function() {
        var initElements = function(){
            //下拉列表美化
            $('#app_processor_info_search_form select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            //时间控件
            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
            }
        }

        var handleForm = function() {
            util.search();
        }

        var initData = function() {
            util.closeForm(util.getItem("formID"));
            app_processor_info_search_page(util.getItem("orl"));
            $('#app_processor_info_search_form :reset').on('click',function(){
                $('#app_processor_info_search_form select[name="business_scope"]').select2("val","");
                $('#app_processor_info_search_form select[name="status"]').select2("val","");
                $('#app_processor_info_search_form select[name="name"]').select2("val","");
                $('#app_processor_info_search_form select[name="opra_uname"]').select2("val","");;
            });
        }

        return {
            init: function() {
                initElements();
                handleForm();
                initData();
            }
        }
    }();

    ControlObj.init();
});