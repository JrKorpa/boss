$import("public/js/select2/select2.min.js", function() {
    var rel_style_stone_info_id = '<%$view->get_id()%>';
    var rel_style_stone_info_stone_position = '<%$view->get_stone_position()%>';
    var rel_style_stone_info_stone_cat = '<%$view->get_stone_cat()%>';

    var obj = function() {
        var initElements = function() {
            //初始化下拉列表按钮组
            $('#rel_style_stone_info select[name="stone_position"]').select2({
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
                if(rel_style_stone_info_stone_position == $('#rel_style_stone_info select[name="stone_position"]').val()){
                    rel_style_stone_info_stone_position =rel_style_stone_info_stone_position;
                }else{
                    rel_style_stone_info_stone_position = $('#rel_style_stone_info select[name="stone_position"]').val();
                }
                var _position = rel_style_stone_info_stone_position;
                if(rel_style_stone_info_stone_cat == $('#rel_style_stone_info select[name="stone_cat"]').val()){
                    rel_style_stone_info_stone_cat =rel_style_stone_info_stone_cat
                }else{
                    rel_style_stone_info_stone_cat = $('#rel_style_stone_info select[name="stone_cat"]').val();
                }
                var _cat = rel_style_stone_info_stone_cat;
                if (_position != '' && _cat != '') {
                    $.post('index.php?mod=style&con=RelStyleStone&act=getAttrList', {'position': _position,'cat':_cat,'id':rel_style_stone_info_id}, function(data) {
                        $("#show_div").empty();
                        $('#show_div').append(data);
                    });
                }
            });//validator与select2冲突的解决方案是加change事件

            $('#rel_style_stone_info select[name="stone_cat"]').select2({
                allowClear: true,
            }).change(function(e) {
                $(this).valid();
                if(rel_style_stone_info_stone_position == $('#rel_style_stone_info select[name="stone_position"]').val()){
                    rel_style_stone_info_stone_position =rel_style_stone_info_stone_position
                }else{
                    rel_style_stone_info_stone_position = $('#rel_style_stone_info select[name="stone_position"]').val();
                }
                var _position = rel_style_stone_info_stone_position;
                if(rel_style_stone_info_stone_cat == $('#rel_style_stone_info select[name="stone_cat"]').val()){
                    rel_style_stone_info_stone_cat =rel_style_stone_info_stone_cat
                }else{
                    rel_style_stone_info_stone_cat = $('#rel_style_stone_info select[name="stone_cat"]').val();
                }
                var _cat = rel_style_stone_info_stone_cat;
                if (_position != '' && _cat != '') {
                    $.post('index.php?mod=style&con=RelStyleStone&act=getAttrList', {'position': _position,'cat':_cat,'id':rel_style_stone_info_id}, function(data) {
                        $("#show_div").empty();
                        $('#show_div').append(data);
                    });
                }

            });//validator与select2冲突的解决方案是加change事件
            
            
            
        };

        //表单验证和提交
        var handleForm = function() {
            var url = rel_style_stone_info_id ? 'index.php?mod=style&con=RelStyleStone&act=update' : 'index.php?mod=style&con=RelStyleStone&act=insert';
            var options1 = {
                url: url,
                error: function()
                {
                    alert('请求超时，请检查链接');
                },
                beforeSubmit: function(frm, jq, op) {
                    $('body').modalmanager('loading');//进度条和遮罩
                },
                success: function(data) {
                    if (data.success == 1) {
                        $('.modal-scrollable').trigger('click');//关闭遮罩
                        alert(rel_style_stone_info_id ? "修改成功!" : "添加成功!");
                        util.retrieveReload();
                        if (data.tab_id)
                        {
                            util.syncTab(data.tab_id);
                        }
                    } else {
                        $('body').modalmanager('removeLoading');//关闭进度条
                        alert(data.error ? data.error : (data ? data : '程序异常'));
                    }
                },
                error:function() {
                    $('.modal-scrollable').trigger('click');
                    alert("数据加载失败");
                }
            };

            $('#rel_style_stone_info').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                },
                messages: {
                },
                highlight: function(element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    //$(element).focus();
                },
                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-control'));
                },
                submitHandler: function(form) {
                    $("#rel_style_stone_info").ajaxSubmit(options1);
                }
            });
            //回车提交
            $('#rel_style_stone_info input').keypress(function(e) {
                if (e.which == 13) {
                    $('#rel_style_stone_info').validate().form();
                }
            });
        };
        var initData = function() {
            //下拉组件重置
            $('#rel_style_stone_info :reset').on('click', function() {
                $('#rel_style_stone_info select[name="stone_position"]').select2("val", rel_style_stone_info_stone_position).change();
                $('#rel_style_stone_info select[name="stone_cat"]').select2("val", rel_style_stone_info_stone_cat).change();

            })
            if (rel_style_stone_info_id)
            {//修改
                $('#rel_style_stone_info :reset').click();
            }
        };
        return {
            init: function() {
                initElements();//处理表单元素
                handleForm();//处理表单验证和提交
                initData();//处理表单重置和其他特殊情况
            }
        }
    }();
    obj.init();
});