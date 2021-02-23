$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js",
    "public/js/select2/select2.min.js","public/js/fancyapps-fancyBox/jquery.fancybox.css"],function(){
    util.setItem('orl','index.php?mod=report&con=BespokeOrderDetail&act=search');
    util.setItem('formID','bespoke_order_detail_form');
    util.setItem('listDIV','bespoke_order_detail_list');

    var BespokeOrderDetailObj = function(){
        var initElements=function(){
            $('#bespoke_order_detail_form select').select2({
                placeholder: "请选择",
                allowClear: true
            }).change(function(e){
                $(this).valid();
            });

            $('#bespoke_order_detail_form select[name="shop_type"]').change(function(e){
                $(this).valid();
                $('#bespoke_order_detail_form select[name="shop_id"]').empty();
                var t_v = $(this).val();
                if(t_v){
                    $.post('index.php?mod=report&con=common&act=getShops',{shop_type:t_v},function(data){
                        $('#bespoke_order_detail_form select[name="shop_id"]').empty();
                        $('#bespoke_order_detail_form select[name="shop_id"]').append(data);
                    });
                }
                else
                {
                    $('#bespoke_order_detail_form select[name="shop_id"]').select2('val','').attr('readOnly',false).change();
                }
            });

            if ($.datepicker) {
                $('.date-picker').datepicker({
                    format: 'yyyy-mm-dd',
                    rtl: App.isRTL(),
                    autoclose: true,
                    clearBtn: true
                });
                $('body').removeClass("modal-open");
            }

            $('#bespoke_order_detail_form :reset').on('click',function(){
                $('#bespoke_order_detail_form select').select2("val","");
            })
        };
        var handleForm=function(){
            util.search();
        };
        var initData=function(){
            util.closeForm(util.getItem("formID"));
            shopcount_search_page(util.getItem("orl"));
        };

        return {
            init:function(){
                initElements();
                handleForm();
                //initData();
            }
        }
    }();
    BespokeOrderDetailObj.init();
});

//导出
function downloadBespokeOrderDetail(){
    var args=$("#bespoke_order_detail_form").serialize();
    url= "index.php?mod=report&con=BespokeOrderDetail&act=downloads&"+args;
    window.open(url);
}
