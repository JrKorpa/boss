//分页
function diamond_info_search_page(url){
	util.page(url);
}

//匿名回调
$import(["public/js/select2/select2.min.js",'public/js/jquery.validate.extends.js'],function(){
	util.setItem('orl','index.php?mod=diamond&con=DiamondInfo&act=search');//设定刷新的初始url
	util.setItem('formID','diamond_info_search_form');//设定搜索表单id
	util.setItem('listDIV','diamond_info_search_list');//设定列表数据容器id

	//匿名函数+闭包
	var obj = function(){
		
		var initElements = function(){
			//单选按钮
			var test = $("#diamond_info_search_form input[type='checkbox']:not(.toggle, .star, .make-switch)");
			if (test.size() > 0) {
				test.each(function () {
					if ($(this).parents(".checker").size() == 0) {
						$(this).show();
						$(this).uniform();
					}
				});
			}
			//下拉组件
			//初始化下拉组件
			$('#diamond_info_search_form select').select2({
				placeholder: "全部",
				allowClear: true,
			}).change(function(e){
				$(this).valid();
			});//validator与select2冲突的解决方案是加change事件
			$('#diamond_info_search_form :reset').on('click',function(){
				$("#diamond_info_search_form :checkbox").each(function(){
				   $(this).parent().removeClass('active');
				}); 
			})
		};
		
		var handleForm = function(){
            $('#diamond_info_search_form').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					carat_min: {
						isFloat:true
					},
					carat_max: {
						isFloat:true
					},
					kelan_price_min: {
						isFloat:true
					},
					kelan_price_max: {
						isFloat:true
					},
                },
                messages: {
					carat_min: {
						isFloat:"石重最大值和最小值只能填写正数."
					},
					carat_max: {
						isFloat:"石重最大值和最小值只能填写正数."
					},
					kelan_price_min: {
						isFloat:"BDD价区间最大值和最小值只能填写正数."
					},
					kelan_price_max: {
						isFloat:"BDD价区间最大值和最小值只能填写正数."
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
                        error.insertAfter(element.closest('.form-control').parent());
                },
            });
            //回车提交
            $('#diamond_info_search_form input').keypress(function(e) {
                if (e.which == 13) {
                    $('#diamond_info_search_form').validate().form()
                }
            });
			util.search();
		};
		
		var initData = function(){
			util.closeForm(util.getItem("formID"));
			diamond_info_search_page(util.getItem("orl"));//下拉组件重置
			$('#diamond_info_search_form :reset').on('click',function(){
				$('#diamond_info_search_form select[name="warehouse"]').select2("val",'');
				$('#diamond_info_search_form select[name="from_ad"]').select2("val",'');
				$('#diamond_info_search_form select[name="good_type"]').select2("val",'');
				$('#diamond_info_search_form select[name="status"]').select2("val",'');
				$('#diamond_info_search_form input[name="gm"]').parent().removeClass('checked');
				$('#diamond_info_search_form input[name="ysyd"]').parent().removeClass('checked');
				
			});
		}
		return {
			init:function(){
				initElements();//处理搜索表单元素和重置
				handleForm();//处理表单验证和提交
				initData();//处理默认数据
			}
		}	
	}();

	obj.init();
});

function downloads() {
	var formdata = $("#diamond_info_search_form").serialize();
    location.href = "index.php?mod=diamond&con=DiamondInfo&act=downLoad&"+formdata;
}

//婚博会数据下载专用
function hbdown()
{
	var formdata = $("#diamond_info_search_form").serialize();
    location.href = "index.php?mod=diamond&con=DiamondInfo&act=hbdown&"+formdata;
}