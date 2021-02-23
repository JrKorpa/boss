$import(["public/js/bootstrap-datepicker/js/bootstrap-datepicker.js"], function () {
    util.setItem('orl', 'index.php?mod=report&con=ShopSaleRate&act=search');
    util.setItem('formID', 'shopsalerate_report_form');
    util.setItem('listDIV', 'shopsalerate_report_list');

    var initElements = function () {
        $('.date-picker').datepicker({
            format: "yyyy-mm",
            minViewMode: 'months', // or 1, 月选择
            startView: 'decade' // or 2, 10年选择
        }).on('changeMonth', function(e) {
            $(e.currentTarget).data('datepicker').hide();
        });
        $('body').removeClass("modal-open");
    };
    initElements();
    util.search();
});
//导出
function downloadShopSaleRate() {
    var args = $("#shopsalerate_report_form").serialize();
    url = "index.php?mod=report&con=ShopSaleRate&act=downloads&" + args;
    window.open(url);
}