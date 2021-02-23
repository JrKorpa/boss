//分页
function app_bespoke_old_search_page(url) {
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js",
	"public/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js", "public/js/bootstrap-datepicker/js/bootstrap-datepicker.js"
], function() {
	util.setItem('orl', 'index.php?mod=bespoke&con=AppBespokeOld&act=search');
	util.setItem('formID', 'app_bespoke_old_search_form'); //设定搜索表单id
	util.setItem('listDIV', 'app_bespoke_old_search_list'); //设定列表数据容器id

	var baseMemberInfoObj = function() {
		var initElements = function() {
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
			// 复选框
			//复选框组美化js，
			var test = $("#app_bespoke_old_search_form input[type='checkbox']:not(.toggle, .make-switch)");
			if (test.size() > 0) {
				test.each(function() {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}

            $('#app_bespoke_old_search_form :reset').on('click',function(){
                $('#app_bespoke_old_search_form :checkbox').each(function(){
                    $(this).parent().removeClass("checked");
                })
            })
		};
		var handleForm = function() {
			util.search();
		};
		var initData = function() {
			util.closeForm(util.getItem("formID"));
			app_bespoke_old_search_page(util.getItem('orl'));
		};
		return {
			init: function() {
				initElements();
				handleForm();
				//initData();
			}
		}
	}();
	baseMemberInfoObj.init();
});

function downLoad() {
	var formdata = $("#app_bespoke_old_search_form").serialize();
    if(!$("input[name='bespoke_sn']").val() && !$("input[name='bespoke_man']").val() && !$("input[name='mobile']").val()){
        if (!$("input[name='start_add_time']").val() || !$("input[name='end_add_time']").val()) {
            alert("请输入开始时间和结束时间");
            return false;
        }
    }
	var date1 = new Date($("input[name='start_add_time']").val()); //开始时间
	var date2 = new Date($("input[name='end_add_time']").val()); //结束时间
	var date3 = date2.getTime() - date1.getTime(); //时间差的毫秒数
	var days = Math.floor(date3 / (24 * 3600 * 1000)); //计算出相差天数
	if (days > 31) {
		alert("时间不能超过31天");
		return false;
	}
	location.href = "index.php?mod=bespoke&con=AppBespokeOld&act=downLoad&" + formdata;
}