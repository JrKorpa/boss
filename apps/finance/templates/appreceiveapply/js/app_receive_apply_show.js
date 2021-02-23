//申请单驳回
function reCon()
{
    var nowUserId = '<%$smarty.session.userName%>';
    var makeUserId = '<%$view->get_make_name()%>';
    if(nowUserId == makeUserId){
        util.xalert('自己不能驳回自己单子');
       	return false;
    }
    var status = '<%$view->get_status()%>';
    if(status==1){
        util.xalert('不能驳回未提交的单据');
        return false;
    }
    if ($("#rule_th_cancel").css("display") == "none")
    {
        $("#rule_th_cancel").css("display", "");
        $('#app_receive_apply_show_list tr').each(function() {
            $(this).find("td").eq(21).html("<input type='text' class='form-control' name='overrule_reason[]' id='" + $(this).find("td").eq(0).html() + "'/>");
            $(this).find("td").eq(21).css("display", "")
        });
        util.xalert('请填写驳回原因');
    } else {
        $i = 0;
        $j = 0;
        var ids = new Array();
        var reasons = new Array();
        $("input[name='overrule_reason[]']").each(function(i, o) {
            if ($(o).val() != '')
            {
                if ($(o).val() != '' && no_tsStr($(o).val()))
                {
                    $j = 1;
                    return false;
                }
                $i++;
                ids.push($(o).attr('id'))
                reasons.push($(o).val())
            }
        });
        if ($j)
        {
            util.xalert("驳回原因不能包括特殊字符");
            return false;
        }
        ids = ids.join();
        reasons = reasons.join("#");
        if ($i == 0)
        {
            util.xalert('驳回原因至少填写一项');
        } else {
            var url = 'index.php?mod=finance&con=AppReceiveApply&act=reCon';
            var id = '<%$view->get_id()%>';
            $.post(url, {id: id , ids:ids, reasons:reasons}, function(res) {
                if (!res.success)
                {
                    util.xalert(res.error);
                } else {
                    util.xalert("已经驳回");
                    util.retrieveReload(obj);
                }
            });
        }
    }
}


//不包含*,$,#特殊字符 还有什么特殊字符需要加进去就行
function no_tsStr(str)
{
	if(str.replace(/^[^*$#]+$/,''))	
	{
		return true;
	}
	return false;
}