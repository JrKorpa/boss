//分页
function diamond_price_search_page(url) {
    util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js",'public/js/jquery.validate.extends.js'], function() {
    util.setItem('orl', 'index.php?mod=diamond&con=DiamondPrice&act=search');//设定刷新的初始url
    util.setItem('formID', 'diamond_price_search_form');//设定搜索表单id
    util.setItem('listDIV', 'diamond_price_search_list');//设定列表数据容器id
    
    var version = '<%$view->getLastId()%>';

    //匿名函数+闭包
    var obj = function() {

        var initElements = function() {
            //初始化下拉组件
            $('#diamond_price_search_form select').select2({
                placeholder: "全部",
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
            });//validator与select2冲突的解决方案是加change事件
            //重置按钮效果
            $('#diamond_price_search_form :reset').on('click',function(){
                $('#diamond_price_search_form select[name="shape"]').select2('val','').change();
                $('#diamond_price_search_form select[name="clarity"]').select2('val','').change();
                $('#diamond_price_search_form select[name="color"]').select2('val','').change();
                $('#diamond_price_search_form select[name="version"]').select2('val',version).change();
            });
            
        };

        var handleForm = function() {
            


            $('#diamond_price_search_form').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					price_start: {
						isFloat:true
					},
					price_end: {
						isFloat:true
					},
					min: {
						isFloat:true
					},
					max: {
						isFloat:true
					},
                },
                messages: {
					price_start: {
						isFloat:"输入价格区间最大值和最小值只能填写正数."
					},
					price_end: {
						isFloat:"输入价格区间最大值和最小值只能填写正数."
					},
					min: {
						isFloat:"输入重量区间最大值和最小值只能填写正数."
					},
					max: {
						isFloat:"输入重量区间最大值和最小值只能填写正数."
					},
                },
                highlight: function(element) { // hightlight error inputs
                    $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    if(element.parents('.form-group').find('.help-block').length <= 0)
                        error.insertAfter(element.parent());
                },
            });
            //回车提交
            $('#diamond_price_search_form input').keypress(function(e) {
                if (e.which == 13) {
                    $('#diamond_price_search_form').validate().form()
                }
            });
			util.search();
        };

        var initData = function() {
            util.closeForm(util.getItem("formID"));
            diamond_price_search_page(util.getItem("orl"));
        }
        return {
            init: function() {
                initElements();//处理搜索表单元素和重置
                handleForm();//处理表单验证和提交
                initData();//处理默认数据
            }
        }
    }();

    obj.init();
});

function downloadDiamond() {
    var shape = $("#diamond_price_search_form select[name='shape']").val();
    var clarity = $("#diamond_price_search_form select[name='clarity']").val();
    var color = $("#diamond_price_search_form select[name='color']").val();
    var version = $("#diamond_price_search_form select[name='version']").val();
    var min = $("#diamond_price_search_form input[name='min']").val();
    var max = $("#diamond_price_search_form input[name='max']").val();
    var price_start = $("#diamond_price_search_form input[name='price_start']").val();
    var price_end = $("#diamond_price_search_form input[name='price_end']").val();
    var args = "&target=1&shape="+shape+"&clarity="+clarity+"&color="+color+"&min="+min+"&max="+max+"price_start="+price_start+"&price_end="+price_end+"&version="+version;
    location.href = "index.php?mod=diamond&con=DiamondPrice&act=downLoad"+args;
}


function downloadDemo(){
    location.href = "index.php?mod=diamond&con=DiamondPrice&act=downLoad&target=demo";
}