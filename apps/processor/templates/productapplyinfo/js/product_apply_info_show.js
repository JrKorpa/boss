$import(function(){
    var apply_status = '<%$view->get_apply_status()%>';
    var refuse_remark = '<%$view->get_refuse_remark()%>';

    var obj = function(){

        var initElements = function(){
            if(apply_status == '0'){
                var remark_table = '<tr><td colspan="8"><label class="control-label">拒绝原因：</label><textarea id="product_apply_info_remark" style="border-color: #999999;" class="form-control" name="refuse_remark" rows="2"></textarea></td></tr>';
                $('#product_apply_show_info_table').append(remark_table);
            }else if(apply_status == '2'){
                var remark_table = '<tr><td colspan="8"><label class="control-label">拒绝原因：</label><textarea style="border-color: #999999;" class="form-control" disabled name="refuse_remark" rows="2">'+refuse_remark+'</textarea></td></tr>';
                $('#product_apply_show_info_table').append(remark_table);
            }
        }
        return {
            init:function(){
                initElements();
            }
        }
    }();
    obj.init();
});

function product_applyinfo_checkout(){
    var remark = $('#product_apply_info_remark').val();
    var id = '<%$view->get_id()%>';

    if(remark == ''){
        util.xalert('请填写拒绝原因!!!');
    }else{
        var url = "index.php?mod=processor&con=ProductApplyInfo&act=checkOut";
        $.post(url,{'remark':remark,'id':id},function(e){
            if(e == '0'){
                util.xalert('操作失败!!!');
            }else if(e == '1'){
                util.xalert('操作成功!!!');
            }else{
                util.xalert(e);
            }
            util.retrieveReload();
        });
    }



}